<?php

namespace Copiaincolla\MetaTagsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Copiaincolla\MetaTagsBundle\Entity\Metatag;

//  $metatagasLoader = $this->container->get('ci_metatags.loader');
//  $urls = $metatagasLoader->getUrls();

/**
 * MetaTag controller.
 *
 * @Route("/metatags")
 */
class MetaTagsAdminController extends Controller
{

    /**
     * @Route("/", name="admin_metatag")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->findBy(array(), array('url' => 'DESC'));

        return array(
            'entities' => $entities
        );
    }


    /**
     * Displays a form to create a new Metatag entity.
     *
     * @Route("/new", name="admin_metatag_new")
     * @Template("CopiaincollaMetaTagsBundle:MetaTagsAdmin:edit.html.twig")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = new Metatag();
        $form = $this->createForm($this->container->get('ci_metatags.metatag_formtype'), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Metatag entity.
     *
     * @Route("/create", name="admin_metatag_create")
     * @Method("post")
     * @Template("CopiaincollaMetaTagsBundle:MetaTagsAdmin:edit.html.twig")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = new Metatag();

        $request = $this->getRequest();

        $form = $this->createForm($this->container->get('ci_metatags.metatag_formtype'), $entity);

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_metatag_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Metatag entity.
     *
     * @Route("/{id}/edit", name="admin_metatag_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metatag entity.');
        }

        $editForm = $this->createForm($this->container->get('ci_metatags.metatag_formtype'), $entity);

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Metatag entity.
     *
     * @Route("/{id}/update", name="admin_metatag_update")
     * @Method("post")
     * @Template("CopiaincollaMetaTagsBundle:MetaTagsAdmin:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metatag entity.');
        }

        $editForm = $this->createForm($this->container->get('ci_metatags.metatag_formtype'), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_metatag_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Metatag entity.
     *
     * @Route("/{id}/delete", name="admin_metatag_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('CopiaincollaMetaTagsBundle:Metatag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Metatag entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_metatag'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }
}
