<?php

namespace ddms\abstractions\command;

use ddms\interfaces\command\Command as DDMSCommandInterface;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\abstractions\command\AbstractCommand as DDMSCommandBase;

abstract class AbstractDDMS extends DDMSCommandBase implements DDMSCommandInterface
{

    /**
     * @param array<mixed> $argv
     */
    final public function runCommand(DDMSUserInterface $ddmsUI, DDMSCommandInterface $ddmsCommand, $argv): bool
    {
        return $ddmsCommand->run($ddmsUI, $ddmsCommand->prepareArguments($argv));
    }

}
