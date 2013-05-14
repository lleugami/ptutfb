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
	$user = $this->container->get('metinet.manager.fbuser')->findUserByFbId('me');
        //$friends = $this->container->get('metinet.manager.fbuser')->getUserFriends("me");
        $repository = $this->getDoctrine()->getRepository('MetinetFacebookBundle:User');
        $classement = $repository->getClassement($user['id']);
        echo 'Classement '.$classement;
        return array(null);
    }

    /**
     * @Route("/log", name="log")
     * @Template()
     */
    public function adminAction()
    {
        return array();
    }


}
