<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

use Copiaincolla\MetaTagsBundle\Service\RouteExposer;
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
     * @param \Copiaincolla\MetaTagsBundle\Service\RouteExposer $metaTagsRouteExposer
     */
    public function __construct(
        array $config = array(),
        Router $router,
        EntityManager $em,
        $container,
        RouteExposer $metaTagsRouteExposer,
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
        // associative array [route name] => [array of urls] to be returned
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
            if (array_key_exists($routeKey, $this->config['urls_loader']['parameters']['dynamic_routes'])) {
                $output[$routeKey] = $this->dynamicRouteUrlsGenerator->generateUrls($routeKey, $route);
            }

            // route does not need variables to be loaded by objects in database
            else {
                $output[$routeKey] = $this->urlsGenerator->generateUrls($routeKey, $route);
            }
        }

        /*
         * load a custom service defined by user, to load additional generated routes
         */
        $output = array_merge($output, $this->getCustomServiceUrls());

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

    private function getCustomServiceUrls()
    {
        $output = array();

        if (array_key_exists('custom_service', $this->config['urls_loader'])) {

            $serviceId = $this->config['urls_loader']['custom_service']['id'];
            $serviceFunction = $this->config['urls_loader']['custom_service']['function'];

            // load custom service
            $service = $this->container->get($serviceId);


            if (!is_null($serviceFunction) && !method_exists($service, $serviceFunction)) {
                throw new \Exception('Called undefined function "'.$serviceFunction.'" in class "'.get_class($service).'"');
            } elseif (is_null($serviceFunction) && !method_exists($service, 'getUrls')) {
                throw new \Exception('Called undefined function "getUrls" in class "'.get_class($service).'". Either add getUrls() or specify a custom function under custom_service.function key.');
            }

            $loadedUrls = ($serviceFunction) ? $service->$serviceFunction() : $service->getUrls();

            // merge generated urls
            foreach ($loadedUrls as $routeKey => $preparedRoute) {
                // @FIX overwrite
                $output[$routeKey] = $preparedRoute;
            }
        }

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

    /**
     * return bundle configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
