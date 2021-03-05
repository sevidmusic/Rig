<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help as DDMSHelpCommand;
use ddms\classes\ui\CommandLineUI as DDMSUserInterface;
use ddms\classes\factory\CommandFactory as DDMSCommandFactory;;

final class CommandFactoryTest extends TestCase
{

    public function testGetCommandInstanceReturnsDDMSHelpCommandInstanceIfClassCorrespondingToSpecifiedCommandNameDoesNotExist(): void {
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance($this->getRandomName(), new DDMSUserInterface())
            instanceof DDMSHelpCommand
        );
    }

    public function testGetCommandInstanceReturnsDDMSHelpCommandInstanceIfSpecifiedCommandNameIs_help(): void {
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance('help', new DDMSUserInterface())
            instanceof DDMSHelpCommand
        );
    }

    private function getCommandFactoryInstance(): DDMSCommandFactory
    {
        return new DDMSCommandFactory();
    }

    private function getRandomName(): string {
        return 'Foo' . rand(10000,20000) . 'Bar' . rand(1000, 2000);
    }

}
