<?php
namespace SweetCode\MariaBundle;

use SweetCode\MariaBundle\DependencyInjection\CompilerPass\InjectComparators;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MariaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new InjectComparators());
    }
}