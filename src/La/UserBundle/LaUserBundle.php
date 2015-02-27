<?php

namespace La\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use La\UserBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LaUserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }

    public function getParent(){
        return 'FOSUserBundle';
    }
}
