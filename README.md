# Hug

- Hug is a tool for interpreting a composer.json and editing a Goss file on the fly to check for project specific extensions (excluding Faros configuration)

## Install

First you need to download the latest release of Hug here : https://github.com/le-phare/hug/releases/latest

You can also clone the project and build from sources with `make build`.

## How it works

- Hug comes to retrieve the composer.json in the "yourComposerJson" folder (to prevent him from processing Hug's composer.json) and parses it in order to retrieve the extensions necessary for the proper functioning of your project.

- It will retrieve the project's url to be tested in your "hosts" file contained in the environment variables of your Ansible folder (Production or Preprod)

- The goss.yaml file will then be generated in the corresponding folder (./generatedFiles) and for Faros users, generate the modified probe in the same folder.

## How to use it

- Required option : --ansible-path | -a
- Example (in a docker container) : dc run --rm php php hug.phar --ansible-path=./path/to/ansible/environnement/hosts
- Note : no '=' with the shortcut '-a' : dc run --rm php php hug.phar -a ./path/to/ansible/environnement/hosts

## Requirements

- Your ./ansible/environnement/hosts file must be in :  
[app]  
'project's url'

## Maintenance (Faros)

- If a new version of Faros comes out, you have to modify the script in the ParseJsonService.php file to match the pattern with the name of the faros bundle (faros or faros-ng for current versions) in your require section of composer.json

- You must also modify the faros templates and it's probe (./srcFaros) to update them if necessary. 
