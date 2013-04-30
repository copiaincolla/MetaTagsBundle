# Create a custom urls loader service

You can add custom generated urls by defining a new service returning a formatted array.

In your `Acme/FooBundle/Loader/UrlsLoader`:

```
<?php

namespace Acme\FooBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;

/**
 * Urls loader.
 */
class UrlsLoader
{
    protected $router;
    protected $em;

    public function __construct(Router $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    public function getUrls()
    {
        $output = array();
        
        /*
         * generate custom urls
         * eg. with $this->router->generate()
         */
        [...]
        
        return $output;
    }
}

```

Be careful:
- `Acme/FooBundle/Loader/UrlsLoader` __must__ implement the function `getUrls()` (if you do not specify another function name in config.yml)
- `$output` __must__ reflect the following structure:

```
$output =  array() {
    [route_name]=>
    array() {
        [0]=>
        string() url string generated with $this->router->generate()
        [1]=>
        string() url string generated with $this->router->generate()
        [...]
    },
    [route_name]=>
    array() {
        [0]=>
        string() url string generated with $this->router->generate()
        [1]=>
        string() url string generated with $this->router->generate()
        [...]
    },
    [...]
}
```

`$ouput` is a two-levels array: the key is the *route name* (eg. defined with `@Route` annotation) and the value is an array of urls to match.

In your `Acme/FooBundle/Resources/services.xml`:

```
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="acme.metatags.url_loader" class="Acme\FooBundle\Loader\UrlsLoader">
            <argument type="service" id="router" />
            <argument type="service" id="doctrine.orm.entity_manager" />
        </service>
    </services>
</container>

```

In your `app/config/config.yml`:

```
copiaincolla_meta_tags:
   urls_loader:
        custom_service:
            id: acme.metatags.urll_loader
            function: getUrls
```
