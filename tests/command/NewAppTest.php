<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\ui\CommandLineUI;

final class NewAppTest extends TestCase
{
    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified() : void
    {
        $newApp = new NewApp();
        $ui = new CommandLineUI();
        $this->expectException(\RuntimeException::class);
        $newApp->run($ui, $newApp->prepareArguments(['--new-app']));
    }
}
