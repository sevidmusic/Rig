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
        $rootDirectory = escapeshellarg(($flags['root-dir'][0] ?? __DIR__));
        $openInBrowser = (isset($flags['open-in-browser']) ? true : false);
        $domain = escapeshellarg('http://' . str_replace("'", '', $localhost));
        shell_exec(
            '/usr/bin/php -S ' . $localhost . ' -t ' . $rootDirectory .             # start PHP built in server
            ' >> ' . $serverLogPath .                                               # redirect sdout to server log
            ' 2>> ' . $serverLogPath .                                              # redirect sderr to server log
            ' & sleep .09' .                                                        # give server a momement, this also allows --view-server-log to read log right away
            ($openInBrowser ? ' & xdg-open ' . $domain . ' &> /dev/null' : '') .    # open in browsr if --open-in-browser flag specified
            ' & disown'                                                             # send all to bg and disown
        );
    }
}
