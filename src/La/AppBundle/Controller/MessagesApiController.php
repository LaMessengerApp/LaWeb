<?php

namespace La\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use La\AppBundle\Entity\Conversation;
use La\AppBundle\Entity\Message;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/// REST API ///

class MessagesApiController extends Controller
{
  /**
    *
    * post message
    *
    * @return array
    * @View()
    */
  public function postMessageAction($var){
    // /api/messages/text=lapin&users[]=7
    // $var =>   users, text, [convId]
    parse_str($var);
    
    $me = $this->container->get('security.context')->getToken()->getUser();

    $em = $this->getDoctrine()->getManager();

    //est ce une new conv
    $newConv = true;

    //si on a un id de conv en param / sinon on regarde si elle existe deja
    if(isset($convId)){
      $conv = new Conversation();
      $conv = $em->getRepository('LaAppBundle:Conversation')->find($convId);
    }else{
      // tester si la conversation n'existe pas deja
      $myConv = $me->getConversations();
      foreach ($myConv as $key => $c) {
        foreach ($c->getUsers() as $k2 => $u) {
          foreach ($users as $k3 => $u2) {
            if($u->getId() == $u2){
              $conv = new Conversation();
              $conv = $em->getRepository('LaAppBundle:Conversation')->find($c->getId());
              $newConv = false;
            }
          }
        }
      }
    }
    
    //creation de la conversation
    if($newConv == true){
      $conv = new Conversation();
      foreach ($users as $k => $v) {
        $user = $em->getRepository('LaUserBundle:User')->find($v);
        $conv->addUser($user);
      }
      $conv->addUser($me);
    }
    

    //creation du message
    $mess = new Message();
    $mess->setAuthor($me);
    $mess->setConversation($conv);
    $mess->setText($text);

  
    $em->persist($conv);
    $em->persist($mess);
    // Envoi BDD
    $em->flush();

    return array('author' => $me, 'text' => $text, 'users' => $conv->getUsers(), 'newConv' => $newConv);
  }

  /**
    *
    * get user conversations
    *
    * @return array
    * @View()
    */
  public function getConversationsAction(){
    $me = $this->container->get('security.context')->getToken()->getUser();
    $em = $this->getDoctrine()->getManager();

    $conversations = $me->getConversations();
    foreach ($conversations as $k => $c) {
      $conversations->messages[] = $em->getRepository('LaAppBundle:Message')->findByConversation($c->getId());
    }

    $conv = new array();
    $conv = $conversations;
    return array('conversations' => $conv, 'mess' => $conversations->messages);
  }
}

