<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


/**
 * MetaTags loader.
 * 
 * Load meta tags from default values or database
 */
class MetaTagsLoader
{
    protected $defaultMetatags;
    protected $em;
    
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
        $this->defaultMetatags   = $config['defaults'];
        $this->em                = $em;
    }
    
    /**
     * Load meta tags depending on the url
     * 
     * @param Request $request
     */
    public function getMetaTags(Request $request)
    {
        $metatags = $this->defaultMetatags;
        
        return $metatags;
    }
}











