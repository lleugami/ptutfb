<?php

namespace Metinet\Bundle\FacebookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     * @Template()
     */
    public function indexAction()
    {
    	$repository = $this->getDoctrine()
    	->getRepository('MetinetFacebookBundle:User');;
    	$users = $repository->findAll();
    	var_dump($users);exit;
        return array();
    }


}
