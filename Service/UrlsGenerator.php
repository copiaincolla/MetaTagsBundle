<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Route;
use Copiaincolla\MetaTagsBundle\Lib\CartesianProduct;

/**
 * Generate urls starting from routes
 */
class UrlsGenerator
{
    protected $config;
    protected $router;

    /**
     * @param array $config
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct(array $config = array(), Router $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * Generate urls for the routes loaded but not included under the 'urls_loader.parameters.dynamic_routes' key
     *
     * @param $routeKey
     * @param $route
     * @return array
     */
    public function generateUrls($routeKey, Route $route)
    {
        // array of fixed_params for all parametric routes. can be empty
        $parametricRoutesFixedParams = $this->config['urls_loader']['parameters']['fixed_params'];

        $fixedParamsCartesian = CartesianProduct::cartesian($parametricRoutesFixedParams);

        $urls = array();

        if (count($fixedParamsCartesian) > 0) {
            foreach ($fixedParamsCartesian as $fixedParam) {
                $urls[] = $this->generateUrl($routeKey, $route, $fixedParam);
            }
        } else {
            $urls[] = $this->generateUrl($routeKey, $route);
        }

        return array_filter($urls, function($e) {
            return $e != null;
        });
    }

    /**
     * Generate a single url
     * Manage the url generation exceptions
     *
     * Always call this function to generate a url to be loaded in the bundle
     *
     * $routeParameters could contain extra parameters: only required parameters for a route are used
     *
     * @param string $routeKey route name
     * @param Route $route Route object
     * @param array $defaultVariables array of [route variable] => [value]
     */
    public function generateUrl($routeKey, Route $route, array $routeParameters = array())
    {
        $compiledRoute = $route->compile();

        $routeVariables = $compiledRoute->getVariables();

        $routeParameters = array_intersect_key($routeParameters, array_flip($routeVariables));


        // try to generate the route
        try {
            return $this->router->generate($routeKey, $routeParameters);

            // MissingMandatoryParametersException
        } catch (\Exception $e) {
            // no exception is thrown, the url is simply not added to the list
        }

        return null;
    }

}
