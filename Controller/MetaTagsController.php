<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

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

        $request = Request::createFromGlobals();

        return array (
            'metatags'  => $metatagsLoader->getMetaTagsForRequest($request),
            'vars'      => $vars
        );
    }

}