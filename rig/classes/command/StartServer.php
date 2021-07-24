<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;

class StartServer extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $this->startServer($preparedArguments, $userInterface);
        return true;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * &> /dev/null & xdg-open "http://localhost:${1:-8080}" &>/dev/null & disown
     */
    private function startServer(array $preparedArguments, UserInterface $ui): void
    {
        ['flags' => $flags] = $preparedArguments;
        $serverLogPath = escapeshellarg('/tmp/rig-php-built-in-server.log');
        $localhost = escapeshellarg(
            'localhost:' . ($flags['port'][0] ?? '8080')
        );
        $rootDirectory = escapeshellarg(($flags['root-dir'][0] ?? $this->defaultServerRoot($flags)));
        $domain = escapeshellarg('http://' . str_replace("'", '', $localhost));
        $ui->showMessage(
            PHP_EOL .
            'Starting server @ ' . $domain . ' using ' . $rootDirectory . ' as server root directory.' .
            PHP_EOL .
            PHP_EOL
        );
        shell_exec(
            '/usr/bin/php -S ' . $localhost . ' -t ' . $rootDirectory .
            (isset($flags['php-ini'][0]) ? ' -c ' . escapeshellarg($flags['php-ini'][0]) : '') .
            ' >> ' . $serverLogPath .
            ' 2>> ' . $serverLogPath .
            (isset($flags['open-in-browser']) ? ' & xdg-open ' . $domain . ' &> /dev/null' : '') .
            ' & disown'
        );
    }


    /**
     * @param array<string, array<int, string>> $flags
     */
    private function defaultServerRoot(array $flags): string
    {
        return str_replace(['Apps', 'tmp'], '', $flags['path-to-apps-directory'][0]);
    }
}