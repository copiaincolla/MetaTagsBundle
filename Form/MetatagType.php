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
        $urls = $this->urlsLoader->getUrls();

        /*
         * prepare the multidimensional array of urls to be put in a <select> tag
         * by replacing the urls keys
         */
        array_walk($urls, function($val, $key) use(&$urls){
            if (count($val) > 0) {
                $urls[$key] = array_combine($val, $val);
            }
        });

        $builder
                ->add('url', 'choice', array(
                    'choices' => $urls,
                    'required' => true,
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
