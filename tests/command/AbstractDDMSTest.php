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

        $mockCommand = $this->getMockBuilder(DDMSCommand::class)
            ->setMethods(['run'])
            ->getMock();

        $mockDDMS = $this->getMockBuilder(DDMS::class)
            ->setMethods(['run'])
            ->getMock();

        $mockUserInterface = $this->getMockBuilder(DDMSUserInterface::class)
            ->getMock();

        $this->assertEquals(
            $mockCommand->run($mockUserInterface, $mockCommand->prepareArguments([])),
            $mockDDMS->runCommand($mockUserInterface, $mockCommand, [])
        );
    }
}
