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
    public function renderAction($templateVars = array())
    {
        $metatagsLoader = $this->container->get('ci_metatags.metatags_loader');

        return $this->render("CopiaincollaMetaTagsBundle:MetaTags:render.html.twig", array(
            'metatags' => $metatagsLoader->getMetaTagsForRequest($this->getRequest(), $templateVars)
        ));
    }

}
