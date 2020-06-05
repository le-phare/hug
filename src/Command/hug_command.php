<?php

namespace Hug\Command;

use Hug\Parse\parseJson;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class hug_command extends Command
{
	public static $defaultName ='hug:generate';

	    public function __construct()
    {
        parent::__construct();
    }

	protected function configure()
    {
        $this
        	->setDescription('generate')
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
         $array = parseJson();
         $yaml = Yaml::dump($array);

        file_put_contents('./fichierGoss/goss_projet.yaml', $yaml, FILE_APPEND);
        return Command::SUCCESS;

    }
}
