<?php

namespace Hug\Command;

use Hug\Service\ParseJsonService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class HugCommand extends Command
{
    public static $defaultName = 'hug';
    /**
     * @var ParseJsonService
     */
    private $parseJson;

    public function __construct(ParseJsonService $parseJson)
    {
        parent::__construct();
        $this->parseJson = $parseJson;
    }

    protected function configure(): void
    {
        $this
            ->setDescription("(TEST CONFIGURATION) - Génère un fichier goss.yaml à partir d'un composer.json")
            ->setHelp('Récupère le composer.json à la racine du projet et vient générer un fichier goss.yaml pour venir le tester par la suite avec Goss')
            ->addOption(
                'ansible-host-file',
                'a',
                InputOption::VALUE_REQUIRED,
                'Récupérer les variables Ansible du projet',
                'ansible/preprod/hosts'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);
        $ansible = $input->getOption('ansible-host-file');

        if (!is_file($ansible)) {
            $logger->critical("Le paramètre {ansible} n'est pas un fichier", [
                'ansible' => $ansible,
            ]);

            return Command::FAILURE;
        }

        if (!$this->parseJson->ParseJson($ansible)) {
            return Command::FAILURE;
        }

        $logger->notice("Fichier goss généré : \n{goss_file}", [
            'goss_file' => file_get_contents('./generatedFiles/goss.yaml'),
        ]);

        return Command::SUCCESS;
    }
}
