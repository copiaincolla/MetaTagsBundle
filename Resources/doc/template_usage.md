# Template usage

Currently only twig is supported.

Meta tags are embedded trough the `{% render %}` function of twig. It is sufficient to add the `{% render %}` function in the template containing an `<head>` tag.

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

## Using twig variables inside meta tag values

There are situations in which you want to use twig variables inside a meta tag value.

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

After loading urls generated from `product_show`route (see how [here](MetaTagsBundle/Readme.md#load_urls)), you may want to use the variable `entity` in the values of the meta tags.

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
