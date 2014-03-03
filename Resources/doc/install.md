Installation
============

The `master` branch and the `X.Y` tags are compatible with `Symfony >= 2.2.*`.

## Step 1 - Vendors

To choose the right version of this bundle to install, have a look at [Tagging and Branching system explanation.](tagging_branching.md)

Review the list of available tags [here.](https://github.com/copiaincolla/MetaTagsBundle/tags)

In short:

- tags with the __"X.Y"__ format are compatible with `Symfony >= 2.2.*`
- tags with the __"S2.1/X.Y"__ format are compatible with `Symfony 2.1.*`
- tags with the __"S2.0/X.Y"__ format are compatible with `Symfony 2.0.*`

### composer

If you are using `composer`, add the following line to your `composer.json`:

```
{
    "require": {
        "copiaincolla/metatags-bundle": "X.Y"
    }
}
```

note: substitute `"X.Y"` with the most recent tag or the concrete tag you want to use
    
### deps

If you are using `deps`, add the following line to your `deps`:

```
[CopiaincollaMetaTagsBundle]
    git=https://github.com/copiaincolla/MetaTagsBundle.git
    target=/bundles/Copiaincolla/MetaTagsBundle
    version=X.Y
```

note: substitute `"X.Y"` with the most recent tag or the concrete tag you want to use

And in your `app/autoload.php`:

    // Copiaincolla
    'Copiaincolla'   => __DIR__.'/../vendor/bundles',

## Step 2 - AppKernel.php

Load the bundle by adding this to `app/AppKernel.php`:

    new Copiaincolla\MetaTagsBundle\CopiaincollaMetaTagsBundle(),

## Step 3 - Import routing

Add the following line to `app/routing.yml`:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle_admin:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
    
These routes should be proteced by access control. Given `^/admin` as a secured path, you may write:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle_admin:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
        prefix:   /admin/

    
## Step 4 - Configuration

Add to `app/config.yml`:

    copiaincolla_meta_tags: ~

## Step 5 - Database

Update the database by adding some tables; run:

    php app/console doctrine:schema:update --force


The bundle is now ready to work!
