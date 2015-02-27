<?php
namespace La\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use La\UserBundle\Security\Authentication\Token\WsseUserToken;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class WsseListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';
        if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
            return;
        }

        $token = new WsseUserToken();
        $token->setUser($matches[1]);

        $token->digest   = $matches[2];
        $token->nonce    = $matches[3];
        $token->created  = $matches[4];
        //var_dump($token);
        try {
            var_dump("try / token :".$token);
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
            return;
        } catch (AuthenticationException $failed) {
            var_dump("catch");
            // ... you might log something here
            $failedMessage = "WSSE Login failed for ".$token->getUsername().". Why ? ".$failed->getMessage();

            // To deny the authentication clear the token. This will redirect to the login page.
            // $this->securityContext->setToken(null);
            // return;

            // Deny authentication with a '403 Forbidden' HTTP response
            $response = new Response();
            $response->setStatusCode(403);
            $response->setContent($failedMessage);
            $event->setResponse($response);
            return;
        }
        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}