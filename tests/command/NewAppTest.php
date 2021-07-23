<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\command\NewApp;
use rig\classes\ui\CommandLineUI;
use rig\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;

final class NewAppTest extends TestCase
{

    use TestsCreateApps;
    private UserInterface $ui;
    private NewApp $newApp;

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->getNewApp()->run($this->getUserInterface(), $this->getNewApp()->prepareArguments(['--new-app']));
    }

    public function testRunThrowsRuntimeExceptionIfExpectedPathToNewAppDirectoryIsUnavailable() : void
    {
        $preparedArguments = $this->getNewApp()->prepareArguments(['--new-app', '--name', $this->getRandomAppName()]);
        $this->expectException(\RuntimeException::class);
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
    }

    public function testRunCreatesNewAppDirectoryAtPathAssignedTo_rig_apps_directory_path_Flag(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists(self::expectedAppDirectoryPath($preparedArguments)));
        $this->assertTrue(is_dir(self::expectedAppDirectoryPath($preparedArguments)));
    }

    public function testRunCreatesNewAppsCssDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedCssDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'css';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedCssDirectoryPath));
        $this->assertTrue(is_dir($expectedCssDirectoryPath));
    }

    public function testRunCreatesNewAppsJsDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedJsDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'js';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedJsDirectoryPath));
        $this->assertTrue(is_dir($expectedJsDirectoryPath));
    }

    public function testRunCreatesNewAppsDynamicOutputDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedDynamicOutputDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'DynamicOutput';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedDynamicOutputDirectoryPath));
        $this->assertTrue(is_dir($expectedDynamicOutputDirectoryPath));
    }

    public function testRunCreatesNewAppsResourcesDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedresourcesDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'resources';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedresourcesDirectoryPath));
        $this->assertTrue(is_dir($expectedresourcesDirectoryPath));
    }

    public function testRunCreatesNewAppsResponsesDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedresponsesDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Responses';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedresponsesDirectoryPath));
        $this->assertTrue(is_dir($expectedresponsesDirectoryPath));
    }

    public function testRunCreatesNewAppsRequestsDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedrequestsDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Requests';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedrequestsDirectoryPath));
        $this->assertTrue(is_dir($expectedrequestsDirectoryPath));
    }

    public function testRunCreatesNewAppsOutputComponentsDirectory(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedoutputComponentsDirectoryPath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'OutputComponents';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedoutputComponentsDirectoryPath));
        $this->assertTrue(is_dir($expectedoutputComponentsDirectoryPath));
    }

    public function testRunCreatesNewAppsComponentsPhpFile(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedcomponentsPhpFilePath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertTrue(file_exists($expectedcomponentsPhpFilePath));
    }

    public function testRunSets_DOMAIN_To_httplocalhost8080_InNewAppsComponentsPhpIf_domain_FlagIsNotPresent(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name ];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedcomponentsPhpFilePath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php';
        $expectedComponentsPhpFileTemplatePath = str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertEquals(
            str_replace(
                '_DOMAIN_',
                'http://localhost:8080/',
                strval(file_get_contents($expectedComponentsPhpFileTemplatePath))
            ),
            file_get_contents($expectedcomponentsPhpFilePath)
        );
    }

    public function testRunSets_DOMAIN_To_httplocalhost8080_InNewAppsComponentsPhpIf_domain_FlagIsPresentButHasNoArguments(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name, '--domain'];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedcomponentsPhpFilePath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php';
        $expectedComponentsPhpFileTemplatePath = str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertEquals(
            str_replace(
                '_DOMAIN_',
                'http://localhost:8080/',
                strval(file_get_contents($expectedComponentsPhpFileTemplatePath))
            ),
            file_get_contents($expectedcomponentsPhpFilePath)
        );
    }

    public function testRunSets_DOMAIN_To_httplocalhost8080_InNewAppsComponentsPhpIf_domain_FlagIsPresentButFirstArgumentIsNotAValidDomain(): void
    {
        $name = $this->getRandomAppName();
        $argv = ['--new-app', '--name', $name, '--domain', 'FooBar' . strval(rand(1000, 9999))];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedcomponentsPhpFilePath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php';
        $expectedComponentsPhpFileTemplatePath = str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertEquals(
            str_replace(
                '_DOMAIN_',
                'http://localhost:8080/',
                strval(file_get_contents($expectedComponentsPhpFileTemplatePath))
            ),
            file_get_contents($expectedcomponentsPhpFilePath)
        );
    }

    public function testRunSets_DOMAIN_ToSpecifiedDomainInNewAppsComponentsPhpIf_domain_FlagIsPresentAndFirstArgumentIsAValidDomain(): void
    {
        $name = $this->getRandomAppName();
        $domain = 'http://localhost:' . strval(rand(8000, 8999));
        $argv = ['--new-app', '--name', $name, '--domain', $domain];
        $preparedArguments = $this->getNewApp()->prepareArguments($argv);
        ['flags' => $flags] = $preparedArguments;
        $expectedcomponentsPhpFilePath = self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php';
        $expectedComponentsPhpFileTemplatePath = str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getNewApp()->run($this->getUserInterface(), $preparedArguments);
        $this->assertEquals(
            str_replace(
                '_DOMAIN_',
                $domain,
                strval(file_get_contents($expectedComponentsPhpFileTemplatePath))
            ),
            file_get_contents($expectedcomponentsPhpFilePath)
        );
    }

    private function getNewApp(): NewApp
    {
        if(!isset($this->newApp)) {
            $this->newApp = new NewApp();
        }
        return $this->newApp;
    }

    private function getUserInterface(): UserInterface
    {
        if(!isset($this->ui)) {
            $this->ui = new CommandLineUI();
        }
        return $this->ui;
    }

}
