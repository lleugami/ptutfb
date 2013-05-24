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
        //echo $_SERVER['DOCUMENT_ROOT'];

        $themes = $this->showThemeAction();
        $nb_quizz = $this->countQuizzAction();;

        /* ON RECUP L'USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));

        /* ON RECUP LES AMIS DE l'USER */
        $friends = $this->container->get('metinet.manager.fbuser')->getUserFriendsUsingApp('me');
        
        /* CLASSEMENT DE L'USER ET SES AMIS */
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
    	$userconnect = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));
    	$friends = $this->container->get('metinet.manager.fbuser')->getUserFriendsUsingApp('me');
    	$classementAmis = $repositoryUsers->getClassementAvecAmisByQuizz($friends, $userconnect->getId(), $id);
		$useractuelquizz = $this->getDoctrine()->getRepository('MetinetFacebookBundle:QuizzResult')->findOneBy(array('user' => $userconnect->getId(), 'quizz' => $id));
    	$classementGeneral = $repositoryUsers->getClassementTopTenByQuizz($id);
		$counter = 1;
    	$taux = 0;
		if(isset($useractuelquizz)) {
			$resultat = $useractuelquizz->getWinpoints() / $entity->getWinpoints();
			$taux = $resultat * 100;
		}
		
		//var_dump($useractuelquizz);exit; 
    	return array('quizz' => $entity, 'countQuestion' =>	$countQuestion, 'classementAvecAmis' => $classementAmis, 'countclassementamis' => count($classementAmis), 
    			'classementgeneral' => $classementGeneral, 'countclassement' => count($classementGeneral), 'quizzresult' => $useractuelquizz, 'taux'=>$taux, 'counter' =>$counter);
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
                $user[$value->getId()][] =  $value3->getUser();
            }

        }

        $nb_quizz = count($entity);

        return  array('quizz' => $entity,'titre_theme' => $titre_theme,'nb_quizz' => $nb_quizz,'img_theme' => $img_theme,'desc_theme' => $desc_theme,'nb_question' => $nb_question, 'user' => $user);
    }
    
    /**
     * @Route("quizz/{id}", name="start_quizz")
     * @Template()
     */
    public function startQuizzAction($id)
    {
        /* ON RECUP LE USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));

        $quizz = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Quizz')->find($id);
        
        /* ON REGARDE SI USER A DEJA REPONDU AU QUIZZ */
        $quizzResult = $this->getDoctrine()->getRepository('MetinetFacebookBundle:QuizzResult')->findOneBy(array('user' => $user->getId(), 'quizz' => $quizz->getId()));
        
        if(is_object($quizzResult) && $quizzResult->getDateEnd() != null){
            return $this->redirect( $this->generateUrl('detail', array('id' => $quizz->getId())) );
        }
        else{
            return Array('quizz' => $quizz);
        }
    }
    
    /**
     * @Route("questionsofquizz/", name="get_questions")
     * 
     */
    public function getQuestionsOfQuizzAction(){
        
        /* ON RECUP LE USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));

        /* ON RECUP QUIZZ */
        $id = $this->get('request')->request->get('id');
        
        $quizz = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Quizz')->find($id);
        
        /* ON REGARDE SI USER A DEJA REPONDU AU QUIZZ */
        $quizzResult = $this->getDoctrine()->getRepository('MetinetFacebookBundle:QuizzResult')->findOneBy(array('user' => $user->getId(), 'quizz' => $quizz->getId()));
       
        if(!is_object($quizzResult)){
            /* nouveau quizz result */
            $quizzResult = new \Metinet\Bundle\FacebookBundle\Entity\QuizzResult;
            $quizzResult->setDateStart(new \DateTime());
            $quizzResult->setUser($user);        
            $quizzResult->setQuizz($quizz); 
        }
        
        /* ON ENREGISTRE NOUVEAU QUIZZ RESULT */
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($quizzResult);
        $em->flush();
        
        $questions = $quizz->getQuestions();
        
        $return = "";
        
        foreach ($questions as $question){
            
            $nope = 0;
            
            foreach($question->getAnswers() as $answer){
                
                foreach ($user->getAnswers() as $answerUser){
                    if($answer->getId() == $answerUser->getId()){
                        $nope = 1;
                    }
                }
            }
                          
            if($nope != 1){
                if( count($question->getAnswers()) >= 1){
                    $return .= '<div style="position:absolute; top:0; left:0; right:0; bottom:0; background-color: rgba(0,0,0,0.5); z-index: 0;">';
                    $return .= '<form id="form_question" action="#" method="post" style="position:absolute; top: 100px; left: 33%; background-color: white; padding: 10px; margin:auto; border-radius: 10px; box-shadow: 0 0 10px black; width: 33%; z-index: 10;">';
                    $return .= '<center><img src="/uploads/images/mini/question/mini_'.$question->getPicture().'" alt="'.$question->getTitle().'" /></center>';
                    $return .= '<p>'.$question->getTitle().'</p>';
                    foreach ($question->getAnswers() as $answer){
                        $return .= '<div style="width:auto">';
                        $return .= '<label for="answer'.$answer->getId().'" style="text-align:left; width:auto;">'.$answer->getTitle().'</label>';
                        $return .= '<input onclick="ajaxSetAnswer()" id="answer'.$answer->getId().'" type="radio" name="answer" value="'.$answer->getId().'" id="answer'.$answer->getId().'"/>';
                        $return .= '</div>';
                    }
                    $return .= '<input id="id" type="hidden" value="'.$quizz->getId().'"/>';
                    $return .= '</form>';
                    $return .= '</div>|';
                }
            }
        }
        
        $return = substr($return, 0, -1);
        echo $return;
        
        exit();
    }
    
    /**
     * @Route("answerforquestion/", name="set_answer")
     * 
     */
    public function setAnswerForQuestionAction(){
        
        /* ON RECUP L'USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));
        
        $quizzId = $this->get('request')->request->get('id');
        
        /* ON RECUP QUIZZ RESULT */
        $quizzResult = $this->getDoctrine()->getRepository('MetinetFacebookBundle:QuizzResult')->findOneBy(array('user' => $user->getId(), 'quizz' => $quizzId));
        
        
        $answerId = $this->get('request')->request->get('answer');
        
        $answer = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Answer')->find($answerId);
        if($answer->getIsCorrect() == true){
            $quizzResult->setWinPoints($quizzResult->getWinPoints() + 1);
        }
        
        $user->addAnswer($answer);
        
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->persist($quizzResult);
        $em->flush();
        
        
        exit();
    }
    
    /**
     * @Route("resultofquizz/", name="get_result")
     * 
     */
    public function getResultOfQuizz(){
        
        /* ON RECUP LE QUIZZ */
        $quizzId = $this->get('request')->request->get('id');
        
        $quizz = $this->getDoctrine()->getRepository('MetinetFacebookBundle:Quizz')->find($quizzId);
        
        /* ON RECUP L'USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));
        
        /* ON RECUP LE RESULTAT DU QUIZZ */
        $quizzResult = $this->getDoctrine()->getRepository('MetinetFacebookBundle:QuizzResult')->findOneBy(array('user' => $user->getId(), 'quizz' => $quizzId));
        $quizzResult->setDateEnd(new \DateTime());
        
        /* CALCULE TAUX REUSSITE ET NOMBRE DE POINTS GAGNES */
        $nbQuestion = count($quizz->getQuestions());
               
        $tauxReussite = round(($quizzResult->getWinPoints() * 100) / $nbQuestion,0);
        
        $nbWinPoints = round($quizz->getWinPoints() * ($tauxReussite / 100),0); 
        
        $average = $quizzResult->getDateEnd()->getTimestamp() - $quizzResult->getDateStart()->getTimestamp();
        
        /* CALCULE BONUS OU MALUS */
        if($average <= $quizz->getAverageTime() && $tauxReussite >= 75){
            $messageAverage = "Bonus de temps";
            $pointAverage = 0.25 * $quizz->getWinPoints();
        }
        else if($average > $quizz->getAverageTime()){
            $messageAverage = "Malus de temps";
            $pointAverage = (-0.15 * $quizz->getWinPoints());       
        }
        else{
            $messageAverage = "Aucun bonus";
            $pointAverage = 0;
        }
        
        $pointAverage = round($pointAverage,0);
        
        $totalPoints = round($nbWinPoints + $pointAverage,0);
        
        $quizzResult->setWinPoints($totalPoints);
        $quizzResult->setAverage($average);
        
        /* ON AJOUTE LES POINTS GAGNE A L'USER */
        $user->setPoints($user->getPoints() + $totalPoints);
        /* ON ENREGISTRE LE RESULTAT */
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($quizzResult);
        $em->persist($user);
        $em->flush();
        
        /* ON AFFICHE LE MESSAGE */
        if(0 <= $tauxReussite && $tauxReussite < 25){
            
            $message = $quizz->getTxtWin1();       

        }
        else if(25 <= $tauxReussite && $tauxReussite < 50){
            
            $message = $quizz->getTxtWin2();
        }
        else if(50 <= $tauxReussite && $tauxReussite < 75){
            
            $message = $quizz->getTxtWin3();
        }
        else if(75 <= $tauxReussite && $tauxReussite <= 100){
            
            $message = $quizz->getTxtWin4();
        }
        
       
        echo '<table>
                <tr><td>Taux de réussite</td><td>'.$tauxReussite.' % </td></tr>
                <tr><td>Points gagnés</td><td>'.$nbWinPoints.' pts</td></tr>
                <tr><td>Temps écoulé</td><td>'.$average.' s</td></tr>
                <tr><td>Temps estimé</td><td>'.$quizz->getAverageTime().' s</td></tr>
                <tr><td>'.$messageAverage.'</td><td>'.$pointAverage.' pts</td></tr>
            </table><h1>TOTAL : '.$totalPoints.' pts</h1><p>'.$message.'</p>';
                    
        echo "<script> publier('Quizz en Folie', 'http://apps.facebook.com/quizz_en_folie/', 'http://ptutfb1.hebergearea.com/uploads/images/quizz.jpg','Les meilleures Quizz','J\'ai répondu au Quizz : ".$quizz->getTitle()."  Mon score : ".$totalPoints." pts ') </script>";
        
        echo '<center><a href="#" class="button-bevel orange"><input id="button_start" type="button" value="Retour" onclick="location.reload() ;"/></a></center>';
        exit();
    
    }
    
        /**
     * @Route("classement/", name="classement")
     * @Template()
     */
    public function classementAction()
    {
        /* ON RECUP L'USER */
        $userFb = $this->container->get('metinet.manager.fbuser')->getUserFb();

        $user = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->findOneBy(array('fbUid' => $userFb['id']));
       
        /* ON RECUP LES AMIS DE l'USER */
        $friends = $this->container->get('metinet.manager.fbuser')->getUserFriendsUsingApp('me');
        
        /* CLASSEMENT DE L'USER ET SES AMIS */
        $classementAvecAmis = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->getClassementAvecAmis($friends,$user->getId(),0);
       
        $classement = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->getClassementUsers($user);
        
        $classement10 = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User')->getClassementTopTen();
        
        return(array('classementAvecAmis' => $classementAvecAmis , 'classement' => $classement, 'classement10' => $classement10));
    }
}
