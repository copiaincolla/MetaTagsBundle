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
     *
     */
    public function getEntityMetatagDefaults()
    {
        $entity = $this->em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->findOneBy(array());

        if (!$entity) {
            $entity = new MetatagDefaults();
        }

        return $entity;
    }
}
