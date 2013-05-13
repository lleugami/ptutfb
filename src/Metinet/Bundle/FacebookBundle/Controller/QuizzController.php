<?php

namespace Metinet\Bundle\FacebookBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Metinet\Bundle\FacebookBundle\Entity\Quizz;
use Metinet\Bundle\FacebookBundle\Form\QuizzType;

/**
 * Quizz controller.
 *
 * @Route("/admin/quizz")
 */
class QuizzController extends Controller
{
    /**
     * Lists all Quizz entities.
     *
     * @Route("/", name="quizz")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MetinetFacebookBundle:Quizz')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Quizz entity.
     *
     * @Route("/", name="quizz_create")
     * @Method("POST")
     * @Template("MetinetFacebookBundle:Quizz:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Quizz();
        $form = $this->createForm(new QuizzType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->upload();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('quizz_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Quizz entity.
     *
     * @Route("/new", name="quizz_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Quizz();
        $form   = $this->createForm(new QuizzType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Quizz entity.
     *
     * @Route("/{id}", name="quizz_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Quizz entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Quizz entity.
     *
     * @Route("/{id}/edit", name="quizz_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Quizz entity.');
        }

        $editForm = $this->createForm(new QuizzType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Quizz entity.
     *
     * @Route("/{id}", name="quizz_update")
     * @Method("PUT")
     * @Template("MetinetFacebookBundle:Quizz:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Quizz entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new QuizzType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $entity->upload();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('quizz_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Quizz entity.
     *
     * @Route("/{id}", name="quizz_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Quizz entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('quizz'));
    }

    /**
     * Creates a form to delete a Quizz entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
