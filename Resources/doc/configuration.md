Configuration
=============

This section explains how to tweak the configuration depending on your needs.

The most basic configuration you can specify in `app/config/consig.yml` is:

    copiaincolla_meta_tags: ~
    
# Configuration Reference

The complete configuration is:

```
copiaincolla_meta_tags:
    
    urls_loader:
    
        exposed_routes:
            bundles:
                - FooBarBundle
                - FooOtherBundle

        custom_service:
            id: foo.metatags.url_loader
            function: foo

        parameters:
            fixed_params:
                _locale: [en, it, fr]
    
            dynamic_routes:
                routes:
                    category_show:
                        repository: "FooBarBundle:Category"
                        fixed_params:
                            foo_param: [foo, bar]
                        object_params:
                            id: id
                            slug: getSlug
                  product_show:
                        repository: "FooBarBundle:Product"
                        fixed_params:
                            foo_param: [foo, bar]
                        object_params:
                            id: id
                            slug: getSlug
                            category_slug: getCategorySlug
```

---

## copiaincolla_meta_tags / urls_loader

Configure the loaded routes



### copiaincolla_meta_tags / urls_loader / exposed_routes

Specify the routes you want to manage by adding bundle names to `exposed_routes.bundles` key:

```
copiaincolla_meta_tags:
    urls_loader:
        exposed_routes:
            bundles:
                - FooBarBundle
                - FooOtherBundle
```

In this case all routes contained in all Controllers belonging to `FooBarBundle` and `FooOtherBundle` are loaded, and MetaTagsBundle will try to generate all the possible urls based on these routes.

Other possible configurations for `exposed_routes` key are:

Same as above:

```
copiaincolla_meta_tags:
    urls_loader:
        exposed_routes:
            bundles: [FooBarBundle, FooOtherBundle ]
```

Will load no bundles:

```
copiaincolla_meta_tags:
    urls_loader:
        exposed_routes:
            bundles: ~
```

or

```
    exposed_routes: ~
```

### copiaincolla_meta_tags / urls_loader / custom_service

Specify the id and (optionally) the function of your custom service to load additional urls.

```
copiaincolla_meta_tags:
    urls_loader:
        custom_service:
            id: foo.metatags.url_loader
            function: foo
```

### copiaincolla_meta_tags / urls_loader / parameters

Configure the parameters to be used to generate the routes that needs parameters.

#### fixed_params

An array of `route parameter` => `value` (can be either a value or an array) which are applied to every generated url.

MetaTags bundle loads all the user defined routes, and tries to generate the corresponding urls. If an url needs a parameter, the bundle tries to load this array.

Example: the route

```
/**
 * Route("/{_locale}/action)
 */
public function contactAction()
```

with

```
copiaincolla_meta_tags:
    urls_loader:
        parameters:
            fixed_params:
                _locale: [en, fr, de]
```

will generate the urls:

```
/en/contact
/fr/contact
/de/contact
```

#### copiaincolla_meta_tags / urls_loader / parameters / dynamic_routes

With this option, you can load urls generated from a route and entities stored in database

```
copiaincolla_meta_tags:
    urls_loader:
        parameters:
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
copiaincolla_meta_tags:
    urls_loader:
        parameters:
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