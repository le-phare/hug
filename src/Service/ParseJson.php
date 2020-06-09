<?php

namespace Hug\Service;

use Symfony\Component\Yaml\Yaml;

class ParseJson
{
    public function ParseJson(array $ansible): void
    {
        $json = file_get_contents('./tests/composer.json');
        $json_data = json_decode($json, true);
        $extensions = $json_data['require'];
        $keys = array_keys($extensions);
        $pattern = '/^ext-/i';
        $array = preg_grep($pattern, $keys);
        $yaml = array_values($array);
        //récupérer URL selon chemin Ansible
        $temp = array_values($ansible);
        $string = implode('', $temp);
        $url = file_get_contents($string, false, null, 5); //récupère la valeur après [app] => pas très propre
        // TODO: avec symfony plus de print_r, utilise dump() du composant symfony/dumper si tu veux debugger, sinon le LoggerInterface si tu veux logger.
        print_r($url);

        if (file_exists('./fichierGoss/goss_projet.yaml')) {
            echo 'Le fichier existe déjà, il va être supprimé';
            $template = Yaml::dump([
                'http' => [$url => ['status' => 200, 'body' => null]],
            ], 4, 2);
            file_put_contents('./fichierGoss/goss_projet.yaml', $template);
            $phpExtensions = Yaml::dump($yaml, 2, 2, 0);
            file_put_contents('fichierGoss/goss_projet.yaml', $phpExtensions, FILE_APPEND);
        } else {
            $template = Yaml::dump([
                'http' => ['http://proxibleus.hde.lph/test_projet.php' => ['status' => 200, 'body' => null]],
            ], 4, 2);
            file_put_contents('./fichierGoss/goss_projet.yaml', $template);
            $phpExtensions = Yaml::dump($yaml, 2, 2, 0);
            file_put_contents('fichierGoss/goss_projet.yaml', $phpExtensions, FILE_APPEND);
        }
    }
}

// parser la conf ansible, ajouter une option --ansible-path pour récupérer un dossier externe à /hugo
// fichier hosts dans ansible, se renseigner sur la config ansible du phare ou conf général
