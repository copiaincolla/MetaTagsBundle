<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * MetaTag controller
 */
class MetaTagsController extends Controller
{
    /**
     * Render HTML metatags
     *
     * @Template()
     */
    public function renderAction($vars = array())
    {
        $metatagsLoader = $this->container->get('ci_metatags.metatags_loader');

        return array (
            'metatags'  => $metatagsLoader->getMetaTagsForRequest($this->getRequest()),
            'vars'      => $vars
        );
    }

}
