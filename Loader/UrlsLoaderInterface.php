<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

use Copiaincolla\MetaTagsBundle\Service\RouteExposer;
use Copiaincolla\MetaTagsBundle\Service\UrlsGenerator;
use Copiaincolla\MetaTagsBundle\Service\DynamicRouteUrlsGenerator;

/**
 * Urls loader interface.
 */
interface UrlsLoaderInterface
{
    public function __construct(
        array $config = array(),
        Router $router,
        EntityManager $em,
        $container,
        RouteExposer $metaTagsRouteExposer,
        UrlsGenerator $urlsGenerator,
        DynamicRouteUrlsGenerator $dynamicRouteUrlsGenerator
    );

    public function getGeneratedUrls($excludeAlreadyAssociated = false);

    public function getDatabaseUrls();
}
