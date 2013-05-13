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
    	
    	return array('nombretotal' => $tot);
    }

}
