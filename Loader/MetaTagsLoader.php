<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Copiaincolla\MetaTagsBundle\Service\Defaults;
use Copiaincolla\MetaTagsBundle\Entity\Metatag;

/**
 * MetaTags loader.
 *
 * Load meta tags from default values or database
 */
class MetaTagsLoader
{
    protected $defaults;
    protected $em;

    /**
     * constructor
     *
     * @param array $config
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Defaults $defaults)
    {
        $this->em       = $em;
        $this->defaults = $defaults;
    }

    /**
     * Load the right meta tags for a Request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $inlineMetatags array of override metatags
     * @return array
     */
    public function getMetaTagsForRequest(Request $request, $inlineMetatags = array())
    {
        $pathInfo = $request->getPathInfo();

        // search MetaTag in database
        $metaTag = $this->em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->findOneBy(array(
            'url' => $pathInfo
        ));

        return $this->mergeWithDefaults($pathInfo, $metaTag, $inlineMetatags);
    }

    /**
     * merges the values in $metaTag object with the default ones
     *
     * @param \Copiaincolla\MetaTagsBundle\Entity\Metatag $metaTagEntity
     * @param $inlineMetatags array of override metatags
     * @return array
     */
    private function mergeWithDefaults($pathInfo, $metaTagEntity, $inlineMetatags)
    {
        $metaTags = array();
        
        // get default meta tags matching by regex
        $defaultMetaTags = $this->defaults->getEntityMetatagDefaults($pathInfo);

        // return backend metatags if defined, otherwise try to return inline metatags
        foreach (Metatag::getSupportedMetaTags() as $name) {
            if ($metaTagEntity !== null && (string)$metaTagEntity->getValue($name)) {
                $metaTags[$name] = $this->cleanMetaTagValue($metaTagEntity->getValue($name));
            } elseif (array_key_exists($name, $inlineMetatags)) {
                $metaTags[$name] = $this->cleanMetaTagValue($inlineMetatags[$name]);
            } else {

                foreach ($defaultMetaTags as $e) {
                    $defaultMetaTag = $e->toArray();
                    if (array_key_exists($name, $defaultMetaTag) && trim($defaultMetaTag[$name]) != '') {
                        $metaTags[$name] = $defaultMetaTag[$name];
                        break;
                    }
                }

            }
        }

        return $metaTags;
    }

    /**
     * clean a meta tag value
     *
     * - convert the value into a string
     * - trim
     *
     * @param $str
     * @return string
     */
    private function cleanMetaTagValue($str)
    {
        return trim((string) $str);
    }
}