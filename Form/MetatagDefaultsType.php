<?php

namespace Copiaincolla\MetaTagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Copiaincolla\MetaTagsBundle\Loader\UrlsLoader;

class MetatagDefaultsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pathRegex', null, array('label' => 'Regex to match'))
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
        return 'copiaincolla_metatagsbundle_metatagdefaultstype';
    }

}
