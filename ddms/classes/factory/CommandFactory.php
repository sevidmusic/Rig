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
        $ddmsUserInterface->notify(CommandFactory::class . " Error: The specified command" . $commandName . ", is not valid. Defaulting to --help.", $ddmsUserInterface::ERROR);
        return new Help();
    }

}
