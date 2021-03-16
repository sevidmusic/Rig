<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
class NewApp extends AbstractCommand implements Command
{


    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        if(!in_array('name', array_keys($flags))) {
            throw new RuntimeException('  You must specify a name for the new App');
        }
        $appDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $flags['name'][0];
        if(!is_dir($appDirectoryPath)) {
            $this->createAppsDirectoryStructure($appDirectoryPath);
            $this->createAppsComponentsPhp($appDirectoryPath);
            return true;
        }
        $userInterface->showMessage('An App named ' .  $flags['name'][0] . ' already exists. Please specify a unique name.');
        return false;
    }

    private function createAppsComponentsPhp(string $appDirectoryPath): void
    {
        $componentPhpFileTemplatePath = str_replace(
            'ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command',
            'FileTemplates' . DIRECTORY_SEPARATOR . 'Components.php',
            __DIR__
        );
        $componentsPhpTemplate = strval(file_get_contents($componentPhpFileTemplatePath));
        $content = str_replace('_DOMAIN_', 'http://localhost:8080/', $componentsPhpTemplate);
        file_put_contents($appDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php', $content);
    }

    private function createAppsDirectoryStructure(string $appDirectoryPath): void {
            mkdir($appDirectoryPath);
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'css');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'js');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'resources');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'Responses');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'Requests');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents');
    }

}

