<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;

class ViewActiveServers extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $this->viewActiveServers($userInterface);
        return true;
    }

    private function viewActiveServers(UserInterface $ui): void
    {
        $output = shell_exec(
            'printf "%s" "$(' .
                '/usr/bin/ps -aux | ' .
                '/usr/bin/grep -Eo \'php -S localhost:[0-9][0-9][0-9][0-9]\' | ' .
                '/usr/bin/sed \'s,php -S localhost,  http://localhost,g\'' .
            ')"'
        );
        $ui->showMessage(
            PHP_EOL . PHP_EOL . '  Active Servers:' . PHP_EOL . PHP_EOL .
            (is_string($output) ? $output : '') . PHP_EOL
        );
    }

}
