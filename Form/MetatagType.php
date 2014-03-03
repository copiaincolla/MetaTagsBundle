<?php

namespace Copiaincolla\MetaTagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Copiaincolla\MetaTagsBundle\Loader\UrlsLoader;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MetatagType extends AbstractType
{

    protected $urlsLoader;

    public function __construct(UrlsLoader $urlsLoader)
    {
        $this->urlsLoader = $urlsLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $config = $this->urlsLoader->getConfig();

        $entity = $builder->getForm()->getData();
        */


        /*
         * add 'url' field
         */
        /*
        $builder->setAttribute('allow_editable_url', $config['config']['allow_editable_url']);

        $urlType = 'text';
        if ($config['config']['allow_editable_url'] === false && $entity->getUrl() !== null) {
            $urlType = 'hidden';
        };
        $builder->add('url', $urlType, array('required' => true));
        */
        $builder->add('url', 'hidden');


        // add other fields
        $builder
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

    /*
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['allow_editable_url'] = $form->getAttribute('allow_editable_url');
    }
    */

    public function getName()
    {
        return 'copiaincolla_metatagsbundle_metatagtype';
    }

}
