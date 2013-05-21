<?php

namespace Metinet\Bundle\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
	
      /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {

        $themes = $this->showThemeAction();
        $nb_quizz = $this->countQuizzAction();;

        $userFb= $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));

        $friends = $this->container->get('metinet.manager.fbuser')->getUserFriendsUsingApp('me');
        
        
        $classementAvecAmis = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->getClassementAvecAmis($friends,$user->getId());
        
        /* Classement */
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User');
        $classement = $repository->getClassement($userFb['id']);
        
        /* Dernier Quizz */
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Quizz');
        $listeDernierQuizz = $repository->quatreDernierQuizz();
        $dernierQuizzPromo = $repository->dernierQuizzPromo();
               
        return array('classement' => $classement, 'classementAvecAmis' => $classementAvecAmis, 'listeDernierQuizz' => $listeDernierQuizz, 'dernierQuizzPromo' => $dernierQuizzPromo,'themes' => $themes,'nb_quizz' => $nb_quizz);
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
     * @Route("/detailquizz/{id}", name="detail")
     * @Template()
     */
    public function detailsquizzAction($id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('MetinetFacebookBundle:Quizz')->find($id);
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Quizz entity.');
    	}
    	$repositoryQuestion = $em->getRepository('MetinetFacebookBundle:Question');
    	$countQuestion = $repositoryQuestion->getCountQuestionsByQuizz($entity->getId());
    	
    	$repositoryUsers = $em->getRepository('MetinetFacebookBundle:User');
    	$userFb= $this->container->get('metinet.manager.fbuser')->getUserFb();
    	$user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));
    	$friends = $this->container->get('metinet.manager.fbuser')->getUserFriendsUsingApp('me');
    	$classementAmis = $repositoryUsers->getClassementAvecAmisByQuizz($friends, $user->getId(), $id);
    	return array('quizz' => $entity, 'countQuestion' =>	$countQuestion);
    }

 
    /**
     * @Route("/{id}", name="list_quizz")
     * @Template()
     */
    public function listAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MetinetFacebookBundle:Quizz')->getQuizzTrier($id);

        if (!$entity) 
        {
             return $this->redirect($this->generateUrl('index'));
        }

        foreach ($entity as $key => $value) 
        {
            $titre_theme =  $value->getTheme()->getTitle();
            $img_theme =  $value->getTheme()->getPicture();
            $desc_theme =  $value->getTheme()->getLongDesc();
            $entity2 = $em->getRepository('MetinetFacebookBundle:Question')->findBy(array('quizz' => $value->getId()));
            $nb_question[$value->getId()] = count($entity2);

            $entity3 = $em->getRepository('MetinetFacebookBundle:QuizzResult')->findBy(array('quizz' => $value->getId()));
        
            $user[$value->getId()] = null;
            foreach ($entity3 as $key => $value3) 
            {
                $user[$value->getId()][] =  $value3->getUser()->getPicture();
            }

        }

        $nb_quizz = count($entity);

        return  array('quizz' => $entity,'titre_theme' => $titre_theme,'nb_quizz' => $nb_quizz,'img_theme' => $img_theme,'desc_theme' => $desc_theme,'nb_question' => $nb_question, 'user' => $user);
    }

}
