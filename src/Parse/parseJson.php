<?php

namespace Hug\Parse;

use Symfony\Component\Yaml\Yaml;

class ParseJson
{
/*	
$json = file_get_contents('/../tests/composer.json');
$json_data = json_decode($json, true);
$extensions = $json_data['require'];
$keys = array_keys($extensions);
$pattern = '/^ext-/i';
$array = preg_grep($pattern, $keys);
*/
$yaml = Yaml::dump($array);

file_put_contents('./fichierGoss/goss_projet.yaml', $yaml, FILE_APPEND);
}
