<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help;
use ddms\classes\ui\CommandLineUI;
use ddms\classes\factory\CommandFactory;

final class CommandFactoryTest extends TestCase
{

    public function testGetCommandInstanceReturnsHelpInstanceIfClassCorrespondingToSpecifiedCommandNameDoesNotExist(): void {
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance($this->getRandomName(), new CommandLineUI())
            instanceof Help
        );
    }

    public function testGetCommandInstanceReturnsHelpInstanceIfSpecifiedCommandNameIs_help(): void {
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance('help', new CommandLineUI())
            instanceof Help
        );
    }

    private function getCommandFactoryInstance(): CommandFactory
    {
        return new CommandFactory();
    }

    private function getRandomName(): string {
        return 'Foo' . rand(10000,20000) . 'Bar' . rand(1000, 2000);
    }

}
