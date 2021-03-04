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
            ->getMock();

        $mockCommand = $this->getMockBuilder(DDMSCommand::class)
            ->setMethods(['run'])
            ->getMock();

        $mockCommand->method('run')
            ->willReturn(true);

        $mockDDMS = $this->getMockForAbstractClass(DDMS::class);
        $mockDDMS->expects($this->any())->method('run')
            ->will($this->returnValue(false)
        );

        $this->assertEquals(
            $mockCommand->run($mockUserInterface, $mockCommand->prepareArguments([])),
            $mockDDMS->runCommand($mockUserInterface, $mockCommand, [])
        );
    }
}
