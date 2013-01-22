<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Component\Routing\Route;

class MetaTagsRouteExposer
{
    protected $config;

    protected $container;

    protected $loadedBundlesRegex;

    public function __construct(array $config = array(), $container)
    {
        $this->config = $config;

        $this->container = $container;

        $this->loadedBundlesRegex = $this->getLoadedBundlesRegex();
    }

    /**
     * generate regex string to filter the controller of a route
     *
     * the regex is an OR concatenation of the bundles' namespaces set by the user in config.yml
     *
     * eg: (^Acme\\FooBundle)|(^Acme)
     */
    private function getLoadedBundlesRegex()
    {
        $regex = '';

        foreach ($this->container->get('kernel')->getBundles() as $bundle) {

            if (in_array($bundle->getName(), $this->config['exposed_routes']['bundles'])) {

                if ($regex != '') {
                    $regex .= '|';
                }

                $regex .= "(^".addslashes($bundle->getNamespace()).")";
            }
        }

        return $regex;
    }

    /**
     * check if the Route $route is exposed
     *
     * @param $route
     * @return Boolean
     */
    public function isRouteExposed(Route $route)
    {
        $routeDefaults = $route->getDefaults();

        if (!isset($routeDefaults['_controller'])) {
            return false;
        }

        $_controller = $routeDefaults['_controller'];

        // check if an "expose" parameter is set in the controller
        $routeExposedByRouteOption = $this->getRouteExposedByRouteOption($route);
        if (null !== $routeExposedByRouteOption) {
            return $routeExposedByRouteOption;
        }

        // check if the bundles is set in the configurations file
        if ($this->isRouteExposedByBundle($_controller)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a route is exposed by including a bundle name under the 'exposed_routes.bundles' key in config.yml
     *
     * @param $_controller
     * @return bool
     */
    private function isRouteExposedByBundle($_controller)
    {
        if ($this->loadedBundlesRegex != "" && preg_match($this->loadedBundlesRegex, $_controller)) {
            return true;
        }

        return false;
    }

    /**
     * check if a route must be included to generate urls by checking the "options" attribute
     *
     * a route can be exposed by setting the "ci_metatags_expose" options key to true
     *
     * eg: @Route("/myaction", name="my_action", options={"ci_metatags_expose"=true})
     *
     * @param \Symfony\Component\Routing\Route $route
     * @return bool
     */
    private function getRouteExposedByRouteOption(Route $route)
    {
        $routeOptions = $route->getOptions();

        if (array_key_exists('ci_metatags_expose', $routeOptions)) {
            if (true === $routeOptions['ci_metatags_expose']) {
                return true;
            } else if (false === $routeOptions['ci_metatags_expose']) {
                return false;
            }
        }

        return null;
    }

}
