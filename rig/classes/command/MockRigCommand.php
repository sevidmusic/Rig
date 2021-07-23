<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;
use tests\command\RigTest;

final class MockRigCommand extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $userInterface->showMessage(RigTest::MOCK_COMMAND_OUTPUT);
        return true;
    }

}
