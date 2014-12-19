<?php

namespace La\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('la_user_homepage'));
        //return $this->render('LaAppBundle:Default:index.html.twig', array('name' => $name));
    }
    public function conversationsAction()
    {
        return $this->render('LaAppBundle:Default:conversations.html.twig', array());
    }
    public function addMessageAction($conversation ,$text)
    {
        return $this->render('LaAppBundle:Default:conversations.html.twig', array());
    }
    public function addConversationAction($users)
    {
    	/*$users = array(
    		$this->get('doctrine')->getRepository('AppBundle:User')->findOneById(1),
    		$this->get('doctrine')->getRepository('AppBundle:User')->findOneById(2),
    	);
    	$conversation = $this->get('conversation')->add($users, $myText, time());*/
    	/*$conversation = new Conversation();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$conversation.addUser($user);
    	foreach ($users as $key => $value) {
    		$conversation.addUser($value);
    	}
    	return $this->redirect($this->generateUrl('la_app_conversation'));*/
        //return $this->render('LaAppBundle:Default:conversations.html.twig', array());
        return new Response($users);
    }
}
