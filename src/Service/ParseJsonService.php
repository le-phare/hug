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
        $this->path = './generatedFiles/goss.yaml';
        $this->logger->pushHandler(new StreamHandler('php://stdout'));
    }

    public function ParseJson(string $ansible, string $composerPath = './composer.json'): bool
    {
        if (!file_exists($composerPath)) {
            $this->logger->error('Le fichier composer.json est introuvable');

            return false;
        }
        $json = file_get_contents($composerPath); //Récupération du contenu du composer.json
        $jsonData = json_decode($json, true); //Convertit la chaîne json en variable PHP
        if (!\array_key_exists('require', $jsonData)) {
            $this->logger->error('Aucune mention require dans le projet');

            return false;
        }
        $extensions = $jsonData['require']; //Recherche de la mention 'require' pour obtenir la liste des extensions
        $keys = \array_keys($extensions);
        $farosVersion = $this->getFarosVersion($keys);
        if (null !== $farosVersion) {
            $this->generateTemplate($farosVersion);
        } else {
            $defaultTemplate = file_get_contents('./srcFaros/template.yaml');
            file_put_contents($this->path, $defaultTemplate);
        }
        try {
            $url = $this->getHost($ansible);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
        $arrayTemp = preg_grep('/^ext-/i', $keys);
        $arrayExt = preg_replace('/^ext-/i', '', $arrayTemp);
        $value = Yaml::parseFile($this->path);
        $temp = $value['http']['url'];
        $value = array_filter($value, function ($key) {
            return 'http' !== $key;
        }, ARRAY_FILTER_USE_KEY);
        $arrayExtensions = array_diff(array_values($arrayExt), $temp['body'] ?? []);
        if (!empty($arrayExtensions)) {
            $this->modifSonde($arrayExtensions);
        }
        $array_merge = array_merge($temp['body'] ?? [], array_values($arrayExt));
        $body = array_values(array_unique($array_merge));
        $http = [
            'http' => [
                $url.'/sonde_faros.php' => [
                    'status' => 200,
                    'body' => $body,
                ],
            ],
        ];
        $template = Yaml::dump(array_merge($value, $http), 4, 2);
        file_put_contents($this->path, $template);

        return true;
    }

    /**
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

    private function generateTemplate(string $farosVersion): void
    {
        if ('10' === $farosVersion) {
            $faros = file_get_contents('./srcFaros/templateFaros_10.yaml');
            if (file_exists($this->path)) {
                $this->logger->info('Le fichier goss.yaml existe déjà, il va être supprimé');
            }
            file_put_contents($this->path, $faros);

            return;
        }
        if ('9' === $farosVersion) {
            $faros = file_get_contents('./srcFaros/templateFaros_9.yaml');
            if (file_exists($this->path)) {
                $this->logger->info('Le fichier goss.yaml existe déjà, il va être supprimé');
            }
            file_put_contents($this->path, $faros);

            return;
        }
    }

    /**
     * @param array<string> $requirements
     */
    private function getFarosVersion(array $requirements): ?string
    {
        if (preg_grep('/^\b(faros-ng)\b/i', $requirements)) {
            $this->logger->info('Version 10 de Faros détectée');

            return '10';
        } elseif (preg_grep('/^\b(faros)\b/i', $requirements)) {
            $this->logger->info('Version 9 de Faros détectée');

            return '9';
        }

        return null;
    }

    /**
     * @param array<string> $projet
     */
    private function modifSonde(array $projet): void
    {
        $ext = array_values($projet);
        $this->logger->info('Extensions spécifiques au projet détectées, modification de la sonde Faros');
        $sonde = file_get_contents('./srcFaros/sonde_faros.php');
        file_put_contents('./generatedFiles/sonde_faros.php', $sonde);
        $writeStart = "\necho '<pre>';\n";
        $writeEnd = "echo '</pre>';\n";
        foreach ($ext as $value) {
            $writeStart .= "echo '".$value."';\n";
        }
        file_put_contents('./generatedFiles/sonde_faros.php', $writeStart.$writeEnd, FILE_APPEND);
    }
}
