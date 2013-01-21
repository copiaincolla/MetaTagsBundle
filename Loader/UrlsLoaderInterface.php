<?php

namespace Copiaincolla\MetaTagsBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

/**
 * Urls loader.
 */
interface UrlsLoaderInterface
{
    function __construct(array $config = array(), Router $router, EntityManager $em, $container);

    function getUrls($excludeAlreadyAssociated = false);
}
