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

Add the following lines to `app/config.yml`:

    copiaincolla_meta_tags: ~

Add the following line to `app/routing.yml`:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
    

