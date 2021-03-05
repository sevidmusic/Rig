<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\abstractions\command\AbstractDDMS;
use ddms\abstractions\ui\AbstractUserInterface;
use ddms\abstractions\command\AbstractCommand;

final class AbstractDDMSTest extends TestCase
{
    public function testRunCommandReturnsValueMatchingValueReturnedOnIndependantCallToSpecifiedCommandsRunMethod(): void
    {

        $mockUserInterface = $this->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

        $mockCommand = $this->getMockBuilder(AbstractCommand::class)
            ->setMethods(['run'])
            ->getMockForAbstractClass();

        $mockCommand
            ->method('run')
            ->willReturn(true);

        $mockDDMS = $this->getMockBuilder(AbstractDDMS::class)
            ->getMockForAbstractClass();

        $this->assertEquals(
            $mockCommand->run($mockUserInterface, $mockCommand->prepareArguments([])),
            $mockDDMS->runCommand($mockUserInterface, $mockCommand, [])
        );
    }
}
