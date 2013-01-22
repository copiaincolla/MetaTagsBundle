<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

use Copiaincolla\MetaTagsBundle\Service\MetaTagsRouteExposer;
use Copiaincolla\MetaTagsBundle\Service\UrlsGenerator;
use Copiaincolla\MetaTagsBundle\Service\DynamicRouteUrlsGenerator;

/**
 * Urls loader.
 *
 * Load the user selected routes and generate corresponding urls
 */
class UrlsLoader implements UrlsLoaderInterface
{
    protected $config;

    protected $router;
    protected $em;
    protected $container;
    protected $metaTagsRouteExposer;
    protected $dynamicRouteUrlsGenerator;
    protected $urlsGenerator;

    /**
     * @param array $config
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Doctrine\ORM\EntityManager $em
     * @param $container
     * @param \Copiaincolla\MetaTagsBundle\Service\MetaTagsRouteExposer $metaTagsRouteExposer
     */
    public function __construct(
        array $config = array(),
        Router $router,
        EntityManager $em,
        $container,
        MetaTagsRouteExposer $metaTagsRouteExposer,
        UrlsGenerator $urlsGenerator,
        DynamicRouteUrlsGenerator $dynamicRouteUrlsGenerator
    )
    {
        $this->config = $config;

        $this->router = $router;
        $this->em = $em;
        $this->container = $container;
        $this->metaTagsRouteExposer = $metaTagsRouteExposer;
        $this->urlsGenerator = $urlsGenerator;
        $this->dynamicRouteUrlsGenerator = $dynamicRouteUrlsGenerator;
    }

    /**
     * Get all generated urls by the CopiaincolleMetaTagsBundle, based on the configuration passed by the user
     *
     * Return an associative array of (relative) urls organized by route name
     * es: $output = array(
     *         'homepage' => array([url]),
     *         'products' => array([url], [url], [url], [url], ...),
     *         ...
     * )
     *
     * User will be able to set meta tags for each url
     *
     * @return array
     */
    public function getGeneratedUrls($excludeAlreadyAssociated = false)
    {
        // associative array [route name] => [array urls] to be returned
        $output = array();

        // remove the baseUrl to generate baseUrl independent urls
        $baseUrl = $this->router->getContext()->getBaseUrl();
        $this->router->getContext()->setBaseUrl(null);

        // iterate on loaded routes to generate urls
        foreach ($this->router->getRouteCollection()->all() as $routeKey => $route) {

            // if route is not exposed, don't consider it
            if (!$this->metaTagsRouteExposer->isRouteExposed($route)) {
                continue;
            }

            // array of generated urls, organized by route name
            $output[$routeKey] = array();

            // route needs datas fetched from database
            if (array_key_exists($routeKey, $this->config['parametric_routes']['routes'])) {

                $output[$routeKey] = array_merge($output[$routeKey], $this->dynamicRouteUrlsGenerator->generateUrls($routeKey, $route));

            }

            // route does not need variables to be loaded by objects in database
            else {
                $output[$routeKey] = array_merge($output[$routeKey], $this->urlsGenerator->generateUrls($routeKey, $route));
            }
        }

        /*
         * load a custom service defined by user, to load additional generated routes
         */

        if (array_key_exists('urls_loader_custom_service', $this->config) && $this->config['urls_loader_custom_service'] != null) {

            // load custom service
            $urlsLoaderCustomService = $this->container->get($this->config['urls_loader_custom_service']);

            // merge generated urls
            foreach ($urlsLoaderCustomService->getUrls() as $routeKey => $preparedRoute) {
                // @FIX overwrite
                $output[$routeKey] = $preparedRoute;
            }
        }

        // restore the original baseURl
        $this->router->getContext()->setBaseUrl($baseUrl);

        // purge urls from already associated urls
        if ($excludeAlreadyAssociated) {
            $output = $this->purgeRoutesArrayFromAlreadyAssociatedUrls($output);
        }

        // purge $output from routes with no urls generated
        $output = array_filter($output, function($urls) {
            return count($urls) > 0;
        });

        // sort the urls array by route names
        ksort($output);

        return $output;
    }

    /**
     * Purge the array of routes/urls from the urls already associated (currently stored in database)
     *
     * @param $routes
     */
    private function purgeRoutesArrayFromAlreadyAssociatedUrls($routes)
    {
        // get all url stored in database
        $databaseUrls = $this->getDatabaseUrls();

        foreach ($routes as $route => $urls) {
            foreach ($urls as $k => $url) {
                if (in_array($url, $databaseUrls)) {
                    unset($routes[$route][$k]);
                }
            }
        }

        return $routes;
    }

    /**
     * Get all urls  stored in database
     *
     * @return array of urls
     */
    public function getDatabaseUrls()
    {
        $metatags = $this->em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->findAll();
        $arrDatabaseUrls = array();

        foreach($metatags as $metatag) {
            $arrDatabaseUrls[] = $metatag->getUrl();
        }

        return $arrDatabaseUrls;
    }
}
