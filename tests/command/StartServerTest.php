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
                ['--start-server']
            )
        );
        $finalServerCount = count($this->activeServers());
        $this->assertTrue($initialServerCount < $finalServerCount);
    }

    public function testStartServerOpensBrowserIf_open_in_broswer_FlagIsPresent(): void
    {
        $this->killAllServers();
        $initialServerCount = $this->numberOfBrowserProcesses();
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['--start-server', '--port', strval(rand(8000, 8999)), '--open-in-browser']
            )
        );
        /**
         * Give browser a chance to start, not waiting will produce a false positive,
         * i.e., test will fail even though browser was opened.
         * Also note: running this test multiple times will open multiple
         * browser tabs if successful, if more than 5 tabs are open xdg-open
         * sometimes fails, not sure why, so if this test is failing try closing
         * the browser and running again.
         * In general, xdg-open may not always succeed, which does not mean that
         * an attempt to open the browser was made.
         */
        sleep(1);
        $finalServerCount = $this->numberOfBrowserProcesses();
        $this->assertTrue($initialServerCount < $finalServerCount);
    }

    public function testStartServerStartsAServerOnPort8080IfPortIsNotSpecified(): void
    {
        $this->killAllServers();
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['--start-server']
            )
        );
        $this->assertTrue(in_array('http://localhost:8080', $this->activeServers('urls')));
    }

    public function testStartServerStartsAServerOnSpecifiedPortIfPortIsSpecified(): void
    {
        $this->killAllServers();
        $randomPort = strval(rand(8000, 8999));
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['--start-server', '--port', $randomPort]
            )
        );
        $this->assertTrue(in_array('http://localhost:' . $randomPort, $this->activeServers('urls')));
    }

    private function killAllServers(): void
    {
        foreach($this->activeServers('pid') as $pid) {
            if(!empty($pid) && is_numeric($pid)) {
                exec('/usr/bin/kill ' . escapeshellarg($pid));
            }
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
        if($target === 'roots') {
            return explode(PHP_EOL, $this->psRootDirectories());
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

    public function testStartServerStartsAServerUsing_tmp_DirectoryAsRootDirectoryIfRootDirIsNotSpecified(): void
    {
        $this->killAllServers();
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['--start-server']
            )
        );
        $this->assertTrue(in_array('/tmp', $this->activeServers('roots')));
    }

    public function testStartServerStartsAServerUsingSpecifiedDirectoryAsRootDirectoryIfRootDirIsSpecified(): void
    {
        $this->killAllServers();
        $startServer = new StartServer();
        $startServer->run(
            new CommandLineUI(),
            $startServer->prepareArguments(
                ['--start-server', '--root-dir', __DIR__]
            )
        );
        $this->assertTrue(in_array(__DIR__, $this->activeServers('roots')));
    }

    /**
     * @return string Results of `p -aux | grep -E '[php] -S` | awk '{print $2}'`
     */
    private function psRootDirectories(): string
    {
        return strval(
            shell_exec(
                'printf "%s" "$(' .
                    '/usr/bin/ps -aux | ' .
                    '/usr/bin/grep -E \'[p]hp -S\' | ' .
                    '/usr/bin/awk \'{print $15}\'' .
                ')"'
            )
        );
    }

    private function numberOfBrowserProcesses(): int
    {
        $testBrowser = 'firefox';
        return count(
            explode(
                PHP_EOL,
                strval(
                    shell_exec(
                        'printf "%s" "$(' .
                        '/usr/bin/ps -aux | ' .
                        '/usr/bin/grep -E \'' . $testBrowser  . '\'' .
                    ')"'
                    )
                )
            )
        );
    }

}
