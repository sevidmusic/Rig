<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use tests\command\DDMSTest;

final class MockDDMSCommand extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $userInterface->showMessage(DDMSTest::MOCK_COMMAND_OUTPUT);
        return true;
    }

}
