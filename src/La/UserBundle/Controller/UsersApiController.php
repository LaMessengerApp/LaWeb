<?php

namespace La\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use La\UserBundle\Entity\User;
use La\UserBundle\Entity\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use FOS\RestBundle\Controller\Annotations\Route;


/// REST API ///

class UsersApiController extends Controller
{
  protected $container;
  public function __construct(ContainerInterface $container = null)
  {
    $this->container = $container;
  }

  /**
   * @param Username, Password
   * @return array
   * @Route("login")
   * @View()
   */
  public function postLoginAction(Request $request){
    $username = $request->request->get('username');
    $password = $request->request->get('password');

    $em = $this->getDoctrine();
    $user = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findOneByUsername($username);

    if (!$user) {
        //throw new UsernameNotFoundException("User not found");
        return array('fail' => "User not found");
    } else {
        // Get the encoder for the users password
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        if($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())){
          // User + password match
          
          $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
          $this->get("security.context")->setToken($token); //now the user is logged in
           
          //now dispatch the login event
          $request = $this->get("request");
          $event = new InteractiveLoginEvent($request, $token);
          $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        } else {
          // Password bad
          return array('success' => "fail");
        }
        
    }
    return array('success' => "true");
  }

  /**
   * @param Username, Password
   * @return array
   * @Route("newuser")
   * @View()
   */
  public function postNewuserAction(Request $request){
    $username = $request->request->get('username');
    $password = $request->request->get('password');
    $email = $request->request->get('email');

    $em = $this->getDoctrine()->getManager();

    $u = new User();
    $u->setUsername($username);
    $u->setPlainPassword($password);
    $u->setEmail($email);
    $u->setEnabled(1);
    $em->persist($u);
    $em->flush($u);
    
    return array('success' => "true", "username"=> $username, 'mail'=> $email);
  }

  /**
  * @return array
  * @View()
  */
  public function getUsersAction(){
    $user = $this->container->get('security.context')->getToken()->getUser();
    if(!($user instanceof User)){
      throw new UnauthorizedHttpException('', 'The requested resource requires user authentication');
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
   * @param User $user
   * @return array
   * @View()
   * @ParamConverter("user", class="LaUserBundle:User")
   */
  public function getUserbyusernameAction($username){
    $user = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findOneByUsername($username);
    if(is_null($user)){
      return array('user' => null);
    }
    return array('user' => $user);
  }

  /**
   * @return bool
   * @View()
   */
  public function getIsauthentificatedAction(){
    $user = $this->container->get('security.context')->getToken()->getUser();
      if(!($user instanceof User)){
        return 0;
      }
    return 1;
  }

  /**
   * @return array
   * @View()
   * get me
   */
  public function getMeAction(){
    $me = $this->container->get('security.context')->getToken()->getUser();
    if(!($me instanceof User)){
      throw new UnauthorizedHttpException('', 'The requested resource requires user authentication');
    }
    return array('me' => $me);
  }

  /**
   * Post Friend Request // fait une demande d'ami, donc stat 0
   * @param User $user
   * @return array
   * @View()
   * @ParamConverter("newFriend", class="LaUserBundle:User")
   */
  public function postFriendrequestAction(User $newFriend){

    $code = 0;
  	//on recupere l'user courant
  	$user = $this->container->get('security.context')->getToken()->getUser();
    if(!($user instanceof User)){
      throw new UnauthorizedHttpException('', 'The requested resource requires user authentication');
    }
  	// S'ils sont amis, on ne fait rien
  	$isFriend = false;
  	foreach ($user->getFriendships() as $key => $friendship) {
  		if ( $friendship->getUser2() == $newFriend )
  		{
  			$isFriend = true;
        $code = 3;
  			if ( $friendship->getStatus() == 1 )
  			{
  				$friendship->setStatus(2);
  				foreach ($newFriend->getFriendships() as $key => $value) {
  					if ($value->getUser2() == $user) {
  						$value->setStatus(2);
              $code = 2;
  					}
  				}
  			}
  		}
  	}

  	if ($isFriend == false) {
  		$user->addFriend($newFriend);
      $code = 1;
  	}
  	
  	$em = $this->getDoctrine()->getManager();

    $em->persist($user);
    $em->persist($newFriend);

    $em->flush();

    return array('code' => $code, 'user' => $newFriend);
  }

  /**
   * @param imgPath
   * @return array
   * @Route("userpicture")
   * @View()
   */
  public function postUserpictureAction(Request $request){
    $me = $this->container->get('security.context')->getToken()->getUser();
    if(!($me instanceof User)){
      throw new UnauthorizedHttpException('', 'The requested resource requires user authentication');
    }

    $image = $request->files->get('image');

    // générer un nom aléatoire et essayer de deviner l'extension (plus sécurisé)
    $extension = $image->guessExtension();
    $imgName = substr($image->getClientOriginalName(), 0, strlen($image->getClientOriginalName())-strlen($extension)-1);

    if (!$extension) {
        // l'extension n'a pas été trouvée
        $extension = 'bin';
    }
    $dir =  __DIR__.'/../../../../web/userimages';
    $newImgName = $imgName.'-'.rand(1, 999999).'.'.$extension;
    $image->move($dir, $newImgName);

    $me->setPictureName($newImgName); 
    $em = $this->getDoctrine()->getManager();
    $em->persist($me);
    $em->flush();

    return array('success' => "true", "file" => $newImgName);
  }
}

