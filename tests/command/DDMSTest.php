<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\factory\CommandFactory;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\abstractions\command\AbstractCommand;
use ddms\abstractions\ui\AbstractUserInterface;
use ddms\interfaces\ui\UserInterface;
use ddms\interfaces\command\Command;
use \RuntimeException;

final class DDMSTest extends TestCase
{

    private const MOCK_COMMAND_UI_OUTPUT = 'Testing';

    public function testRunCommandReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToSpecifiedCommandsRunMethod(): void
    {
        $this->assertEquals(
            $this->getMockDDMSCommand()->run($this->getMockUserInterface(), $this->getMockDDMSCommand()->prepareArguments([])),
            $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockDDMSCommand(), [])
        );
        $this->expectOutputString(self::MOCK_COMMAND_UI_OUTPUT . self::MOCK_COMMAND_UI_OUTPUT);
        $this->getMockDDMSCommand()->run($this->getMockUserInterface(), $this->getMockDDMSCommand()->prepareArguments($this->mockArgvArrayWithFlagsAndOptions()));
        $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockDDMSCommand(), $this->mockArgvArrayWithFlagsAndOptions());
    }

    public function testRunReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToRunMethodOfExistingCommandThatCorrespondsToFirstFlag(): void
    {
        $helpCommand = new Help();
        $ddms = $this->getMockDDMS();
        $ui = $this->getMockUserInterface();
        $argv = ['--help', '--flag1', '--flag2'];
        $this->expectOutputString(
            PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL .
            PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL
        );
        $this->assertEquals(
            $helpCommand->run($ui, $helpCommand->prepareArguments($argv)),
            $ddms->run($ui, $ddms->prepareArguments($argv))
        );
    }

    public function testRunOutputs_help_HelpFileContentsAndThrowsARuntimeExceptionIfFlagsAreSpecifiedAndFirstFlagDoesNotCorrespondToAnExistingCommand(): void
    {
        $ddms = $this->getMockDDMS();
        $this->expectOutputString(PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL);
        $this->expectException(RuntimeException::class);
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments(['--command-does-not-exist', 'flagArg1', 'flagArg2']));
    }

    private function determineHelpFilePath(string $helpFlagName): string
    {
        return str_replace('tests/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . $helpFlagName . '.txt';
    }

    private function getMockDDMSCommand(): AbstractCommand
    {

        return new MockDDMSCommand();

    }

    private function getMockUserInterface(): AbstractUserInterface
    {
        return $this->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

    }

    /**
     * @return array<mixed>
     */
    private function mockArgvArrayWithFlagsAndOptions(): array
    {
        return ['mockOutput', '--output', self::MOCK_COMMAND_UI_OUTPUT];
    }

    private function getMockDDMS(): DDMS
    {
        return new DDMS(new CommandFactory());
    }

}

final class MockDDMSCommand extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        if(in_array('mockOutput', $preparedArguments['options']) && isset($preparedArguments['flags']['output'][0])) {
            $userInterface->showMessage($preparedArguments['flags']['output'][0]);
        }
        return true;
    }

}
