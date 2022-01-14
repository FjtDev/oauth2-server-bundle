<?php

namespace OAuth2\ServerBundle\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateScopeCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'OAuth2:CreateScope';

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(self::$defaultName);
    }
    protected function configure()
    {
        $this
            ->setDescription('Create a scope for use in OAuth2')
            ->addArgument('scope', InputArgument::REQUIRED, 'The scope key/name')
            ->addArgument('description', InputArgument::REQUIRED, 'The scope description used on authorization screen')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scopeManager = $this->container->get('oauth2.scope_manager');

        try {
            $scopeManager->createScope($input->getArgument('scope'), $input->getArgument('description'));
        } catch (Exception $e) {
            $output->writeln('<fg=red>Unable to create scope ' . $input->getArgument('scope') . '</fg=red>');

            return;
        }

        $output->writeln('<fg=green>Scope ' . $input->getArgument('scope') . ' created</fg=green>');
    }
}
