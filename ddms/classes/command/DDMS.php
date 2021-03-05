<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractDDMS;
use ddms\interfaces\ui\UserInterface;

class DDMS extends AbstractDDMS implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        return false;
    }

}
