<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\StartServer;
use ddms\classes\ui\CommandLineUI;

final class StartServerTest extends TestCase
{
    public function testStartServerStartsAServer(): void
    {
        $initialServerCount = count($this->activeServers());
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['flags' => ['--start-server' => []], 'options' => []]
            )
        );
        $finalServerCount = count($this->activeServers());
        $this->assertTrue($initialServerCount < $finalServerCount);
    }

    /**
     * @return array<int, string>
     */
    private function activeServers(): array
    {
        return explode(PHP_EOL, $this->psPhp());
    }

    private function psPhp(): string
    {
        return strval(
            shell_exec(
                'printf "%s" "$(' .
                    '/usr/bin/ps -aux | ' .
                    '/usr/bin/grep -Eo \'php -S localhost:[0-9][0-9][0-9][0-9]\' | ' .
                    '/usr/bin/sed \'s,php -S localhost,  http://localhost,g\'' .
                ')"'
            )
        );
    }

}
