<?php

namespace La\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use La\UserBundle\Entity\User;
use La\UserBundle\Entity\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/// REST API ///

class UsersApiController extends Controller
{
  /**
  * @return array
  * @View()
  */
  public function getUsersAction(){
    $user = $this->container->get('security.context')->getToken()->getUser();
    	if($user == "anon."){
    		return $this->redirect('../login');
    	}
    $friends = $user->getFriendships();
    $users = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findAll();
    $user = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->find(1);

    //$users = $this->getRepository('LaUserBundle:User')->findAll();
    return array('users' => $users);
  }
  /**
   * @param User $user
   * @return array
   * @View()
   * @ParamConverter("user", class="LaUserBundle:User")
   */
  public function getUserAction(User $user){
    return array('user' => $user);
  }
}

