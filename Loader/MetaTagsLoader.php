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
    
    /**
     * List of supported metatags
     */
    private $supportedMetatags = array(
        'title',
        'description',
        'keywords',
        'author',
        'language',
        'robots',
        'googlebot',
        'og:title',
        'og:description',
        'og:image'
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
    }
    
    /**
     * Load the right meta tags for a Request
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function getMetaTagsForRequest(Request $request)
    {
        $pathInfo = $request->getPathInfo();

        $metaTags = $this->defaultMetaTags;

        // search MetaTag in database
        $metaTag = $this->em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->findOneBy(array(
            'url' => $pathInfo
        ));

        if ($metaTag) {
            $metaTags = $this->mergeMetaTag($metaTags, $metaTag);
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
    private function mergeMetaTag($metaTags = array(), MetaTag $metaTag)
    {
        // title
        $title = $this->cleanMetaTagValue($metaTag->getTitle());
        if ('' !== $title) {
            $metaTags['title'] = $title;
        }

        // description
        $description = $this->cleanMetaTagValue($metaTag->getDescription());
        if ('' !== $description) {
            $metaTags['description'] = $description;
        }

        // keywords
        $keywords = $this->cleanMetaTagValue($metaTag->getKeywords());
        if ('' !== $keywords) {
            $metaTags['keywords'] = $keywords;
        }

        // robots
        $robots = $this->cleanMetaTagValue($metaTag->getRobots());
        if ('' !== $robots) {
            $metaTags['robots'] = $robots;
        }

        // googlebot
        $googlebot = $this->cleanMetaTagValue($metaTag->getGooglebot());
        if ('' !== $googlebot) {
            $metaTags['googlebot'] = $googlebot;
        }

        // author
        $author = $this->cleanMetaTagValue($metaTag->getAuthor());
        if ('' !== $author) {
            $metaTags['author'] = $author;
        }

        // language
        $language = $this->cleanMetaTagValue($metaTag->getLanguage());
        if ('' !== $language) {
            $metaTags['language'] = $language;
        }

        // og:title
        $ogTitle = $this->cleanMetaTagValue($metaTag->getOgTitle());
        if ('' !== $ogTitle) {
            $metaTags['og:title'] = $ogTitle;
        }

        // og:description
        $ogDescription = $this->cleanMetaTagValue($metaTag->getOgDescription());
        if ('' !== $ogTitle) {
            $metaTags['og:description'] = $ogDescription;
        }

        // og:image
        $ogImage = $this->cleanMetaTagValue($metaTag->getOgImage());
        if ('' !== $ogImage) {
            $metaTags['og:image'] = $ogImage;
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