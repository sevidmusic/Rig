<?php

namespace rig\classes\factory;

use rig\interfaces\factory\CommandFactory as CommandFactoryInterface;
use rig\interfaces\ui\UserInterface;
use rig\interfaces\command\Command;
use rig\classes\command\Help;

class CommandFactory implements CommandFactoryInterface
{

    public function getCommandInstance(string $commandName, UserInterface $rigUserInterface): Command
    {
        $commandClassName = 'rig\\classes\\command\\' . ucwords($commandName);
        $implements = (class_exists($commandClassName) ? class_implements($commandClassName) : []);
        if(in_array(Command::class, (is_array($implements) ? $implements : []))) {
            return new $commandClassName();
        }
        return new Help();
    }

}
