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
     * Get (if exesting) the first, in order of importance, MetatagDefaults entity whose path regex is matching $path
     */
    public function getEntityMetatagDefaults($path)
    {
        $entity = $this->em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->getFirstMatching($path);

        if (!$entity) {
            $entity = new MetatagDefaults();
        }

        return $entity;
    }
}
