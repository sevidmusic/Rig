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
        mkdir($flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $flags['name'][0]);
        return true;
    }

}
