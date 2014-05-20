<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Component\Routing\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class RouteExposer
{
    protected $config;
    protected $kernel;
    protected $router;

    protected $loadedBundlesRegex;

    public function __construct(array $config = array(), $kernel, Router $router)
    {
        $this->config = $config;
        $this->kernel = $kernel;
        $this->router = $router;

        $this->loadedBundlesRegex = $this->calculateLoadedBundlesRegex();
    }

    /**
     * generate regex string to filter the controller of a route
     *
     * the regex is an OR concatenation of the bundles' namespaces set by the user in config.yml
     *
     * eg: (^Acme\\FooBundle)|(^Acme)
     */
    private function calculateLoadedBundlesRegex()
    {
        $regex = '';

        foreach ($this->kernel->getBundles() as $bundle) {

            if (in_array($bundle->getName(), $this->config['urls_loader']['exposed_routes']['bundles'])) {
                if ($regex != '') {
                    $regex .= '|';
                }
                $regex .= "(^".addslashes($bundle->getNamespace()).")";
            }
        }

        return "#".$regex."#";
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
        return ($this->loadedBundlesRegex != "" && preg_match($this->loadedBundlesRegex, $_controller));
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

    /**
     * return a Route object by matching a $url
     *
     * @param $url
     */
    public function getRouteByUrl($url)
    {
        $matchedRoute = $this->router->match($url);

        if ($matchedRoute !== null) {
            $_route = $matchedRoute['_route'];

            return $this->router->getRouteCollection()->get($_route);
        }
        return null;
    }

}
