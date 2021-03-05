<?php


namespace ddms\interfaces\factory;

use ddms\interfaces\ui\UserInterface;
use ddms\interfaces\command\Command;

interface CommandFactory
{

    public function getCommandInstance(string $commandName, UserInterface $ddmsUserInterface): Command;

}
