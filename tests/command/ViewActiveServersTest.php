<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\ViewActiveServers;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;

final class ViewActiveServersTest extends TestCase
{

    public function testViewActiveServersHasOutputIfAtLeastOneBuiltInServerInstanceIsRunning(): void
    {
        $viewActiveServers = new ViewActiveServers();
        $this->startBuiltInServerInstance();
        $this->expectOutputString($this->expectedViewActiveServersOutput());
        $viewActiveServers->run(new CommandLineUI(), $viewActiveServers->prepareArguments(['--view-active-servers']));
    }

    private function startBuiltInServerInstance(): void
    {
        $serverLogPath = escapeshellarg('/tmp/ddms-php-built-in-server.log');
        $localhost = escapeshellarg('localhost:' . strval(rand(8000, 8999)));
        shell_exec(
            '/usr/bin/php -S ' . $localhost . ' -t ' . escapeshellarg(__DIR__) . # start PHP built in server
            ' >> ' . $serverLogPath . # redirect sdout to server log
            ' 2>> ' . $serverLogPath . # redirect sderr to server log
            ' & sleep .09' . # give server a momement
            ' & disown' # send all to bg and disown
        );
    }

    private function expectedViewActiveServersOutput(): string
    {
        # Get active servers:
        $output = shell_exec(
            'printf "%s" "$(' .
                '/usr/bin/ps -aux | ' .
                '/usr/bin/grep -Eo \'php -S localhost:[0-9][0-9][0-9][0-9]\' | ' .
                '/usr/bin/sed \'s,php -S localhost,  http://localhost,g\'' .
            ')"'
        );
        return PHP_EOL . PHP_EOL . '  Active Servers:' . PHP_EOL . PHP_EOL .
               ((is_string($output) ? $output : '')) . PHP_EOL;
    }

}
