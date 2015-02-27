<?php
namespace La\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use La\UserBundle\Security\Authentication\Token\WsseUserToken;

class WsseProvider implements AuthenticationProviderInterface 
{
    private $userProvider;
    private $cacheDir;

    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir     = $cacheDir;
    }

    public function authenticate(TokenInterface $token)
    {
var_dump("authenticate");
        //$user = $this->get("user_api_controller")->getUsersAction();
        //$user = $this->getDoctrine()->getManager()->getRepository('LaUserBundle:User')->findOneByUsername($username);
        $user = $this->userProvider->loadUserByUsername($token->getUsername());
var_dump("user");
        if(!$user){
            throw new AuthenticationException("Bad credentials... Did you forget your username ?");
        }
        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);
            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    protected function validateDigest($digest, $nonce, $created, $secret)
    {
var_dump("validateDigest");
        //check created time is not in the future
        if(strtotime($created) > time()){
            throw new AuthenticationException("Back to the future...");
        }

        // Expire le timestamp après 5 minutes
        if (time() - strtotime($created) > 300) {
            throw new AuthenticationException("Too late for this timestamp... Watch your watch.");
        }

        // Valide que le nonce est unique dans les 5 minutes
        if (file_exists($this->cacheDir.'/'.$nonce) && file_get_contents($this->cacheDir.'/'.$nonce) + 300 > time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }
        //if cache directory does not exist we create it
        if(!is_dir($this->cacheDir)){
            mkdir($this->cacheDir, 0777, true);
        }

        file_put_contents($this->cacheDir.'/'.$nonce, time());

        // Valide le Secret
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));


        if($digest !== $expected){
            throw new AuthenticationException("Bad credentials ! Digest is not as expected.");
        }

        return true;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}