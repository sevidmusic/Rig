<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewGlobalResponse;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

final class NewGlobalResponseTest extends TestCase
{

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--new-global-response']));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--new-global-response', '--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newGlobalResponse = new NewGlobalResponse();
        $this->expectException(RuntimeException::class);
        $newGlobalResponse->run(new CommandLineUI(), $newGlobalResponse->prepareArguments(['--new-global-response', '--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

}
