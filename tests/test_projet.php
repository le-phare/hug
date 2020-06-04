<?php
echo '<pre>';
ob_start();
include 'test_faros.php';
$data = ob_get_clean();
//Fichier composer.json
$json = file_get_contents('./../composer.json');
//Pattern à chercher / remplacer 
$pattern = '/^ext-/i';
//Tableau d'extensions Faros
$extensionsFaros = printLoadedExtensions();


function extraireJson(string $json, string $pattern):array
{
    $json_data = json_decode($json, true);
    $extensions = $json_data['require'];
    echo 'PHP version :'.$extensions['php'].PHP_EOL;
    $keys = array_keys($extensions);
    $result = preg_grep($pattern, $keys);
    return $result;
}
    
function formatExtensions (array $extensions, string $pattern):array
{
    //$result = extraireJson();
    $result = array_map(function ($element) use ($pattern){
         return preg_replace($pattern,'',$element);
    },$extensions);
    return $result;
}

function extraireExtensionsProjet (array $extensionsFaros, array $extensionsProjet):array
{
    $extensionsDifferentes = array_diff($extensionsProjet, $extensionsFaros);
    return $extensionsDifferentes;
}

function afficher (array $extensionsDifferentes):void
{
        foreach ($extensionsDifferentes as $ext) {
            echo $ext.PHP_EOL;
        }
}

$result = extraireJson($json,$pattern);
$extensionsProjet = formatExtensions($result,$pattern);
$extensionsDifferentes = extraireExtensionsProjet($extensionsFaros,$extensionsProjet);
afficher($extensionsDifferentes);
echo '</pre>';
