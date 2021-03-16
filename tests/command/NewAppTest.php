<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\ui\CommandLineUI;

final class NewAppTest extends TestCase
{
    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified() : void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $this->expectException(\RuntimeException::class);
        $newApp->run($ui, $newApp->prepareArguments(['--new-app']));
    }

    public function testRunCreatesNewAppDirectoryAtPathAssignedTo_ddms_internal_flag_pwd_Flag(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedAppDirectoryPath));
        $this->assertTrue(is_dir($expectedAppDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsCssDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedCssDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'css';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedCssDirectoryPath));
        $this->assertTrue(is_dir($expectedCssDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsJsDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedJsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'js';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedJsDirectoryPath));
        $this->assertTrue(is_dir($expectedJsDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsDynamicOutputDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedDynamicOutputDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedDynamicOutputDirectoryPath));
        $this->assertTrue(is_dir($expectedDynamicOutputDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsResourcesDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedresourcesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'resources';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedresourcesDirectoryPath));
        $this->assertTrue(is_dir($expectedresourcesDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsResponsesDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedresponsesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Responses';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedresponsesDirectoryPath));
        $this->assertTrue(is_dir($expectedresponsesDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsRequestsDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedrequestsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedrequestsDirectoryPath));
        $this->assertTrue(is_dir($expectedrequestsDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsOutputComponentsDirectory(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedoutputComponentsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedoutputComponentsDirectoryPath));
        $this->assertTrue(is_dir($expectedoutputComponentsDirectoryPath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunCreatesNewAppsComponentsPhpFile(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo';
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedcomponentsPhpFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php';
        $newApp->run($ui, $preparedArguments);
        $this->assertTrue(file_exists($expectedcomponentsPhpFilePath));
        $this->removeDirectory($expectedAppDirectoryPath);
    }

    public function testRunSets_DOMAIN_To_httplocalhost8080_InNewAppsComponentsPhpIf_domain_FlagIsNotPresent(): void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $name = 'Foo' . strval(rand(1000,9999));
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $newApp->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedAppDirectoryPath = $flags['ddms-internal-flag-pwd'][0] . DIRECTORY_SEPARATOR . $name;
        $expectedcomponentsPhpFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php';
        $expectedComponentsPhpFileTemplatePath = str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Components.php';
        $newApp->run($ui, $preparedArguments);
        $this->assertEquals(
            str_replace(
                '_DOMAIN_',
                'http://localhost:8080/',
                strval(file_get_contents($expectedComponentsPhpFileTemplatePath))
            ),
            file_get_contents($expectedcomponentsPhpFilePath)
        );
    }

    private function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $contents = scandir($dir);
            $contents = (is_array($contents) ? $contents : []);
            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
                    (is_dir($itemPath) === true && is_link($itemPath) === false)
                        ? $this->removeDirectory($itemPath)
                        : unlink($itemPath);
                }
            }
            rmdir($dir);
        }
    }

}
