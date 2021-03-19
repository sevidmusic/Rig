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
        $appName = self::getRandomAppName();
        $newApp = new NewApp();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        $responseName = $appName . 'GlobalResponse';
        $newGlobalResponse = new NewGlobalResponse();
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--name', $responseName, '--for-app', $appName]));
        $this->assertTrue(file_exists($this->expectedGlobalResponsePath($newAppPreparedArguments, $responseName)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedGlobalResponsePath(array $preparedArguments, string $responseName): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Responses' . DIRECTORY_SEPARATOR . $responseName . '.php';
    }
}
