<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;

class StartServer extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $this->startServer($preparedArguments);
        return true;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * &> /dev/null & xdg-open "http://localhost:${1:-8080}" &>/dev/null & disown
     */
    private function startServer(array $preparedArguments): void
    {
        ['flags' => $flags] = $preparedArguments;
        $serverLogPath = escapeshellarg('/tmp/ddms-php-built-in-server.log');
        $localhost = escapeshellarg(
            'localhost:' . ($flags['port'][0] ?? '8080')
        );
        $rootDirectory = escapeshellarg(($flags['root-dir'][0] ?? '/tmp'));
        $domain = escapeshellarg('http://' . str_replace("'", '', $localhost));
        shell_exec(
            '/usr/bin/php -S ' . $localhost . ' -t ' . $rootDirectory .
            ' >> ' . $serverLogPath .
            ' 2>> ' . $serverLogPath .
            (isset($flags['open-in-browser']) ? ' & xdg-open ' . $domain . ' &> /dev/null' : '') .
            ' & disown'
        );
    }
}
