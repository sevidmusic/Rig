<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class NewGlobalResponse extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new GlobalResponse.');
        }
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  Please specify the name of the App to create the new GlobalResponse for');
        }
        $appDirectoryPath = $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0];
        if(!file_exists($appDirectoryPath) || !is_dir($appDirectoryPath)) {
            throw new RuntimeException('  An App does not exist at' . $appDirectoryPath);
        }
        return true;
    }

}
