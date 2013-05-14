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
    	$tot = $repository->createQueryBuilder('a')
 				->select('COUNT(a)')
 				->getQuery()
 				->getSingleScalarResult();;
    	
    	$datesept = date("Y-m-d H:i:s", strtotime("-7 days"));
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:User');;
    	$totsept = $repository->createQueryBuilder('a')
    	->select('COUNT(a)')
    	->where('a.createdAt >= :created_at')
    	->setParameter('created_at', $datesept)
    	->getQuery()
    	->getSingleScalarResult();;
    	
    	$datetrente = date("Y-m-d H:i:s", strtotime("-30 days"));
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:User');;
    	$tottrente = $repository->createQueryBuilder('a')
    	->select('COUNT(a)')
    	->where('a.createdAt >= :created_at')
    	->setParameter('created_at', $datetrente)
    	->getQuery()
    	->getSingleScalarResult();;
    	
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:Quizz');;
    	$totquizz = $repository->createQueryBuilder('a')
    	->select('COUNT(a)')
    	->getQuery()
    	->getSingleScalarResult();;
    	
    	
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:QuizzResult');;
    	$somme = $repository->createQueryBuilder('a')
    	->select('SUM(a.winPoints)')
    	->getQuery()
    	->getSingleScalarResult();;
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
    	
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:QuizzResult');;
    	$totquizzlance = $repository->createQueryBuilder('a')
    	->select('COUNT(a)')
    	->where('a.dateEnd = :date_end')
    	->setParameter('date_end', '')
    	->getQuery()
    	->getSingleScalarResult();;
    	
		$arrayquizz = $repository->getTopQuizzPopulaires(3, "DESC");
		foreach ($arrayquizz as $quizz) {
			echo $quizz->getId();
			
		}
    	return array('nombretotal' => $tot, 'nombretotalsept' => $totsept, 'nombretotaltrente' => $tottrente, 'nbquizz' => $totquizz, 'scoremoyen' => $resultatmoyen, 'totquizzlancer' => $totquizzlance);
    }

}
