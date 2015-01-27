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
    //$friends = $user->getFriendships();
    $users = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findAll();
    //$user = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->find(1);

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

  /**
   * Post Friend Request // fait une demande d'ami, donc stat 0
   * @param User $user
   * @return array
   * @View()
   * @ParamConverter("newFriend", class="LaUserBundle:User")
   */
  public function postFriendrequestAction(User $newFriend){
  	// /api/friendrequests/53

  	//on recupere l'user courant
	$user = $this->container->get('security.context')->getToken()->getUser();

	// S'ils sont amis, on ne fait rien
	$isFriend = false;
	foreach ($user->getFriendships() as $key => $friendship) {
		if ( $friendship->getUser2() == $newFriend )
		{
			$isFriend = true;
			if ( $friendship->getStatus() == 1 )
			{
				$friendship->setStatus(2);
				foreach ($newFriend->getFriendships() as $key => $value) {
					if ($value->getUser2() == $user) {
						$value->setStatus(2);
					}
				}
			}
		}
	}

	if ($isFriend == false) {
		$user->addFriend($newFriend);
	}
	
	$em = $this->getDoctrine()->getManager();

    $em->persist($user);
    $em->persist($newFriend);

    $em->flush();

    return array('code' => 1, 'user' => $newFriend);
  }
}

