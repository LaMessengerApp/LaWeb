<?php

namespace La\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use La\AppBundle\Entity\Conversation;
use La\AppBundle\Entity\Message;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('la_user_homepage'));
        //return $this->render('LaAppBundle:Default:index.html.twig', array('name' => $name));
    }
    public function meAction()
    {
        $me = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $conversations = $this->get("message_api_controller")->getConversationsAction();
        $friends = $me->getFriendships();
        return $this->render('LaAppBundle:Default:me.html.twig', array('me' => $me, 'conversations' => $conversations['conversations'], 'friends' => $friends));
    }
}
