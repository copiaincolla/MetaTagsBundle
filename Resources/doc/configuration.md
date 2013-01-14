Configuration
=============

This section explains how to tweak the configuration depending on your needs.

The most basic configuration you can specify in `app/config/consig.yml` is:

    copiaincolla_meta_tags: ~
    
# Configuration Reference

The most complete configuration is:

```
copiaincolla_meta_tags:
    defaults:
        title: Foo default title
        description: Foo default description
        keywords: Foo default keywords
        author: Foo default author
    
    exposed_routes:
        bundles:
            - FooBarBundle
            - FooOtherBundle

    urls_loader_custom_service: foo.metatags.url_loader

    dynamic_routes:
        default_params:
            _locale: en
        routes:
          category_show:
              repository: "FooBarBundle:Category"
              fixed_params:
                  _locale: [en, de, fr]
              object_params:
                  id: id
                  slug: getSlug
          product_show:
              repository: "FooBarBundle:Product"
              fixed_params:
                  _locale: [en, de, fr]
              object_params:
                  id: id
                  slug: getSlug
                  category_slug: getCategorySlug
```

## defaults

Set the defaults values for the meta tags.

```
    defaults:
        title: Foo default title
        description: Foo default description
        keywords: Foo default keywords
        author: Foo default author
```

These values will be used for every page: if you specify one or more meta tags for a specific url, these will be loaded from database and optionally merged with the default ones.

Other possible configurations for `default` key are:

```
    defaults:
        title: Foo default title
        description: Foo default description
        keywords: ~
        author: ~
```

```
    defaults:
        title: Foo default title
        description: Foo default description
```

## exposed_routes

Specify the routes you want to manage by adding bundle names to `exposed_routes.bundles` key:

```
    exposed_routes:
        bundles:
            - FooBarBundle
            - FooOtherBundle
```

In this case all routes contained in all Controllers belonging to `FooBarBundle` and `FooOtherBundle` are loaded, and MetaTagsBundle will try to generate all the possible urls based on these routes.

Other possible configurations for `exposed_routes` key are:

Same as above:

```
    exposed_routes:
        bundles: [FooBarBundle, FooOtherBundle ]
```

Will load no bundles:

```
    exposed_routes:
        bundles: ~
```

```
    exposed_routes: ~
```

## urls_loader_custom_service

Specify the id of your custom service to load additional urls.

```
    urls_loader_custom_service: [servce_id]
```


## dynamic_routes

With this option, you can load urls generated from a route and entities stored in database

```
    dynamic_routes:
        prodotto_show:
            params:
                prefix: products
            repository: "AcmeBundle:Product"
            object_params:
                id: id
                slug: getSlug
```

The `dynamic_routes` key is an array, which keys are the routes generating the urls and the values are useful to provide the required parameters

In details:

```
    dynamic_routes:
    
        # route name
        product_show:
            
            # array of fixed params required by the route 'product_show'
            fixed_params:
                # for every generated url, the param 'prefix' will have the value 'products'
                prefix: products

            # repository of entities to fetch the data from
            repository: "AcmeBundle:Product"

            # associate route parameters with values fetched from each entity loaded from database
            object_params:
                id: id
                slug: getSlug
```
