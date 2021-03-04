<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\abstractions\command\AbstractDDMS as DDMS;
use ddms\abstractions\ui\AbstractUserInterface as DDMSUserInterface;
use ddms\abstractions\command\AbstractCommand as DDMSCommand;

final class AbstractDDMSTest extends TestCase
{
    public function testRunCommandReturnsValueMatchingValueReturnedOnIndependantCallToSpecifiedCommandsRunMethod(): void
    {

        $mockUserInterface = $this->getMockBuilder(DDMSUserInterface::class)
            ->getMockForAbstractClass();

        $mockCommand = $this->getMockBuilder(DDMSCommand::class)
            ->setMethods(['run'])
            ->getMockForAbstractClass();

        $mockCommand
            ->method('run')
            ->willReturn(true);

        $mockDDMS = $this->getMockBuilder(DDMS::class)
            ->getMockForAbstractClass();

        $this->assertEquals(
            $mockCommand->run($mockUserInterface, $mockCommand->prepareArguments([])),
            $mockDDMS->runCommand($mockUserInterface, $mockCommand, [])
        );
    }
}
