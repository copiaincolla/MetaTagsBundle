Installation
============

The `master` branch and the last `X.Y` tag are compatible with `Symfony >= 2.3.*`.

## Step 1 - Vendors

To choose the right version of this bundle to install, have a look at [Tagging and Branching system explanation.](tagging_branching.md)

Review the list of available tags [here.](https://github.com/copiaincolla/MetaTagsBundle/tags)

In short:

- tags with the __"X.Y"__ format are compatible with the lastest version of Symfony
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

note: substitute `"X.Y"` with the most recent tag or the specific tag you want to use

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

Import the routes to manage the metatags by adding the following line to `app/routing.yml`:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle_admin:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"

These routes should be proteced by access control. Given `^/admin` as a secured path, you may write:

    # CopiaincollaMetaTagsBundle
    ci_metatags_bundle_admin:
        resource: "@CopiaincollaMetaTagsBundle/Resources/config/routing.yml"
        prefix:   /admin/


## Step 4 - Configuration

Add the minimal configuration to `app/config.yml`:

    copiaincolla_meta_tags: ~

For configuration reference see [Configuration](configuration.md).

## Step 5 - Database

Update the database by adding some tables; run:

    php app/console doctrine:schema:update --force

Two tables will be created: `ci_metatag` and `ci_metatag_default`.

## Finish

The bundle is now ready to work!

Have a look at [Configuration](configuration.md), [Template usage](template_usage.md) and [How to manually add urls to be managed by the bundle](custom_urls_loader_service.md).