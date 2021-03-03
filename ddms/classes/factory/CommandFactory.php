<?php

namespace ddms\classes\factory;

use ddms\interfaces\factory\CommandFactory as DDMSCommandInterfaceFactory;
use ddms\interfaces\ui\UserInterface as DDMSUIInterface;
use ddms\interfaces\command\Command as DDMSCommandInterface;
use ddms\classes\command\Help;

class CommandFactory implements DDMSCommandInterfaceFactory
{

    public function getCommandInstance(string $commandName, DDMSUIInterface $ddmsUserInterface): DDMSCommandInterface
    {
        $commandClassName = 'ddms\\classes\\command\\' . ucwords($commandName);
        $implements = (class_exists($commandClassName) ? class_implements($commandClassName) : []);
        if(in_array(DDMSCommandInterface::class, (is_array($implements) ? $implements : []))) {
            return new $commandClassName();
        }
        $ddmsUserInterface->notify(CommandFactory::class . " Error:\e[0m\e[103m\e[30m `ddms --\e[0m\e[105m\e[30m" . $commandName . "\e[0m\e[103m\e[30m`, does not make sense. For help use `ddms --help`.", $ddmsUserInterface::ERROR);
        return new Help();
    }

}
