<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Copiaincolla\MetaTagsBundle\Entity\Metatag;

/**
 * MetaTags loader.
 * 
 * Load meta tags from default values or database
 */
class MetaTagsLoader
{
    protected $defaultMetaTags;
    protected $em;

    // to load twig templates from string
    protected $twig;
    
    /**
     * List of supported metatags
     */
    private $supportedMetatags = array(
        'title',
        'description',
        'keywords',
        'author'
    );
    
    /**
     * constructor
     * 
     * @param array $config
     * @param EntityManager $em
     */
    public function __construct(array $config, EntityManager $em)
    {
        $this->defaultMetaTags  = $config['defaults'];
        $this->em               = $em;

        // as explained in http://twig.sensiolabs.org/doc/api.html
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());
    }
    
    /**
     * Load meta tags depending on the url
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $templateVars optional array of template parameters
     * @return array
     */
    public function getMetaTagsForRequest(Request $request, $templateVars = array())
    {
        $pathInfo = $request->server->get('PATH_INFO');

        $metaTags = $this->defaultMetaTags;

        $metaTag = $this->em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->findOneBy(array(
            'url' => $pathInfo
        ));

        if ($metaTag) {
            $metaTags = $this->injectMetaTag($metaTags, $metaTag, $templateVars);
        }

        return $metaTags;
    }

    /**
     * injects the values stored in a MetaTag entity into an array
     *
     * @param array $metaTags
     * @param \Copiaincolla\MetaTagsBundle\Entity\Metatag $metaTag
     * @return array
     */
    private function injectMetaTag($metaTags = array(), MetaTag $metaTag, $templateVars = array())
    {
        // title
        $title = $this->cleanMetaTagValue($metaTag->getTitle());
        if ('' !== $title) {
            $metaTags['title'] = $this->twig->render($title, $templateVars);
        }

        // description
        $description = $this->cleanMetaTagValue($metaTag->getDescription());
        if ('' !== $description) {
            $metaTags['description'] = $this->twig->render($description, $templateVars);
        }

        // keywords
        $keywords = $this->cleanMetaTagValue($metaTag->getKeywords());
        if ('' !== $keywords) {
            $metaTags['keywords'] = $this->twig->render($keywords, $templateVars);
        }

        // author
        $author = $this->cleanMetaTagValue($metaTag->getAuthor());
        if ('' !== $author) {
            $metaTags['author'] = $this->twig->render($author, $templateVars);
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
        return trim((string)$str);
    }
}
