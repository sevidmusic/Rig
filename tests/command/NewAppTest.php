<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;

final class NewAppTest extends TestCase
{
    private UserInterface $ui;
    private NewApp $newApp;
    /** @var array <int, string> $createdApps */
    private static $createdApps = [];

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

    public function testRunCreatesNewAppDirectoryAtPathAssignedTo_ddms_apps_directory_path_Flag(): void
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

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private static function expectedAppDirectoryPath(array $preparedArguments) : string
    {
        ['flags' => $flags] = $preparedArguments;
        $path = ($flags['ddms-apps-directory-path'][0] ?? DIRECTORY_SEPARATOR . 'tmp') . DIRECTORY_SEPARATOR . ($flags['name'][0] ?? 'BadTestArgToNewAppNameFlagError');
        return $path;
    }

    private static function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $contents = scandir($dir);
            $contents = (is_array($contents) ? $contents : []);
            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
                    (is_dir($itemPath) === true && is_link($itemPath) === false)
                        ? self::removeDirectory($itemPath)
                        : unlink($itemPath);
                }
            }
            rmdir($dir);
        }
    }

    public static function registerAppName(string $appName): void
    {
        array_push(self::$createdApps, $appName);
    }

    private static function getRandomAppName(): string
    {
        $appName = 'App' . strval(rand(1000,9999));
        self::registerAppName($appName);
        return $appName;
    }

    public static function tearDownAfterClass(): void
    {
        $newApp = new NewApp();
        foreach(self::$createdApps as $appName) {
            $preparedArguments = $newApp->prepareArguments(['--name', $appName]);
            self::removeDirectory(self::expectedAppDirectoryPath($preparedArguments));
        }
    }

}
