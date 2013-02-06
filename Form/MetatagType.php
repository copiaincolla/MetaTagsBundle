<?php

namespace Copiaincolla\MetaTagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Copiaincolla\MetaTagsBundle\Loader\UrlsLoader;

class MetatagType extends AbstractType
{

    protected $urlsLoader;

    public function __construct(UrlsLoader $urlsLoader)
    {
        $this->urlsLoader = $urlsLoader;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('url', 'text', array('required' => true))
            ->add('title', null, array('label' => 'Meta tag "title"'))
            ->add('description', null, array('label' => 'Meta tag "description"'))
            ->add('keywords', null, array('label' => 'Meta tag "keywords"'))
            ->add('robots', null, array('label' => 'Meta tag "robots"'))
            ->add('googlebot', null, array('label' => 'Meta tag "googlebot"'))
            ->add('author', null, array('label' => 'Meta tag "author"'))
            ->add('language', null, array('label' => 'Meta tag "language"'))
            ->add('ogTitle', null, array('label' => 'Meta tag "og:title"'))
            ->add('ogDescription', null, array('label' => 'Meta tag "og:description"'))
            ->add('ogImage', null, array('label' => 'Meta tag "og:image"'))
        ;
    }

    public function getName()
    {
        return 'copiaincolla_metatagsbundle_metatagtype';
    }

}
