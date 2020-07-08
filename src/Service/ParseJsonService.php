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

    /** @var string */
    private $pathPrefix;

    /**
     * @var string
     */
    private $path;

    public function __construct()
    {
        $this->logger = new Logger('hugLog');
        $this->path = getcwd();
        $this->logger->pushHandler(new StreamHandler('php://stdout'));
        var_dump(\Phar::running());
        $this->pathPrefix = \strlen(\Phar::running()) > 0 ? 'phar://.' : realpath(__DIR__.'/../..');
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
        $this->generateTemplate($farosVersion);

        try {
            $url = $this->getHost($ansible);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
        $arrayTemp = preg_grep('/^ext-/i', $keys);
        $arrayExt = preg_replace('/^ext-/i', '', $arrayTemp);
        $value = Yaml::parseFile($this->path.'/goss.yaml');
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
        file_put_contents($this->path.'/goss.yaml', $template);

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

    private function generateTemplate(?string $farosVersion): void
    {
        $templateFile = sprintf('%s/templates/templateFaros_%s.yaml', $this->pathPrefix, $farosVersion);
        $outputPath = $this->path.'/goss.yaml';

        if (is_file($templateFile)) {
            $faros = file_get_contents($templateFile);
            if (file_exists($outputPath)) {
                $this->logger->warning('Le fichier goss.yaml existe déjà, il va être supprimé');
            }
            file_put_contents($outputPath, $faros);
        } else {
            $defaultTemplate = file_get_contents($this->pathPrefix.'/templates/template.yaml');
            file_put_contents($outputPath, $defaultTemplate);
        }

        $this->logger->debug('Fichier {goss_yaml} généré', ['goss_yaml' => $outputPath]);

        return;
    }

    /**
     * @param string[] $requirements
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
        $sondeFile = $this->path.'/sonde_faros.php';
        $ext = array_values($projet);
        $this->logger->info('Extensions spécifiques au projet détectées, modification de la sonde Faros');
        $sonde = file_get_contents($this->pathPrefix.'/templates/sonde_faros.php');
        file_put_contents($sondeFile, $sonde);

        $this->logger->debug('Fichier {sonde} généré', ['sonde' => $sondeFile]);

        $writeStart = "\necho '<pre>';\n";
        $writeEnd = "echo '</pre>';\n";
        foreach ($ext as $value) {
            $writeStart .= "echo '".$value."';\n";
        }
        file_put_contents($sondeFile, $writeStart.$writeEnd, FILE_APPEND);
    }
}
