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
    	
    	return array('nombretotal' => $tot, 'nombretotalsept' => $totsept, 'nombretotaltrente' => $tottrente);
    }

}
