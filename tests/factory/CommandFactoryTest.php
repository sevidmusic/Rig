<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help;
use ddms\classes\ui\CommandLineUI;
use ddms\classes\factory\CommandFactory;

final class CommandFactoryTest extends TestCase
{

    public function testGetCommandInstanceReturnsHelpInstanceIfClassCorrespondingToSpecifiedCommandNameDoesNotExist(): void {
        $commandName = $this->getRandomName();
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance($commandName, new CommandLineUI())
            instanceof Help
        );
    }

    public function testGetCommandInstanceReturnsInstanceOfSpecifiedCommandIfSpecifiedCommandExists(): void {
        $commandName = $this->getRandomExistingCommandName();
        $expectedCommandNamespace = 'ddms\\classes\\command\\' . $commandName;
        $this->assertTrue(
            $this->getCommandFactoryInstance()->getCommandInstance($commandName, new CommandLineUI())
            instanceof $expectedCommandNamespace
        );
    }

    private function getRandomExistingCommandName(): string
    {
        $existinCommandNames = ['help'];
        return $existinCommandNames[array_rand($existinCommandNames)];
    }

    private function getCommandFactoryInstance(): CommandFactory
    {
        return new CommandFactory();
    }

    private function getRandomName(): string {
        return 'Foo' . rand(10000,20000) . 'Bar' . rand(1000, 2000);
    }

}
