<?php

namespace Metinet\Bundle\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	
      /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
	$user = $this->container->get('metinet.manager.fbuser')->getUserFb();
        //$friends = $this->container->get('metinet.manager.fbuser')->getUserFriends("me");
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User');
        $classement = $repository->getClassement($user['id']);

        $themes = $this->showThemeAction();

        return array('classement' => $classement,'themes' => $themes);
    }

    /**
     * @Route("/log", name="log")
     * @Template()
     */
    public function adminAction()
    {
        return array();
    }


    public function showThemeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Theme')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }


                
        foreach ($entity as $key => $value) {
            if($value->getQuizzes() != "")
            {
                $tab[] = $value->getTitle();
            }
           
        }

        print_r($tab);
        exit();



        return $entity;
    }


    /**
     * @Route("/{id}", name="list_quizz")
     * @Template()
     */
    public function listAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->findBy(array('theme' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Quizz entity.');
        }
        return  array('quizz' => $entity);
    }

}
