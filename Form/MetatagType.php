<?php

namespace Copiaincolla\MetaTagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Copiaincolla\MetaTagsBundle\Loader\UrlsLoader;

class MetatagType extends AbstractType
{

    protected $urlsLoader;

    public function __construct(UrlsLoader $urlsLoader)
    {
        $this->urlsLoader = $urlsLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('route', 'choice', array(
                    'choices' => $this->urlsLoader->getUrls(),
                    'required' => false,
                ))
                ->add('title')
                ->add('description')
                ->add('keywords')
                ->add('author')
        ;
    }

    public function getName()
    {
        return 'copiaincolla_metatagsbundle_metatagtype';
    }

}
