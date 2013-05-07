<?php

namespace Metinet\Bundle\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Metinet\Bundle\FacebookBundle\Entity\Quizz;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin/editerquizz", name="editerQuizz")
     * @Template()
     */
    public function editerQuizzAction(Request $request)
    {
        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $quizz = new Quizz();

        $form = $this->createFormBuilder($quizz)
            ->add('title', 'text')
            ->getForm();

        //return $this->render('MetinetFacebookBundle:Admin:editerQuizz.html.twig', array('form' => $form->createView(),));

       if ($request->isMethod('POST'))
        {
            $quizz->setPicture('image');
            $quizz->setShortDesc('description courte');
            $quizz->setWinPoints(2);
            $quizz->setAverageTime(211);
            $quizz->setTxtWin1('win 1');
            $quizz->setTxtWin2('win 2');
            $quizz->setTxtWin3('win 3');
            $quizz->setTxtWin4('win 4');
            $quizz->setShareWallTitle('Titre publication');
            $quizz->setShareWallDesc('description publication'); 
            $quizz->setIsPromoted(1); 


            $form->bind($request);
        
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($quizz);
                $em->flush();
                return array('form' => $form->createView(),'resultat_id' => $quizz->getId());
            }
            
        }else{
            return array('form' => $form->createView(),'resultat_id' => "null");
        }
    }

    /**
     * @Route("/admin/listerquizz", name="listerQuizz")
     * @Template()
     */
    public function listerQuizzAction()
    {
        
        $quizz = $this->getDoctrine()
        ->getRepository('MetinetFacebookBundle:Quizz')
        ->findAll();

        if (!$quizz)
        {
            throw $this->createNotFoundException('Aucun produit trouvé');
        }else{
            return array('quizz' => $quizz);
        }
    }
    
    /**
     * @Route("/admin/listertheme", name="listerTheme")
     * @Template()
     * 
     */
    public function listerThemeAction()
    {
        $repository = $this->getDoctrine()->getRepository('FacebookBundle:Theme');
        $themes = $repository->findAll();
        
        return array("themes" => $themes);
        
    }

}
