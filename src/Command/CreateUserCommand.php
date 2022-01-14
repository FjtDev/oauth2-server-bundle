<?php

namespace OAuth2\ServerBundle\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'OAuth2:CreateUser';

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setName('OAuth2:CreateUser')
            ->setDescription('Create a basic OAuth2 user')
            ->addArgument('username', InputArgument::REQUIRED, 'The users unique username')
            ->addArgument('password', InputArgument::REQUIRED, 'The users password (plaintext)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userProvider = $this->container->get('oauth2.user_provider');

        try {
            $userProvider->createUser($input->getArgument('username'), $input->getArgument('password'));
        } catch (Exception $e) {
            $output->writeln('<fg=red>Unable to create user ' . $input->getArgument('username') . '</fg=red>');

            return;
        }

        $output->writeln('<fg=green>User ' . $input->getArgument('username') . ' created</fg=green>');
    }
}
