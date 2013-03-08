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

    private $config;

    public function __construct(array $config = array())
    {
        // as explained in http://twig.sensiolabs.org/doc/api.html
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());

        $this->config = $config;
    }
    
    public function getFunctions()
    {
        return array(
            'ci_metatags_parse_value' => new \Twig_Function_Method($this, 'parse_value'),
            'ci_metatags_truncate' => new \Twig_Function_Method($this, 'truncate')
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
    public function parse_value($val, $vars = array())
    {
        // add the master request to the available variables for twig rendering
        $vars['_master_request'] = Request::createFromGlobals();

        // return twig rendered meta tag value
        return $this->twig->render($val, $vars);
    }

    public function truncate($text)
    {
        $limit = $this->config['config']['truncate_function']['limit'];
        $truncateWords = $this->config['config']['truncate_function']['truncateWords'];

        $text = implode(" ", (array) $text);

        // truncate the string to $limit characters
        $output = substr($text, 0, $limit);

        // in case there are no spaces in $text, truncate even if should preserve words length
        if (!preg_match("/ /", $output)) {
            return $output;
        }

        // preserve last word if necessary
        if (!$truncateWords) {
            $output = substr($output, 0, strrpos($output, " "));
        }

        // trim
        $output = trim($output);

        return $output;
    }

    public function getName()
    {
        return 'ci_metatags.twig.extension';
    }

}