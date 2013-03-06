<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * MetaTagsAdminDefaults controller.
 *
 * @Route("/metatags/defaults")
 */
class MetaTagsAdminDefaultsController extends Controller
{
    /**
     * Display default meta tags from database
     *
     * @Route("/", name="admin_metatag_defaults")
     * @Template()
     */
    public function indexAction()
    {
        $entityMetatagDefaults = $this->container->get('ci_metatags.defaults')->getEntityMetatagDefaults();

        return array(
            'entity' => $entityMetatagDefaults
        );
    }

    /**
     * Displays a form to edit an existing MetatagDefaults entity.
     *
     * @Route("/edit", name="admin_metatag_defaults_edit")
     * @Template()
     */
    public function editAction()
    {
        $entityMetatagDefaults = $this->container->get('ci_metatags.defaults')->getEntityMetatagDefaults();

        $editForm = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entityMetatagDefaults);

        return array(
            'entity' => $entityMetatagDefaults,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing MetatagDefaults entity.
     *
     * @Route("/update", name="admin_metatag_defaults_update")
     * @Method("post")
     * @Template()
     */
    public function updateAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entityMetatagDefaults = $this->container->get('ci_metatags.defaults')->getEntityMetatagDefaults();
        $editForm = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entityMetatagDefaults);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entityMetatagDefaults);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_metatag_defaults_edit'));
        }

        return array(
            'entity' => $entityMetatagDefaults,
            'form' => $editForm->createView(),
        );
    }
}
