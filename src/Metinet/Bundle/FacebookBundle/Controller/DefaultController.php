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
<<<<<<< HEAD
	   $user = $this->container->get('metinet.manager.fbuser')->getUserFb();
        //$friends = $this->container->get('metinet.manager.fbuser')->getUserFriends("me");
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User');
        $classement = $repository->getClassement($user['id']);

        $themes = $this->showThemeAction();
        $nb_quizz = $this->countQuizzAction();;

        return array('classement' => $classement,'themes' => $themes,'nb_quizz' => $nb_quizz);
=======
	$userFb= $this->container->get('metinet.manager.fbuser')->getUserFb();
        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));

        //$friends = $this->container->get('metinet.manager.fbuser')->getClassementAvecAmis($user->getFbUid());
        
        /* Classement */
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User');
        $classement = $repository->getClassement($userFb['id']);
        
        /* Dernier Quizz */
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Quizz');
        $listeDernierQuizz = $repository->quatreDernierQuizz();
        $dernierQuizzPromo = $repository->dernierQuizzPromo();
               
        return array('classement' => $classement, 'listeDernierQuizz' => $listeDernierQuizz, 'dernierQuizzPromo' => $dernierQuizzPromo);
>>>>>>> e99111f61577a4aa61f730fd5ccb5345e646915a
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

        return $entity;
    }

    public function countQuizzAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Theme')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }else{
            foreach ($entity as $key => $value) {
                $tab[$value->getId()] = count($value->getQuizzes());
            }
        }
        return $tab;
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
