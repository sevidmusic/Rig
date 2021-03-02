<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command as DDMSCommandInterface;
use ddms\abstractions\command\AbstractCommand as DDMSCommandBase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

class DDMSHelp extends DDMSCommandBase implements DDMSCommandInterface
{

    public function run(DDMSUserInterface $ddmsUI, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        return true;
    }
}
