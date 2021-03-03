<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help as DDMSHelpCommand;
use ddms\classes\ui\CommandLineUI as DDMSUserInterface;
use ddms\classes\factory\CommandFactory as DDMSCommandFactory;;

final class CommandFactoryTest extends TestCase
{

    public function testGetCommandInstanceReturnsDDMSHelpCommandInstanceIfClassCorrespondingToSpecifiedCommandNameDoesNotExist(): void {
        $commandFactory = new DDMSCommandFactory();
        $this->assertTrue($commandFactory->getCommandInstance('Foo' . rand(10000,20000) . 'Bar' . rand(1000, 2000), new DDMSUserInterface()) instanceof DDMSHelpCommand);
    }

    public function testGetCommandInstanceReturnsDDMSHelpCommandInstanceIfSpecifiedCommandNameIs_help(): void {
        $commandFactory = new DDMSCommandFactory();
        $this->assertTrue($commandFactory->getCommandInstance('help', new DDMSUserInterface()) instanceof DDMSHelpCommand);
    }

}
