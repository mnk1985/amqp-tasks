<?php namespace App\Amqp\TasksBundle\Command;

use App\Amqp\TasksBundle\Manager\TaskManagerInterface;
use App\Amqp\TasksBundle\Registry\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunTaskWorker extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:run-worker')
            ->addArgument(
                'queueName',
                InputArgument::REQUIRED,
                'queueName for execution'
            )
            ->addOption(
                'iterations',
                null,
                InputOption::VALUE_OPTIONAL,
                'Override default iterations count'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueName = $input->getArgument('queueName');

        if (!$this->getContainer()->has(RegistryInterface::class)) {
            $output->writeln(sprintf(
                '<error>[%s] not found task registry</error>',
                date('Y-m-d H:i:s')
            ));
            return;
        }

        if (!$this->getContainer()->has(TaskManagerInterface::class)) {
            $output->writeln(sprintf(
                '<error>[%s] not found task manager</error>',
                date('Y-m-d H:i:s')
            ));
            return;
        }

        /**  @var TaskManagerInterface $taskManager*/
        $taskManager = $this->getContainer()->get(TaskManagerInterface::class);

        $options = [];

        if ($input->getOption('iterations')) {
            $options['iterations'] = $input->getOption('iterations');
        }

        if ($input->getOption('verbose')) {
            $options['verbose'] = true;
        }

        $taskManager->consume($queueName, $options);
    }
}