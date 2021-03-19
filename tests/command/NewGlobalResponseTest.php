<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\command\NewGlobalResponse;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewGlobalResponseTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments([]));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewGlobalResponseForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'GlobalResponse';
        $newGlobalResponse = new NewGlobalResponse();
        $preparedArguments = $newGlobalResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newGlobalResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedGlobalResponsePath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'GlobalResponse';
        $newGlobalResponse = new NewGlobalResponse();
        $preparedArguments = $newGlobalResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newGlobalResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedGlobalResponsePhpContent($preparedArguments), $this->getNewGlobalResponseContent($preparedArguments));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewGlobalResponseContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedGlobalResponsePath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'GlobalResponse';
        $newGlobalResponse = new NewGlobalResponse();
        $preparedArguments = $newGlobalResponse->prepareArguments(['--name', $responseName, '--for-app', $appName, '--position']);
        $newGlobalResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedGlobalResponsePhpContent($preparedArguments), file_get_contents($this->expectedGlobalResponsePath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPositionIsNotNumeric(): void
    {
        $appName = self::getRandomAppName();
        $newApp = new NewApp();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        $responseName = $appName . 'GlobalResponse';
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--name', $responseName, '--for-app', $appName, '--position', 'FooBarBaz']));
    }
/*
    public function testRunSetsPositionToSpecifiedPositionIfSpecifiedPositionIsNumeric(): void
    {
    }
*/
    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedGlobalResponsePath(array $preparedArguments): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Responses' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['name'][0] . '.php';
    }

    private function createTestAppReturnName(): string
    {
        $appName = self::getRandomAppName();
        $newApp = new NewApp();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        return $appName;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineExpectedGlobalResponsePhpContent(array $preparedArguments): string
    {
        return str_replace(['_NAME_', '_POSITION_'], [$preparedArguments['flags']['name'][0], ($preparedArguments['flags']['position'][0] ?? '0')], strval(file_get_contents($this->expectedTemplateFilePath())));
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'GlobalResponse.php';
    }

}
