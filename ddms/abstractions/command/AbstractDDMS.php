<?php

namespace ddms\abstractions\command;

use ddms\interfaces\command\Command;
use ddms\interfaces\ui\UserInterface;
use ddms\abstractions\command\AbstractCommand;

abstract class AbstractDDMS extends AbstractCommand implements Command
{

    /**
     * @param array<mixed> $argv
     */
    final public function runCommand(UserInterface $ddmsUI, Command $ddmsCommand, $argv): bool
    {
        return $ddmsCommand->run($ddmsUI, $ddmsCommand->prepareArguments($argv));
    }

}
