<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * MetaTag controller.
 *
 * @Route("/metatags")
 */
class MetaTagsController extends Controller
{
    /**
     * @Route("/", name="ci_metatags")
     * @Template()
     */
    public function indexAction()
    {
        $metatagasLoader = $this->container->get('ci_metatags.loader');
        
        $urls = $metatagasLoader->getUrls();
        
        
        return array();
    }
}
