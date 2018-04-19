<?php namespace App\Amqp\TasksBundle\DependencyInjection\Compiler;

use App\Amqp\TasksBundle\Registry\RegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAmqpTasksPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(RegistryInterface::class)) {
            return;
        }

        $definition = $container->findDefinition(RegistryInterface::class);

        $taggedServices = $container->findTaggedServiceIds('amqp_tasks.task');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerTask', array(new Reference($id)));
        }
    }
}