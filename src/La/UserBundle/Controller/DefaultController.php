<?php

namespace La\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use La\UserBundle\Entity\User;
use La\UserBundle\Entity\Repository\UserRepository;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	if($user == "anon."){
    		return $this->redirect('../login');
    	}
		//utilisation de repository
		//$friends = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findFriends($user);
    	
    	$friends = $user->getFriendships();
    	//return new Response($friends[0]);
        return $this->render('LaUserBundle:Default:index.html.twig', array('friends' => $friends));
    }

    public function add_friendAction($newFriendId)
    {
    	//on recupere l'user courant
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	/* ESSAI DE SERVICE FAILED
    	// appel du service userService
    	$userService= $this->container->get('la_user.userService');
    	//on utilise la fonction addFriendship du service userService
    	$em = $this->getDoctrine()->getManager();
    	$test = $userService->addFriendship($em,1,2);
    	return new Response($test);
    	*/
    	

    	// On récupère le repository
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('LaUserBundle:User')
		;

		// On récupère l'entité correspondante à l'id $id
		$newFriend = $repository->find($newFriendId);

		

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

        // Envoi BDD
        $em->flush();

        return $this->redirect($this->generateUrl('la_user_homepage'));
        
    }

    public function valid_friendshipAction($friendId)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	// On récupère le repository
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('LaUserBundle:User')
		;

		// On récupère l'entité correspondante à l'id $id
		$friend = $repository->find($friendId);

		foreach ($user->getFriendships() as $key => $friendship) {
			if ( $friendship->getUser2() == $friend )
			{
				if ( $friendship->getStatus() == 1 )
				{
					$friendship->setStatus(2);
					foreach ($friend->getFriendships() as $key => $value) {
						if ($value->getUser2() == $user) {
							$value->setStatus(2);
						}
					}
				}
			}
		}
		$em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->persist($friend);

        // Envoi BDD
        $em->flush();

        return $this->redirect($this->generateUrl('la_user_homepage'));
    }
    public function delete_friendshipAction($friendId)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	// On récupère le repository
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('LaUserBundle:User')
		;
		$em = $this->getDoctrine()->getManager();
		// On récupère l'entité correspondante à l'id $id
		$friend = $repository->find($friendId);

		foreach ($user->getFriendships() as $key => $friendship) {
			if ( $friendship->getUser2() == $friend )
			{
				$em->remove($friendship);
				foreach ($friend->getFriendships() as $key => $value) {
					if ($value->getUser2() == $user) {
						$em->remove($value);
					}
				}
			}
		}
        // Envoi BDD
        $em->flush();

        return $this->redirect($this->generateUrl('la_user_homepage'));
    }
}
