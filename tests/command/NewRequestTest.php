<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\command\NewRequest;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewRequestTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newRequest = new NewRequest();
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $newRequest->prepareArguments([]));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newRequest = new NewRequest();
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $newRequest->prepareArguments(['--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newRequest = new NewRequest();
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $newRequest->prepareArguments(['--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewRequestForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedRequestPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfRequestAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        #ctype_alnum($string)
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $responseName . '!@#$%^&*()_+=-\][\';"\\,.', '--for-app', $appName]);
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetsNameToSpecifiedNameIfSpecifiedNameIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), file_get_contents($this->expectedRequestPath($preparedArguments)));
    }

    public function testRunSetsContainerTo_Requests_IfContainerIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName]);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), $this->getNewRequestContent($preparedArguments));
    }

    public function testRunSetsContainerTo_Requests_IfContainerIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName, '--container']);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), file_get_contents($this->expectedRequestPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedContainerIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName, '--container', 'FooBarBaz*#$%*']));
    }

    public function testRunSetsContainerToSpecifiedContainerIfSpecifiedContainerIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName, '--container', 'ValidContainerName']);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), file_get_contents($this->expectedRequestPath($preparedArguments)));
    }

    public function testRunSetsRelativeUrlTo_index_php_IfRelativeUrlIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName]);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), $this->getNewRequestContent($preparedArguments));
    }

    public function testRunThrowsRuntimeExceptionIfRelativeUrlIsNotValid(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $this->expectException(RuntimeException::class);
        $newRequest->run(new CommandLineUI(), $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName, '--relative-url', $this->getRandomInvalidRelativeUrl()]));
    }

    public function testRunSetsRelativeUrlToSpecifiedRelativeUrlIfSpecifiedRelativeUrlIsValid(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $appName . 'Request';
        $newRequest = new NewRequest();
        $preparedArguments = $newRequest->prepareArguments(['--name', $requestName, '--for-app', $appName, '--relative-url', 'index.php?foo=bar&baz=biz']);
        $newRequest->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedRequestPhpContent($preparedArguments), file_get_contents($this->expectedRequestPath($preparedArguments)));
    }

    private function getRandomInvalidRelativeUrl(): string
    {
        $invalidUrls = [
            'http://localhost:' . strval(rand(8000, 8999)) . '/index.php',
            'localhost:' . strval(rand(8000, 8999)) . '/index.php',
            'localhost/index.php',
        ];
        return $invalidUrls[array_rand($invalidUrls)];
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewRequestContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedRequestPath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedRequestPath(array $preparedArguments): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['name'][0] . '.php';
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
    private function determineExpectedRequestPhpContent(array $preparedArguments): string
    {
        return str_replace(
            [
                '_NAME_',
                '_CONTAINER_',
                '_RELATIVE_URL_'
            ],
            [
                $preparedArguments['flags']['name'][0],
                ($preparedArguments['flags']['container'][0] ?? 'Requests'),
                ($preparedArguments['flags']['relative-url'][0] ?? 'index.php')
            ],
            strval(file_get_contents($this->expectedTemplateFilePath()))
        );
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Request.php';
    }

}
