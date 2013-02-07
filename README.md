MetaTagsBundle
==============

Symfony Bundle to manage html meta tags by matching urls.

# How it works

Based on the configuration provided by the user and/or a user custom service, MetaTagsBundle _loads_ some urls and manages the association between an __url__ and its __meta tags values__ storing the data in the database.

To choose which urls must be managed by MetaTagsBundle, the __routes__ generating them must be specified. There are some different methods to achieve this:

- load all the routes of a bundle by including the bundle name in `config.yml`
- for each route, specify an `option` in the Route annotation

For routes requiring parameters that must be fetched from database, there's the possibility to load entities from database, and associate the route parameters to the entities values in order to create urls.

For more specific needs, it is also possible to create a custom service which simply returns an array of urls.

Once the urls are loaded in MetaTagsBundle, you will be able to associate the following meta tags to each url:

- __title__
- __description__
- __keywords__
- __author__
- __language__
- __robots__
- __googlebot__
- __og:title__
- __og:description__
- __og:image__

Obviously it is possible to specify default values for each meta tag, used when a url has no or partially meta tags specified by the user.

__Note:__ This bundle is in __beta__ state at the moment, in test phase and almost ready for the first release.

---

## Install

Installation instructions can be found in [Installation](Resources/doc/install.md).

The current version of this bundle is compatible with Symfony >= 2.2.*

#### Tag notes

We will try to provide tags as best as we can, to be used proficiently with composer or deps.

The development of this bundle is intended for Symfony versions >= 2.0.*; here is a brief explanation of the tagging system we use:

- tags with the __"X.Y"__ format are compatible with `Symfony >= 2.2.*`
- tags with the __"S2.0/X.Y"__ format are compatible with `Symfony 2.0.*`
- tags with the __"S2.1/X.Y"__ format are compatible with `Symfony 2.1.*`

If you are using symfony 2.0.*, follow [this guide on the symfony-2.0.x branch](https://github.com/copiaincolla/MetaTagsBundle/blob/symfony-2.0.x/README.md).

If you are using symfony 2.1.*, follow [this guide on the symfony-2.1.x branch](https://github.com/copiaincolla/MetaTagsBundle/blob/symfony-2.1.x/README.md).

More details about _tagging and branching system_ used in CopiaincollaMetaTagsBundle can be found in [Tagging and Branching system explanation](Resources/doc/tagging_branching.md).

## Configure

To configure this bundle, read [Configuration](Resources/doc/configuration.md) for all possible values.

## Load urls

To generate urls starting by all routes contained in a bundle, just add the bundle name to `config.yml`, as explained [here](Resources/doc/configuration.md#copiaincolla_meta_tags--urls_loader--exposed_routes).

You can also add a single route by specifying an option in the Route annotation, like this:

```
@Route("/product/{id}/{slug}", name="product_show", options={"ci_metatags_expose"=true})
```

Through this option, you can also choose __not__ to generate urls from a specific route:

```
@Route("/product/{id}/{slug}", name="product_show", options={"ci_metatags_expose"=false})
```

You can also generate urls for associating meta tags by fetching data from database; read the section [Configuration#dynamic_routes](Resources/doc/configuration.md#copiaincolla_meta_tags--urls_loader--parameters--dynamic_routes).

Finally, you can also create your custom __urls loader__ service, by following [this guide](Resources/doc/custom_urls_loader_service.md).


## Usage in the templates

Currently only twig is supported.

In the template containing an `<head>` tag, simply add:

```
<body>
    <head>
        {% render controller('CopiaincollaMetaTagsBundle:MetaTags:render') %}
        [...]
    </head>

    [...]
</body>
```

For a better explanation of usage in templates and advanced use, read [Template usage](Resources/doc/template_usage.md).