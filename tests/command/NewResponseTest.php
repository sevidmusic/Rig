<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\command\NewApp;
use rig\classes\command\NewResponse;
use rig\classes\ui\CommandLineUI;
use rig\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewResponseTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newResponse = new NewResponse();
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $newResponse->prepareArguments([]));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newResponse = new NewResponse();
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $newResponse->prepareArguments(['--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newResponse = new NewResponse();
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $newResponse->prepareArguments(['--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewResponseForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedResponsePath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedResponsePhpContent($preparedArguments), $this->getNewResponseContent($preparedArguments));
    }

    public function testRunSetsPositionTo_0_IfPositionIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName, '--position']);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedResponsePhpContent($preparedArguments), file_get_contents($this->expectedResponsePath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPositionIsNotNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName, '--position', 'FooBarBaz']));
    }

    public function testRunSetsPositionToSpecifiedPositionIfSpecifiedPositionIsNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName, '--position', '420']);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedResponsePhpContent($preparedArguments), file_get_contents($this->expectedResponsePath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfResponseAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        #ctype_alnum($string)
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName . '!@#$%^&*()_+=-\][\';"\\,.', '--for-app', $appName]);
        $this->expectException(RuntimeException::class);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetsNameToSpecifiedNameIfSpecifiedNameIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Response';
        $newResponse = new NewResponse();
        $preparedArguments = $newResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newResponse->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedResponsePhpContent($preparedArguments), file_get_contents($this->expectedResponsePath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewResponseContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedResponsePath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedResponsePath(array $preparedArguments): string
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
    private function determineExpectedResponsePhpContent(array $preparedArguments): string
    {
        return str_replace(['_NAME_', '_POSITION_'], [$preparedArguments['flags']['name'][0], ($preparedArguments['flags']['position'][0] ?? '0')], strval(file_get_contents($this->expectedTemplateFilePath())));
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Response.php';
    }

}
