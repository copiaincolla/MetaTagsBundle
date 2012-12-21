MetaTagsBundle
==============

Symfony Bundle to manage html metatags

# Note

This bundle is in __alpha__ state at the moment, not ready for production environment

## Install

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
    
## Configuration

Add to `app/config.yml`:

    copiaincolla_meta_tags: ~
    
### Default meta tags values

To define the default meta tags values, just add them to the configuration, under the key `dynamic_routes_default_params`:

    copiaincolla_meta_tags:
        defaults:
            title: "My default Title"
            description: "My default meta-description content"
            keywords: "My default meta-keywords content"
            author: "My default meta-author content"
    
## Load dynamic routes
    
### Default route variables
    
You can define `default_parameters`, an array of parameters to be used for all generated routes. For example, you may want to define the value of `_locale` parameter to be used for all routes you are going to generate:
    
    copiaincolla_meta_tags:
        default_params:
            _locale: en

### Load data from database to compile urls

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


## Usage

In the template you want to add metatags, add the following:

    {% render 'CopiaincollaMetaTagsBundle:MetaTags:render' %}
    
Normally it gose in a template containing the `<head>` section.

## Load user generated urls

The bundle automatically loads these urls to match:
- static urls (based on the routes which don't need variables to be compiled)
- dynamic urls as set in `config.yml` un der the key `copiaincolla_meta_tags.dynamic_routes`

As well as these, you can generate custom urls and add them to the bundle generated ones by following [this guide](https://github.com/copiaincolla/MetaTagsBundle/blob/master/Resources/doc/add_custom_routes.md).
