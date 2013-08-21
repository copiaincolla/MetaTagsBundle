<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Copiaincolla\MetaTagsBundle\Entity\MetatagDefaults;

/*
 * Manage default values
 */
class Defaults
{
    protected $config;
    protected $em;

    /**
     * @param $config
     * @param $em
     * @param UrlsGenerator $urlsGenerator
     */
    public function __construct($config, $em)
    {
        $this->config = $config;
        $this->em = $em;
    }

    /**
     * Get (if exesting) in order of importance, MetatagDefaults entities whose path regex is matching $path
     */
    public function getEntityMetatagDefaults($path)
    {
        return $this->em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->getMatching($path);
    }
}