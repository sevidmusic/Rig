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
            mkdir($appDirectoryPath);
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'css');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'js');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'resources');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'Responses');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'Requests');
            mkdir($appDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents');
            copy(
                str_replace(
                    'ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command',
                    'FileTemplates' . DIRECTORY_SEPARATOR . 'Components.php',
                    __DIR__
                ),
                $appDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php'
            );
        }
        return true;
    }

}
