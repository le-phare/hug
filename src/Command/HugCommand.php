<?php

namespace Hug\Command;

use Hug\Service\ParseJsonService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription("(TEST CONFIGURATION) - Génère un fichier goss_projet.yaml à partir d'un composer.json")
            ->setHelp('Récupère le composer.json à la racine du projet et vient générer un fichier goss_projet.yaml pour venir le tester par la suite avec Goss')
            ->addOption(
                'ansible-path',
                'a',
                InputOption::VALUE_REQUIRED,
                'Récupérer les variables Ansible du projet'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Génération du goss_projet.yaml',
            '==============================',
        ]);
        $ansible = $input->getOption('ansible-path');
        $this->parseJson->ParseJson($ansible);
        if ($output->isVerbose()) {
            $output->writeln('Fichier goss généré : ');
            $output->writeln(
                file_get_contents('./fichierGoss/goss_projet.yaml'));
        }

        return Command::SUCCESS;
    }
}
