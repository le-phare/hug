<?php

namespace Hug\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Yaml\Yaml;

class ParseJson
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('hugLog');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
    }

    public function ParseJson(string $ansible): void
    {
        $json = file_get_contents('./tests/composer.json'); //Récupération du contenu du composer.json
        $jsonData = json_decode($json, true); //Convertit la chaîne json en variable PHP
        $extensions = $jsonData['require']; //Recherche de la mention 'require' pour obtenir la liste des extensions
        $keys = array_keys($extensions);
        $pattern = '/^ext-/i';
        $array = preg_grep($pattern, $keys);
        $yaml = array_values($array);
        $url = $this->getHost($ansible);

        if (file_exists('./fichierGoss/goss_projet.yaml')) {
            $this->logger->info('Le fichier goss_projet.yaml existe déjà, il va être supprimé');
        }
        $template = Yaml::dump([
            'http' => [
                $url => [
                    'status' => 200,
                    'body' => $yaml,
                ],
            ],
        ], 4, 2);
        file_put_contents('./fichierGoss/goss_projet.yaml', $template);
    }

    private function getHost(string $ansible): string
    {
        $temp1 = file_get_contents($ansible);
        $temp2 = explode(PHP_EOL, $temp1);
        $url = $temp2[1];
        $this->logger->info('Url de la machine à tester', ['url :' => $url]);

        return $url;
    }
}
