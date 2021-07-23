<?php

namespace rig\abstractions\command;

use rig\interfaces\command\Command;
use rig\interfaces\ui\UserInterface;
use rig\abstractions\command\AbstractCommand;

abstract class AbstractRig extends AbstractCommand implements Command
{

    /**
     * @param array<mixed> $argv
     */
    final public function runCommand(UserInterface $rigUI, Command $rigCommand, $argv): bool
    {
        return $rigCommand->run($rigUI, $rigCommand->prepareArguments($argv));
    }

}
