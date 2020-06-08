<?php

namespace Hug\Command;

use Hug\Service\ParseJson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HugCommand extends Command
{
    public static $defaultName = 'hug';

    private $parseJson;

    public function __construct(ParseJson $parseJson)
    {
        parent::__construct();
        $this->parseJson = $parseJson;
    }

    protected function configure()
    {
        $this
            ->setDescription("(TEST CONFIGURATION) - Génère un fichier goss_projet.yaml à partir d'un composer.json")
            ->setHelp('Récupère le composer.json à la racine du projet et viens générer un fichier goss_projet.yaml pour venir le tester par la suite avec Goss')
            ->addOption(
                'ansible-path',
                'anspath',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                "Récupérer les variables d'environnement Ansible du projet"
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
        // TODO: si fichier écrasé, le précisé dans les logs
        return Command::SUCCESS;
    }
}
