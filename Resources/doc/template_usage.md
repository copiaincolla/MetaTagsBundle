# Template usage

Meta tags are embedded trough the `{% render %}` function of twig. It is sufficient to add the `{% render %}` function in the template containing an `<head>` tag.

Currently only twig is supported.

Let's say `AcmeFooBundle::layout.html.twig` exists, and contains the base layout; to embed all meta tags managed by MetaTagsBundle add the following inside the `<head>` tag:

```
<body>
    <head>
        {% render controller('CopiaincollaMetaTagsBundle:MetaTags:render') %}
        [...]
    </head>

    [...]
</body>
```

This will print:

```
<body>
    <head>
        <title>[...]</title>
        <meta name="description" content="[...]" />
        <meta name="keywords" content="[...]" />
        <meta name="author" content="[...]" />
        <meta name="language" content="[...]" />
        <meta name="robots" content="[...]" />
        <meta name="googlebot" content="[...]" />
        <meta property="og:title" content="[...]" />
        <meta property="og:description" content="[...]" />
        <meta property="og:image" content="[...]" />

        [...]
    </head>

    [...]
</body>
```

## Override of meta tag values from template

Besides specifying default meta tags and creating custom meta tags depending on a url, you can override "manually" a meta tag directly from the template.

To do this, pass the variable `inlineMetatags` to the `render()` function:

```
{% render controller('CopiaincollaMetaTagsBundle:MetaTags:render', { 'inlineMetatags': {'title': 'New foo title', 'description': 'New foo description'} }) %}
```

The "precedence" order, from the most important to the less one, is:

- values passed from template with `inlineMetatags` variable
- user entered meta tag values for a specific url
- default meta tag values

## Use of twig variables inside meta tag values

There are situations where you want to use twig variables inside a meta tag value.

For example, you have the entity `AcmeFooBundle:Product`:

```
<?php
namespace Acme\FooBundle\Controller\Entity;

class Product
{
    private id;
    
    private name;
    
    private slug;
    
    [...]
```

In `Acme\FooBundle\Controller\ProductController` there's an action called `product_show`, which passes a variable called `entity` to the twig template:

```
<?php

namespace Acme\FooBundle\Controller;

use [...]

class ProductController extends Controller
{
    /**
     * Show product details
     *
     * @Route("/product/{id}/{slug}", name="product_show")
     */
    public function showAction($id)
    {
        [...]

        return array(
            'entity' => $product
        );
    }

```

After loading urls generated from `product_show`route (see how [here](../../Readme.md#load_urls)), you may want to use the variable `entity` in the values of the meta tags.

For example, for url /product/1/the-best-product`, you may want:

```
<title>www.mysite.com | Product "{{ entity.name }}"</title>
```

You can use twig syntax inside meta tag values, it will be correctly parsed using the variables you will provide.

Now, `AcmeFooBundle::layout.html.twig` should be slightly modified, introducing an additional block:

 ```
 <body>
     <head>
        {% block metatags %}
            {% render controller('CopiaincollaMetaTagsBundle:MetaTags:render') %}
        {% endblock metatags %}
        [...]
     </head>

     [...]
 </body>
 ```

Now, in `AcmeFooBundle:Product:show.html.twig`, overwrite the `metatags` block as this:

```
{% block metatags %}
    {% render controller('CopiaincollaMetaTagsBundle:MetaTags:render', {'vars': {'entity': entity}}) %}
{% endblock metatags %}
```

In this way the variable `entity` passed from the action to the template, will be available in `www.mysite.com | Product "{{ entity.name }}"`.

## Use of request attributes in meta tag values

You may want to use some parameter stored in the request to compile a meta tag value.

A clear example of this is the `language` meta tag: it's common to set it as the `\_locale` parameter stored in the request. To do so you can use the __\_master\_request__ twig variable to access the original request.

So, for a route you can populate the `language` field with:

```
{{ _master_request.locale }}
```

This is the same of putting in a template `{{ app.request.locale }}`.

In general you can access request parameter with the syntax:

```
{{ _master_request.get('[PARAMETER_NAME]') }}
```

For example, for the `product_show` route, to populate a meta tag value (eg: keywords, or description) you could use:

```
{{ _master_request.get('slug') }}
```