<?php

namespace ddms\classes\factory;

use ddms\interfaces\factory\CommandFactory as CommandFactoryInterface;
use ddms\interfaces\ui\UserInterface;
use ddms\interfaces\command\Command;
use ddms\classes\command\Help;

class CommandFactory implements CommandFactoryInterface
{

    public function getCommandInstance(string $commandName, UserInterface $ddmsUserInterface): Command
    {
        $commandClassName = 'ddms\\classes\\command\\' . ucwords($commandName);
        $implements = (class_exists($commandClassName) ? class_implements($commandClassName) : []);
        if(in_array(Command::class, (is_array($implements) ? $implements : []))) {
            return new $commandClassName();
        }
        $ddmsUserInterface->showMessage(
            CommandFactory::class .
            "Error:\e[0m\e[103m\e[30m `ddms --\e[0m\e[105m\e[30m" .
            $commandName .
            "\e[0m\e[103m\e[30m`, does not make sense. For help use `ddms --help`."
        );
        return new Help();
    }

}
