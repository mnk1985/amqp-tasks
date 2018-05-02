<?php namespace AmqpTasksBundle;

use AmqpTasksBundle\DependencyInjection\AmqpTasksExtension;
use AmqpTasksBundle\DependencyInjection\Compiler\RegisterAmqpTasksPass;
use AmqpTasksBundle\Tasks\TaskInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AmqpTasksBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(TaskInterface::class)->addTag('amqp_tasks.task');

        $container->addCompilerPass(new RegisterAmqpTasksPass());

        parent::build($container);
    }

    public function getContainerExtensionClass()
    {
        return AmqpTasksExtension::class;
    }
}