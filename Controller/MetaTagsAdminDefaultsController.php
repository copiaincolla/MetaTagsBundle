<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Copiaincolla\MetaTagsBundle\Entity\MetatagDefaults;

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
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->findBy(
            array(),
            array('importance' => 'DESC')
        );

        return array(
            'entities' => $entities
        );
    }

    /**
     * Displays a form to create a new MetaTagDefaults entity.
     *
     * @Route("/new", name="admin_metatag_defaults_new")
     * @Template("CopiaincollaMetaTagsBundle:MetaTagsAdminDefaults:edit.html.twig")
     */
    public function newAction()
    {
        $entity = new MetatagDefaults();

        $form = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new MetatagDefaults entity.
     *
     * @Route("/create", name="admin_metatag_defaults_create")
     * @Method("post")
     * @Template("CopiaincollaMetaTagsBundle:MetaTagsAdminDefaults:edit.html.twig")
     */
    public function createAction()
    {
        $entity = new MetatagDefaults();

        $request = $this->getRequest();

        $form = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entity);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_metatag_defaults_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing MetatagDefaults entity.
     *
     * @Route("/{id}/edit", name="admin_metatag_defaults_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetatagDefaults entity.');
        }

        $editForm = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entity);

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MetatagDefaults entity.
     *
     * @Route("/{id}/update", name="admin_metatag_defaults_update")
     * @Method("post")
     * @Template()
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetatagDefaults entity.');
        }

        $editForm = $this->createForm($this->container->get('ci_metatags.metatagdefaults_formtype'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_metatag_defaults_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Display a form to delete a MetaTag Defaults entity
     *
     * @Template()
     *
     * @param $id
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function _deleteFormAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetatagDefaults entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Deletes a Metatag entity.
     *
     * @Route("/{id}/delete", name="admin_metatag_defaults_delete")
     * @method("POST")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetatagDefaults entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_metatag_defaults'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
            ;
    }

    /**
     * Raise the importance.
     *
     * @Route("/importance/raise/{id}", name="admin_metatag_defaults_importance_raise")
     */
    public function raiseImportance($id)
    {
        $this->editImportance($id, 'up');

        return $this->redirect($this->generateUrl('admin_metatag_defaults'));
    }

    /**
     * Lower the importance.
     *
     * @Route("/importance/lower/{id}", name="admin_metatag_defaults_importance_lower")
     */
    public function lowerImportance($id)
    {
        $this->editImportance($id, 'down');

        return $this->redirect($this->generateUrl('admin_metatag_defaults'));
    }

    private function editImportance($id, $direction)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetatagDefaults entity.');
        }

        $em->getRepository('CopiaincollaMetaTagsBundle:MetatagDefaults')->editImportance($entity, $direction);

        return $entity;
    }
}
