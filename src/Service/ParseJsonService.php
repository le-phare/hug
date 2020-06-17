<?php

namespace Hug\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Yaml\Yaml;

class ParseJsonService
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var string
     */
    private $path;

    public function __construct()
    {
        $this->logger = new Logger('hugLog');
        $this->path = './fichierGoss/goss.yaml';
        $this->logger->pushHandler(new StreamHandler('php://stdout'));
    }

    public function ParseJson(string $ansible, string $composerPath = './tests/mock/composer.json'): void
    {
        $json = file_get_contents($composerPath); //Récupération du contenu du composer.json
        $jsonData = json_decode($json, true); //Convertit la chaîne json en variable PHP
        $extensions = $jsonData['require']; //Recherche de la mention 'require' pour obtenir la liste des extensions
        $keys = \array_keys($extensions);

        if (preg_grep('/^\b(faros-ng)\b/i', $keys))
        {
            $faros = file_get_contents('./templateFaros/templateFaros_10.yaml');
            if (file_exists($this->path)) {
                $this->logger->info('Le fichier goss.yaml existe déjà, il va être supprimé');
            }
            $this->logger->info('Version 10 de Faros détectée');
            file_put_contents($this->path,$faros);
        }
        else if (preg_grep('/^\b(faros)\b/i', $keys))
        {
            $faros = file_get_contents('./templateFaros/templateFaros_9.yaml');
            if (file_exists($this->path)) {
                $this->logger->info('Le fichier goss.yaml existe déjà, il va être supprimé');
            }
            $this->logger->info('Version 9 de Faros détectée');
            file_put_contents($this->path,$faros);
        }
        $value = Yaml::parseFile($this->path);
        $temp = \array_keys($value,"http: http");
        var_dump($temp);


        $array = preg_grep('/^ext-/i', $keys);
        try {
            $url = $this->getHost($ansible);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return;
        }

        $template = Yaml::dump([
            'http' => [
                $url => [
                    'status' => 200,
                    'body' => \array_values($array),
                ],
            ],
        ], 4, 2);
        file_put_contents($this->path, $template);
    }

    /**
     * @param string $ansible
     * @return string
     * @throws \Exception
     */
    private function getHost(string $ansible): string
    {
        if (!file_exists($ansible)) {
            throw new \Exception('Le fichier Ansible est introuvable');
        }
        $temp1 = file_get_contents($ansible);
        if (1 == empty($temp1)) {
            throw new \Exception('Le fichier Ansible est vide');
        }
        $temp2 = explode(PHP_EOL, $temp1);
        $url = $temp2[1];
        $this->logger->info('Url de la machine à tester', ['url :' => $url]);

        return $url;
    }
}
