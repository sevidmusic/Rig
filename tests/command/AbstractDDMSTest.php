<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\abstractions\command\AbstractDDMS;
use ddms\abstractions\ui\AbstractUserInterface;
use ddms\abstractions\command\AbstractCommand;

final class AbstractDDMSTest extends TestCase
{

    private const MOCK_COMMAND_UI_OUTPUT = 'Testing';

    public function testRunCommandReturnsValueMatchingValueReturnedOnIndependantCallToSpecifiedCommandsRunMethod(): void
    {
        $this->assertEquals(
            $this->getMockCommand()->run($this->getMockUserInterface(), $this->getMockCommand()->prepareArguments([])),
            $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockCommand(), [])
        );
    }

    public function testRunCommandOutputMatchesOutputOfIndpendantCallToSpecifiedCommandsRunMethod(): void
    {
        $this->expectOutputString(self::MOCK_COMMAND_UI_OUTPUT . self::MOCK_COMMAND_UI_OUTPUT);
        $this->getMockCommand(true)->run($this->getMockUserInterface(), $this->getMockCommand()->prepareArguments([]));
        $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockCommand(true), []);
    }

    private function getMockUserInterface(): AbstractUserInterface
    {
        return $this->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

    }

    private function getMockCommand(bool $mockOutput = false): AbstractCommand
    {
        $mockCommand = $this->getMockBuilder(AbstractCommand::class)
            ->setMethods(['run'])
            ->getMockForAbstractClass();

        $mockCommand
            ->method('run')
            ->will($this->returnValue(
                $this->mockRunMethod($mockOutput)
            ));

        return $mockCommand;

    }

    private function mockRunMethod(bool $mockOutput = false): bool
    {
        if($mockOutput === true) { echo self::MOCK_COMMAND_UI_OUTPUT; }
        return true;
    }

    private function getMockDDMS(): AbstractDDMS
    {
        return $this->getMockBuilder(AbstractDDMS::class)
            ->getMockForAbstractClass();
    }

}
