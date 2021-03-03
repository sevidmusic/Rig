<?php

namespace ddms\classes\factory;

use ddms\interfaces\factory\CommandFactory as DDMSCommandFactory;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\interfaces\command\Command as DDMSCommand;

abstract class CommandFactory implements DDMSCommandFactory
{

    abstract public function getCommandInstance(string $commandName, DDMSUserInterface $ddmsUserInterface): DDMSCommand;

}
