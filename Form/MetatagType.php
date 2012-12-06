<?php

namespace Copiaincolla\MetaTagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MetatagType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('route')
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
