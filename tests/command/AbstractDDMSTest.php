<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\command\Command;
use ddms\interfaces\ui\UserInterface;
use ddms\abstractions\command\AbstractDDMS;
use ddms\abstractions\ui\AbstractUserInterface;
use ddms\abstractions\command\AbstractCommand;

final class AbstractDDMSTest extends TestCase
{

    public const MOCK_COMMAND_UI_OUTPUT = 'Testing';

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
        $this->getMockCommand()->run($this->getMockUserInterface(), $this->getMockCommand()->prepareArguments(['mockOutput']));
        $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockCommand(), ['mockOutput']);
    }

    private function getMockUserInterface(): AbstractUserInterface
    {
        return $this->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

    }

    private function getMockCommand(): AbstractCommand
    {

        return new MockCommand();

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

final class MockCommand extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        if(in_array('mockOutput', $preparedArguments['options'])) {
            echo AbstractDDMSTest::MOCK_COMMAND_UI_OUTPUT;
        }
        return true;
    }

}
