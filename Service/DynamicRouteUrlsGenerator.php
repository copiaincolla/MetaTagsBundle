<?php

namespace Copiaincolla\MetaTagsBundle\Service;

use Symfony\Component\Routing\Route;
use Copiaincolla\MetaTagsBundle\Service\UrlsGenerator;
use Copiaincolla\MetaTagsBundle\Lib\CartesianProduct;

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
     * Generate urls for a route specified under the 'urls_loader.parameters.dynamic_routes' key
     *
     * @param $routeKey
     * @param $route
     * @return array
     */
    public function generateUrls($routeKey, Route $route)
    {
        $fixedParams = $this->processFixedParams($routeKey);
        $repositoryParams = $this->processRepositoryParam($routeKey);


        $fixedParamsCartesian = CartesianProduct::cartesian($fixedParams);

        $urls = array();

        // both are array
        // @FIX: improve this very raw piece of code
        if(count($fixedParams) > 0 && count($repositoryParams) > 0) {
            foreach ($fixedParamsCartesian as $fixedParam) {
                foreach ($repositoryParams as $repositoryParam) {

                    $routeParams = $fixedParam + $repositoryParam;

                    $urls[] = $this->urlsGenerator->generateUrl($routeKey, $route, $routeParams);
                }
            }
        } elseif ($fixedParamsCartesian) {
            foreach ($fixedParamsCartesian as $fixedParam) {
                $urls[] = $this->urlsGenerator->generateUrl($routeKey, $route, $fixedParam);
            }
        } elseif ($repositoryParams) {
            foreach ($repositoryParams as $repositoryParam) {
                $urls[] = $this->urlsGenerator->generateUrl($routeKey, $route, $repositoryParam);
            }
        }

        return $urls;
    }

    /**
     * Return an array of parameters for processing the 'fixed_params' key
     * Merge the route specific fixed parameters with the default fixed parameters
     *
     * @param $routeKey
     * @return array
     */
    private function processFixedParams($routeKey)
    {
        // array of fixed_params for all parametric routes (can be empty)
        $parametricRoutesFixedParams = $this->config['urls_loader']['parameters']['fixed_params'];


        // array of fixed_params for the current route (can be empty)
        $fixedParams = $this->config['urls_loader']['parameters']['dynamic_routes'][$routeKey]['fixed_params'];

        $routeParameters = array();

        foreach ($fixedParams as $k => $params) {

            $params = (array)$params;

            // apply fixed parameters for all parametric routes
            if (isset($parametricRoutesFixedParams[$k])) {
                $params = array_unique(array_merge($params, (array)$parametricRoutesFixedParams[$k]));
            }

            foreach ($params as $param) {
                $routeParameters[$k][] = $param;
            }
        }

        return $routeParameters;
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
        $dynamicRouteConfig = $this->config['urls_loader']['parameters']['dynamic_routes'][$routeKey];

        $routeParams = array();

        $repository = $dynamicRouteConfig['repository'];

        if (!is_null($repository)) {

            // check the repository name
            try {
                $this->em->getRepository($repository);
            } catch (\Exception $e) {
                throw new \Exception("Unable to find repository \"{$repository}\"");
            }


            // data fetched from database
            $repositoryFunction = $dynamicRouteConfig['repository_fetch_function'];

            // try to call a user defined function
            if (!is_null($repositoryFunction)) {

                if (!method_exists($this->em->getRepository($repository), $repositoryFunction)) {
                    throw new \Exception("Called undefined function\"{$repositoryFunction}\" for repository \"{$repository}\"");
                }

                $data = $this->em->getRepository($repository)->$repositoryFunction();
            }

            // fetch all records
            else {
                $data = $this->em->getRepository($repository)->findAll();
            }

            // generate urls using the parameters from the fetched records
            foreach ($data as $obj) {
                $routeParams[] = $this->prepareEntityUrlsVariable($obj, $dynamicRouteConfig);
            }
        }

        return $routeParams;
    }

    /**
     * fetch the route variables from the object
     *
     * @param mixed $obj object fetched from database
     * @param array $dynamicRouteConfig array containing the dynamic route configuration from bundle config
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
