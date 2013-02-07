<?php

namespace Copiaincolla\MetaTagsBundle\Extension;

use Symfony\Component\HttpFoundation\Request;

/**
 * Twig extension to help rendering meta tags
 */
class MetaTagsExtension extends \Twig_Extension
{
    // to load twig templates from string
    protected $twig;

    public function __construct()
    {
        // as explained in http://twig.sensiolabs.org/doc/api.html
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());
    }
    
    public function getFunctions()
    {
        return array(
            'metaTagsParseValue'   => new \Twig_Function_Method($this, 'metaTagsParseValue'),
        );
    }

    /**
     * Parse the $value string optionally containing twig syntax
     * Variables for twig are passed trough $vars array
     *
     * @param $val twig string to parse
     * @param array $vars variables needed by twig to parse $source
     * @return string parsed $string
     */
    public function metaTagsParseValue($val, $vars = array())
    {
        // add the master request to the available variables for twig rendering
        $vars['_master_request'] = Request::createFromGlobals();

        // return twig rendered meta tag value
        return $this->twig->render($val, $vars);
    }

    public function getName()
    {
        return 'ci_metatags.twig.extension';
    }

}