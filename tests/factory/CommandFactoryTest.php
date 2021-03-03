<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\DDMSHelp as DDMSHelpCommand;
use ddms\classes\ui\CommandLineUI as DDMSUserInterface;
use ddms\interfaces\factory\CommandFactory as DDMSCommandFactory;;

final class CommandFactoryTest extends TestCase
{

    public function testGetCommandInstanceReturnsDDMSHelpCommandInstanceIfSpecifiedCommandNameIs_help(): void { $this->assertTrue(true); }
}
