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
	 * @Route("/admin", name="admin")
	 * @Template()
	 */
    public function indexAction() {
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:User');;
    	$tot = $repository->getCountAllUsers();
    	$lasttenusers = $repository->getLastTenUsers();
    	$datesept = date("Y-m-d H:i:s", strtotime("-7 days"));
    	$totsept = $repository->getCountDateAllUsers($datesept);
    	$datetrente = date("Y-m-d H:i:s", strtotime("-30 days"));
    	$tottrente = $repository->getCountDateAllUsers($datetrente);
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:Quizz');;
    	$totquizz = $repository->getCountTotQuizz();  	
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:QuizzResult');;
    	$somme = $repository->getSommePoint();
    	if(!isset($somme)) {
    		$somme = 0;
    	} else {
    		$somme = (int)$somme;
    	}
    	
    	if(!isset($tot)) {
    		$tot = 0;
    	} else {
    		$tot = (int)$tot;
    	}
    	$resultatmoyen = $somme/$tot;
    	$totquizzlance = $repository->getCountQuizzLancer();
		$arrayquizzpop = $repository->getTopQuizzPopulaires(3, "DESC");

		$arrayquizzflop = $repository->getTopQuizzPopulaires(3, "ASC");
		
    	return array('nombretotal' => $tot, 'nombretotalsept' => $totsept, 'nombretotaltrente' => $tottrente, 'nbquizz' => $totquizz, 'scoremoyen' => $resultatmoyen, 'totquizzlancer' => $totquizzlance, 'tabquizzpop' => $arrayquizzpop, 'tabquizzflop' => $arrayquizzflop, 'tablasttenpers' => $lasttenusers);
    }

}
