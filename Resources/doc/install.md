Installation
============

The `master` branch and the `X.Y` tags are compatible with the version 2.2.x of Symfony.

## Step 1 - Vendors

### composer

If you are using `composer`, add the following line to your `composer.json`:


    {
        "require": {
            "copiaincolla/metatags-bundle": "S2.1-0.1"
        }
    }

note: substitute "S2.1-0.1" with the most recent tag or the concrete tag you want to use
    
### deps

If you are using `deps`, add the following line to your `deps`:
    
    [CopiaincollaMetaTagsBundle]
        git=https://github.com/copiaincolla/MetaTagsBundle.git
        target=/bundles/Copiaincolla/MetaTagsBundle
        version=S2.1-0.1

note: substitute "S2.1-0.1" with the most recent tag or the concrete tag you want to use

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

## Step 5 - Database

Update the database by adding some tables; run:

    php app/console doctrine:schema:update --force


The bundle is now ready to work!
