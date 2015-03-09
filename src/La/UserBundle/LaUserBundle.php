<?php

namespace La\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LaUserBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function getParent(){
        return 'FOSUserBundle';
    }
}
