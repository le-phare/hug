<?php

namespace Hug\Service;

use Symfony\Component\Yaml\Yaml;

class ParseJson
{
    public function ParseJson(): void
    {
        $json = file_get_contents('./tests/composer.json');
        $json_data = json_decode($json, true);
        $extensions = $json_data['require'];
        $keys = array_keys($extensions);
        $pattern = '/^ext-/i';
        $yaml = preg_grep($pattern, $keys);
        $value = Yaml::parseFile('./tests/TestTemplate.yaml');

        if (file_exists('./fichierGoss/goss_projet.yaml')) {
            echo 'Le fichier existe déjà, il va être supprimé';
            $template = Yaml::dump($value);
            file_put_contents('./fichierGoss/goss_projet.yaml', $template);
            file_put_contents('fichierGoss/goss_projet.yaml', $yaml, FILE_APPEND);
        } else {
            $template = Yaml::dump($value);
            file_put_contents('./fichierGoss/goss_projet.yaml', $template);
            file_put_contents('fichierGoss/goss_projet.yaml', $yaml, FILE_APPEND);
        }
    }
}
