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
  public function postMessageAction($varUrl){
    // /api/messages/text=lapin&users[]=7&lat=13,6123&long=34,628278
    //  /!\ . = &#46
    // $var =>   users, text, [convId]
    parse_str($varUrl, $var);
    
    $me = $this->container->get('security.context')->getToken()->getUser();

    $em = $this->getDoctrine()->getManager();

    //est ce une new conv
    $newConv = true;

    //si on a un id de conv en param / sinon on regarde si elle existe deja
    if(isset($var['convId'])){
      $conv = new Conversation();
      $conv = $em->getRepository('LaAppBundle:Conversation')->find($var['convId']);
    }else{
      // tester si la conversation n'existe pas deja
      $myConv = $me->getConversations();
      foreach ($myConv as $key => $c) {
        foreach ($c->getUsers() as $k2 => $u) {
          foreach ($var['users'] as $k3 => $u2) {
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
      foreach ($var['users'] as $k => $v) {
        $user = $em->getRepository('LaUserBundle:User')->find($v);
        $conv->addUser($user);
      }
      $conv->addUser($me);
      $em->persist($conv);
    }
    

    //creation du message
    $mess = new Message();
    $mess->setAuthor($me);
    $mess->setConversation($conv);
    $mess->setText($var['text']);
    $mess->setLatitude(str_replace(",", ".", $var['lat']));
    $mess->setLongitude(str_replace(",", ".", $var['long']));
    $mess->setStatus(0);
    //dans l'url on envoi un nombre avec des virgules, donc on les remplace par des points

    $em->persist($mess);
    // Envoi BDD
    $em->flush();

    return array('author' => $me, 'text' => $var['text'], 'users' => $conv->getUsers(), 'newConv' => $newConv, 'latitude' => $var['lat'], 'longitude' => $var['long'], 'status' => $mess->getStatus());
  }

  /**
    *
    * get conversations
    *
    * @return array
    * @View()
    */
  public function getConversationsAction(){
    // /api/conversations
    $me = $this->container->get('security.context')->getToken()->getUser();
    $em = $this->getDoctrine()->getManager();

    //$mess = $em->getRepository('LaAppBundle:Message')->findByAuthor($me->getId());

    $conv = $me->getConversations();
    foreach ($conv as $k => $c) {
      $conversations[$k]['id'] = $c->getId();
      $conversations[$k]['users'] = $c->getUsers();
      $conversations[$k]['messages'] = $em->getRepository('LaAppBundle:Message')->findByConversation($c->getId());
    }


    //$conversations = $conv;
    return array('conversations' => $conversations);
  }

  /**
    *
    * put message
    *
    * @return array
    * @View()
    */
  public function putMessageAction($varUrl){
    // /api/messages/id=28&text=lapin&lat=13,6123&long=34,628278&status=1
    parse_str($varUrl, $var);

    $me = $this->container->get('security.context')->getToken()->getUser();
    $em = $this->getDoctrine()->getManager();
    $message = $em->getRepository('LaAppBundle:Message')->find($var['id']);

    if(isset($var['text'])){
      $message->setText($var['text']);
    }
    if(isset($var['lat'])){
      $message->setLatitude(str_replace(",", ".", $var['lat']));
    }
    if(isset($var['long'])){
      $message->setLongitude(str_replace(",", ".", $var['long']));
    }
    if(isset($var['status'])){
      $message->setStatus($var['status']);
    }

    $em->persist($message);
    $em->flush();

    return array('code' => 1, 'var' => $var, 'message' => $message);
  }
}

