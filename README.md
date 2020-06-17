#Hug

- Hug is a tool for interpreting a composer.json and editing a Goss file on the fly to check for project specific extensions (excluding Faros configuration)

## Install

## How it works

- Hug comes to retrieve the composer.json at the root of the project and parses it in order to retrieve the extensions necessary for the proper functioning of your project.

- It will retrieve the project's url to be tested in your "hosts" file contained in the environment variables of your Ansible folder (Production or Preprod)

- The goss_project.yaml file will then be generated in the corresponding folder (./fichierGoss)

## How to use it

- Required option : --ansible-path | -a
- Example (in a docker container) : dc run --rm hug.phar --ansible-path=./path/to/ansible/environnement/hosts
- Note : no '=' with the shortcut '-a' : dc run --rm hug.phar -a ./path/to/ansible/environnement/hosts

## Requirements

- Your ./ansible/environnement/hosts file must be in :  
[app]  
'project's url' 
