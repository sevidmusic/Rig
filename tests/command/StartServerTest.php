<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\StartServer;
use ddms\classes\ui\CommandLineUI;

final class StartServerTest extends TestCase
{
    public function testStartServerStartsAServer(): void
    {
        $this->killAllServers();
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

    public function testStartServerStartsAServerOnPort8080IfPortIsNotSpecified(): void
    {
        $this->killAllServers();
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['flags' => ['--start-server' => []], 'options' => []]
            )
        );
    var_dump($this->activeServers('urls'));
        $this->assertTrue(in_array('http://localhost:8080', $this->activeServers('urls')));
    }

    private function killAllServers(): void
    {
        foreach($this->activeServers('pid') as $serverInfo) {
            exec('/usr/bin/kill ' . escapeshellarg($serverInfo));
        }
    }
    /**
     * @param string $target Optional string that determines what server info is
     *                       returned in the array. If set to 'urls', just the
     *                       urls for each server instance will be included in
     *                       the array, if set to 'process', then all process
     *                       information for each server instance will be
     *                       included.
     * @return array<int, string>
     */
    private function activeServers($target = 'process'): array
    {
        if($target === 'urls') {
            return explode(PHP_EOL, $this->psPhpUrls());
        }
        if($target === 'pid') {
            return explode(PHP_EOL, $this->psPhpProcessIds());
        }
        return explode(PHP_EOL, $this->psPhpProcesses());
    }

    /**
     * @return string Resutls of `ps -aux | grep -S localhost:[0-9][0-9][0-9][0-9] | sed 's,php -S localhost, http://localhost,g'`
     */
    private function psPhpUrls(): string
    {
        return strval(
            shell_exec(
                'printf "%s" "$(' .
                    '/usr/bin/ps -aux | ' .
                    '/usr/bin/grep -Eo \'php -S localhost:[0-9][0-9][0-9][0-9]\' | ' .
                    '/usr/bin/sed \'s,php -S localhost,http://localhost,g\'' .
                ')"'
            )
        );
    }

    /**
     * @return string Results of `p -aux | grep -E '([U]SER|[php] -S)`
     */
    private function psPhpProcesses(): string
    {
        return strval(
            shell_exec(
                'printf "%s" "$(' .
                    '/usr/bin/ps -aux | ' .
                    '/usr/bin/grep -E \'([U]SER|[p]hp -S)\'' .
                ')"'
            )
        );
    }

    /**
     * @return string Results of `p -aux | grep -E '[php] -S` | awk '{print $2}'`
     */
    private function psPhpProcessIds(): string
    {
        return strval(
            shell_exec(
                'printf "%s" "$(' .
                    '/usr/bin/ps -aux | ' .
                    '/usr/bin/grep -E \'[p]hp -S\' | ' .
                    '/usr/bin/awk \'{print $2}\'' .
                ')"'
            )
        );
    }


}
