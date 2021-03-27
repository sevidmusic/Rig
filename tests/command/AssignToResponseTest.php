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

    private string $appName;
    private string $requestName;
    private string $responseName;
    private UserInterface $ui;
    private AssignToResponse $assignToResponse;

    public function testRunThrowsExceptionIf_response_IsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsExceptionIf_for_app_IsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    self::getRandomAppName(),
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfAtLeastOneComponentToBeAssignedIsNotSpecified() : void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    $this->responseName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedResponseDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    self::getRandomAppName(),
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfAnyOfTheSpecifiedComponentsToBeAssignedDoNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName,
                    '--output-components',
                    self::getRandomAppName()
                ]
            )
        );
    }

    private function createTestRequestReturnName(string $appName, UserInterface $ui): string
    {
        $requestName = self::getRandomAppName() . 'Request';
        $newApp = new NewRequest();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $requestName, '--for-app', $appName]);
        $newApp->run($ui, $newAppPreparedArguments);
        return $requestName;
    }

    private function createTestResponseReturnName(string $appName, UserInterface $ui): string
    {
        $responseName = self::getRandomAppName() . 'Response';
        $newApp = new NewResponse();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $responseName, '--for-app', $appName]);
        $newApp->run($ui, $newAppPreparedArguments);
        return $responseName;
    }

    protected function setup(): void
    {
        $this->ui = new CommandLineUI();
        $this->appName = $this->createTestAppReturnName();
        $this->requestName = $this->createTestRequestReturnName(
            $this->appName,
            $this->ui
        );
        $this->responseName = $this->createTestResponseReturnName(
            $this->appName,
            $this->ui
        );
        $this->assignToResponse = new AssignToResponse();
    }

}
