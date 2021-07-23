<?php


namespace rig\interfaces\factory;

use rig\interfaces\ui\UserInterface;
use rig\interfaces\command\Command;

interface CommandFactory
{

    public function getCommandInstance(string $commandName, UserInterface $rigUserInterface): Command;

}
