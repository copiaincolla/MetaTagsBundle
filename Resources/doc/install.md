Installation
============

The `master` branch and the `X.Y` tags are compatible with the version 2.2.x of Symfony.

## Step 1 - Vendors

### composer

If you are using `composer`, add the following line to your `composer.json`:


    {
        "require": {
            "copiaincolla/metatags-bundle": "0.*"
        }
    }
    
### deps

If you are using `deps`, add the following line to your `deps`:
    
    [CopiaincollaMetaTagsBundle]
        git=https://github.com/copiaincolla/MetaTagsBundle.git
        target=/bundles/Copiaincolla/MetaTagsBundle
        version=0.*

And in your `app/autoload.php`:

    // Copiaincolla
    'Copiaincolla'   => __DIR__.'/../vendor/bundles',

## Step 2 - AppKernel.php

Load the bundle by adding this to `app/AppKernel.php`:

    new Copiaincolla\MetaTagsBundle\CopiaincollaMetaTagsBundle(),

## Step 3 - Import routing

Add the following line to `app/routing.yml`:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
    
## Step 4 - Configuration

Add to `app/config.yml`:

    copiaincolla_meta_tags: ~
    
The bundle is now ready to work!