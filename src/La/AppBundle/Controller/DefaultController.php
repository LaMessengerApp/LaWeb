<?php

namespace La\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use La\AppBundle\Entity\Conversation;
use La\AppBundle\Entity\Message;
use La\UserBundle\Entity\Friendship;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //landing page 
        $me = $this->getUser();
        if(!is_null($me)){
            return $this->redirect($this->generateUrl('la_app_conversations'));
        }   
        return $this->render('LaAppBundle:Default:index.html.twig');
        
        //return $this->redirect($this->generateUrl('la_app_conversations'));
    }
    public function meAction()
    {
        $me = $this->getUser();
        if(is_null($me)){
            return $this->redirect('login');
        }
        $em = $this->getDoctrine()->getManager();
        $conversations = $this->get("message_api_controller")->getConversationsAction();
        $friends = $me->getFriendships();
        
        return $this->render('LaAppBundle:Default:me.html.twig', array('me' => $me, 'userPicture' => $me->getPictureName(), 'conversations' => $conversations['conversations'], 'friends' => $friends));
    }
}
