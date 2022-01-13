<?php

namespace OAuth2\ServerBundle\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateClientCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'OAuth2:CreateClient';

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a OAuth2 client')
            ->addArgument('identifier', InputArgument::REQUIRED, 'The client identifier')
            ->addArgument('redirect_uri', InputArgument::REQUIRED, 'The client redirect uris (comma separated)')
            ->addArgument('grant_types', InputArgument::OPTIONAL, 'Grant types to restrict the client to (comma separated)')
            ->addArgument('scopes', InputArgument::OPTIONAL, 'Scopes to restrict the client to (comma separated)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->container->get('oauth2.client_manager');

        try {
            $client = $clientManager->createClient(
                $input->getArgument('identifier'),
                explode(',', $input->getArgument('redirect_uri')),
                explode(',', $input->getArgument('grant_types')),
                explode(',', $input->getArgument('scopes'))
            );
        } catch (Exception $e) {
            $output->writeln('<fg=red>Unable to create client ' . $input->getArgument('identifier') . '</fg=red>');
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        } catch (\OAuth2\ServerBundle\Exception\ScopeNotFoundException $e) {
            $output->writeln('<fg=red>Scope not found, please create it first</fg=red>');

            return 1;
        }

        $output->writeln('<fg=green>Client ' . $input->getArgument('identifier') . ' created with secret ' . $client->getClientSecret() . '</fg=green>');
    }
}
