<?php


namespace ddms\interfaces\factory;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\interfaces\command\Command as DDMSCommand;

interface CommandFactory
{

    public function getCommandInstance(string $commandName, DDMSUserInterface $ddmsUserInterface): DDMSCommand;

}
