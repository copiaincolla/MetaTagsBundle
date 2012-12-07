MetaTagsBundle
==============

Symfony Bundle to manage html metatags

# Install

If you are using `composer`, add the following line to your `composer.json`:


    {
        "require": {
            "copiaincolla/metatags-bundle": "dev-master"
        }
    }

If you are using `deps`, add the following line to your `deps`:
    
    [CopiaincollaMetaTagsBundle]
        git=https://github.com/copiaincolla/MetaTagsBundle.git
        target=/bundles/Copiaincolla/MetaTagsBundle    

And in your `app/autoload.php`:

    // Copiaincolla
    'Copiaincolla'   => __DIR__.'/../vendor/bundles',

Load the bundle by adding this to `app/AppKernel.php`:

    new Copiaincolla\MetaTagsBundle\CopiaincollaMetaTagsBundle(),

Add the following line to `app/routing.yml`:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
    
# Configuration

Add to `app/config.yml`:

    copiaincolla_meta_tags: ~
    
You can define `default_parameters`, an array of parameters to be used for all generated routes. For example, you may want to define the value of `_locale` parameter to be used for all routes you are going to generate:
    
    copiaincolla_meta_tags:
        default_params:
            _locale: en
        
Additionally, you can specify `dynamic_routes`, an array of route names associated to data fetched from database.
        
    copiaincolla_meta_tags:
        dynamic_routes:
            prodotto_show:
                params:
                    prefix: products
                repository: "AcmeBundle:Product"
                object_params:
                    id: id
                    slug: getSlug

