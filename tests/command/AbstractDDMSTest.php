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
        $this->assertEquals(
            $this->getMockCommand()->run($this->getMockUserInterface(), $this->getMockCommand()->prepareArguments([])),
            $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockCommand(), [])
        );
    }

    private function getMockUserInterface(): AbstractUserInterface
    {
        return $this->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

    }

    private function getMockCommand(): AbstractCommand
    {
        $mockCommand = $this->getMockBuilder(AbstractCommand::class)
            ->setMethods(['run'])
            ->getMockForAbstractClass();

        $mockCommand
            ->method('run')
            ->willReturn(true);

        return $mockCommand;

    }

    private function getMockDDMS(): AbstractDDMS
    {
        return $this->getMockBuilder(AbstractDDMS::class)
            ->getMockForAbstractClass();
    }

}
