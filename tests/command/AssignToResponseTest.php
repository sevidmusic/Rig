<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewRequest;
use ddms\classes\command\NewResponse;
use ddms\classes\command\AssignToResponse;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class AssignToResponseTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsExceptionIf_response_IsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $this->createTestRequestReturnName($appName);;
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--for-app', $appName, '--requests', $requestName]));
    }

    public function testRunThrowsExceptionIf_for_app_IsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $this->createTestRequestReturnName($appName);
        $responseName = $this->createTestResponseReturnName($appName);
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--response', $responseName, '--requests', $requestName]));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $this->createTestRequestReturnName($appName);
        $responseName = $this->createTestResponseReturnName($appName);
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--for-app', 'Foo' . strval(rand(420, 4200)),'--response', $responseName, '--requests', $requestName]));
    }

    public function testRunThrowsRuntimeExceptionIfAtLeastOneComponentToBeAssignedIsNotSpecified() : void
    {
        $appName = $this->createTestAppReturnName();
        $responseName = $this->createTestResponseReturnName($appName);
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--for-app', $appName, '--response', $responseName]));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedResponseDoesNotExist(): void
    {
        $appName = $this->createTestAppReturnName();
        $requestName = $this->createTestRequestReturnName($appName);;
        $newAssignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $newAssignToResponse->run(new CommandLineUI(), $newAssignToResponse->prepareArguments(['--for-app', $appName, '--response', 'Foo' . strval(rand(420, 4200)), '--requests', $requestName]));
    }

    private function createTestRequestReturnName(string $appName): string
    {
        $requestName = self::getRandomAppName() . 'Request';
        $newApp = new NewRequest();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $requestName, '--for-app', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        return $requestName;
    }

    private function createTestResponseReturnName(string $appName): string
    {
        $responseName = self::getRandomAppName() . 'Response';
        $newApp = new NewResponse();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        return $responseName;
    }

}
