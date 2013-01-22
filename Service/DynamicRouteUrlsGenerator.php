<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Component\Routing\Route;
use Copiaincolla\MetaTagsBundle\Service\UrlsGenerator;

/*
 * Generate a url for a route specified under the 'parametric_routes.routes' key in bundle configuration
 */
class DynamicRouteUrlsGenerator
{
    protected $config;
    protected $em;
    protected $urlsGenerator;

    /**
     * @param $config
     * @param $em
     * @param UrlsGenerator $urlsGenerator
     */
    public function __construct($config, $em, UrlsGenerator $urlsGenerator)
    {
        $this->config = $config;
        $this->em = $em;

        $this->urlsGenerator = $urlsGenerator;
    }

    /**
     * Generate urls for a route specified under the 'parametric_routes.routes' key
     *
     * @param $routeKey
     * @param $route
     * @return array
     */
    public function generateUrls($routeKey, Route $route)
    {
        $fixedParams = $this->processFixedParams($routeKey);

        $repositoryParams = $this->processRepositoryParam($routeKey);

        $urls = array();

        // entrambi sono array
        if($fixedParams && $repositoryParams) {
            foreach ($fixedParams as $fixedParam) {
                foreach ($repositoryParams as $repositoryParam) {

                    $routeParams = $fixedParam + $repositoryParam;

                    $urls = array_merge($urls, $this->urlsGenerator->generateUrls($routeKey, $route, $routeParams));
                }
            }
        } elseif ($fixedParams) {
            foreach ($fixedParams as $fixedParam) {
                $urls = array_merge($urls, $this->urlsGenerator->generateUrls($routeKey, $route, $fixedParam));
            }
        } elseif ($repositoryParams) {
            foreach ($repositoryParams as $repositoryParam) {
                $urls = array_merge($urls, $this->urlsGenerator->generateUrls($routeKey, $route, $repositoryParam));
            }
        }

        return $urls;
    }

    /**
     * Return an array of parameters for processing the 'repository' key
     *
     * @param $routeKey
     * @return array
     */
    private function processRepositoryParam($routeKey)
    {
        // array with infos about the dynamic route lo load, as specified in config by user
        $dynamicRouteConfig = $this->config['parametric_routes']['routes'][$routeKey];

        $routeParams = array();

        if (isset($this->config['parametric_routes']['routes'][$routeKey]['repository'])) {
            $repository = $this->config['parametric_routes']['routes'][$routeKey]['repository'];

            // data fetched from database
            if (isset($this->config['parametric_routes']['routes'][$routeKey]['repository_fetch_function'])) {

                $repositoryFunction = $this->config['parametric_routes']['routes'][$routeKey]['repository_fetch_function'];

                // FIX: verificare esistenza funzione
                $data = $this->em->getRepository($repository)->$repositoryFunction();

            } else {
                $data = $this->em->getRepository($repository)->findAll();
            }

            foreach ($data as $obj) {
                $routeParams[] = $this->prepareEntityUrlsVariable($obj, $dynamicRouteConfig);
            }
        }

        return $routeParams;
    }

    /**
     * Return an array of parameters for processing the 'fixed_params' key
     *
     * @param $routeKey
     * @return array
     */
    private function processFixedParams($routeKey)
    {
        // array with infos about the dynamic route lo load, as specified in config by user
        $dynamicRouteConfig = $this->config['parametric_routes']['routes'][$routeKey];

        $routeParameters = array();

        if (array_key_exists('fixed_params', $dynamicRouteConfig) && count($dynamicRouteConfig['fixed_params']) > 0) {
            foreach ($dynamicRouteConfig['fixed_params'] as $k => $params) {

                if (!is_array($params)) {
                    $params = array($params);
                }

                foreach ($params as $param) {
                    $routeParameters[] = array($k => $param);
                }
            }
        }

        return $routeParameters;
    }

    /**
     * fetch the route variables from the object
     *
     * @param mixed $obj object fetched from database
     * @param array $dynamicRouteConfig array from bundle config
     * @return array $routeParameters
     */
    private function prepareEntityUrlsVariable($obj, $dynamicRouteConfig)
    {
        // route parameters
        $routeParameters = array();

        // set route variables fetching the value from $obj property or method
        if (array_key_exists('object_params', $dynamicRouteConfig)) {

            foreach ($dynamicRouteConfig['object_params'] as $k => $param) {

                // get the value from $obj by accessing the variable name or calling a method
                if (isset($obj->$param)) {
                    $routeParameters[$k] = $obj->$param;
                } else if (method_exists($obj, $param)) {
                    $routeParameters[$k] = call_user_func_array(array($obj, $param), array());
                } else if (method_exists($obj, 'get' . $param)) {
                    $routeParameters[$k] = call_user_func_array(array($obj, 'get' . $param), array());
                } else if (method_exists($obj, 'is' . $param)) {
                    $routeParameters[$k] = call_user_func_array(array($obj, 'is' . $param), array());
                }
            }
        }

        // return object routes parameters
        return $routeParameters;
    }
}
