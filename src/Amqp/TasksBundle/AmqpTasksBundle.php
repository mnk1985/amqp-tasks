<?php namespace App\Amqp\TasksBundle;

use App\Amqp\TasksBundle\DependencyInjection\Compiler\RegisterAmqpTasksPass;
use App\Amqp\TasksBundle\Tasks\TaskInterface;
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
}