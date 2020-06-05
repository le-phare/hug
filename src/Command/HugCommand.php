<?php

namespace Hug\Command;

use Hug\Service\ParseJson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
        'Génération du goss_projet.yaml',
        '==============================',
        '',
    ]);
        $this->parseJson->ParseJson();
        // TODO: si fichier écrasé, le précisé dans les logs
        return Command::SUCCESS;
    }
}
