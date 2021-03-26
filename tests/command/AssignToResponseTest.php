<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
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
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--for-app', $appName]));
    }

    public function testRunThrowsExceptionIf_for_app_IsNotSpecified(): void
    {
        $assignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $assignToResponse->run(new CommandLineUI(), $assignToResponse->prepareArguments(['--response', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newAssignToResponse = new AssignToResponse();
        $this->expectException(RuntimeException::class);
        $newAssignToResponse->run(new CommandLineUI(), $newAssignToResponse->prepareArguments(['--for-app', 'Foo' . strval(rand(420, 4200)), '--response', 'Foo']));
    }

}
