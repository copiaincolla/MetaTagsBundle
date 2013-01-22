<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Route;

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
     * Generate urls starting from a Route object and an array of parameters
     *
     * Apply the parametric_routes.default_params
     *
     * @param $routeKey
     * @param $route
     * @param array $routeParameters
     * @return array
     */
    public function generateUrls($routeKey, Route $route, array $routeParameters = array())
    {
        $parametricRoutesDefaultParams = $this->config['parametric_routes']['default_params'];

        $urls = array();

        if (count($parametricRoutesDefaultParams) > 0) {

            if (is_array($parametricRoutesDefaultParams)) {
                $parametricRoutesDefaultParams = array($parametricRoutesDefaultParams);
            }

            foreach ($parametricRoutesDefaultParams as $k => $defaultParams) {

                if (!is_array($defaultParams)) {
                    $defaultParams = array($defaultParams);
                }

                foreach ($defaultParams as $p => $defaultParam) {
                    $rp = $routeParameters + array($k => $defaultParam);
                    $urls[] = $this->generateUrl($routeKey, $route, $rp);
                }

            }
        } else {
            $urls[] = $this->generateUrl($routeKey, $route, $routeParameters);
        }

        return array_filter(array_unique($urls), function($e) {
            return $e != null;
        });
    }

    /**
     * Generate a single url
     *
     * $routeParameters could contain extra parameters: only required parameters for a route are used
     *
     * @param string $routeKey route name
     * @param Route $route Route object
     * @param array $defaultVariables array of [route variable] => [value]
     */
    private function generateUrl($routeKey, Route $route, array $routeParameters = array())
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
