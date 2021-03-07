<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\factory\CommandFactory;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\classes\ui\CommandLineUI;
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

    public function testRunOutputIncludesOutputOfUserInterface_showFlags_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_flags(): void
    {
        $helpCommand = new Help();
        $ddms = $this->getMockDDMS();
        $argv = ['--help', '--debug', 'flags'];
        $this->expectOutputString(
            PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL .
            $this->expectedShowFlagsOutput($ddms->prepareArguments($argv))
        );
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments($argv));
    }

    public function testRunOutputIncludesOutputOfUserInterface_showOptions_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_options(): void
    {
        $helpCommand = new Help();
        $ddms = $this->getMockDDMS();
        $argv = ['--help', '--debug', 'options'];
        $this->expectOutputString(
            PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL .
            $this->expectedShowOptionsOutput($ddms->prepareArguments($argv))
        );
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments($argv));
    }

    public function testRunOutputs_help_HelpFileContentsAndThrowsARuntimeExceptionIfFlagsAreSpecifiedAndFirstFlagDoesNotCorrespondToAnExistingCommand(): void
    {
        $ddms = $this->getMockDDMS();
        $this->expectOutputString(PHP_EOL . file_get_contents($this->determineHelpFilePath('help')) . PHP_EOL);
        $this->expectException(RuntimeException::class);
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments(['--command-does-not-exist', 'flagArg1', 'flagArg2']));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowOptionsOutput(array $arguments): string
    {
        $mockUI = new MockDDMSUserInterface();
        return $mockUI->expectedShowOptionsOutput($arguments);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowFlagsOutput(array $arguments): string
    {
        $mockUI = new MockDDMSUserInterface();
        return $mockUI->expectedShowFlagsOutput($arguments);
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
        return new MockDDMSUserInterface();
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

final class MockDDMSUserInterface extends AbstractUserInterface implements UserInterface
{
    public function showMessage(string $message): void
    {
        echo $message;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showOptions(array $arguments): void
    {
        $this->showMessage(PHP_EOL . '  Options:' . PHP_EOL);
        foreach($arguments['options'] as $key => $option) {
            $this->showMessage("    $key : $option" . PHP_EOL);
        }
        $this->showMessage(PHP_EOL);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowOptionsOutput(array $arguments): string
    {
        $output = (PHP_EOL . '  Options:' . PHP_EOL);
        foreach($arguments['options'] as $key => $option) {
            $output .= ("    $key : $option" . PHP_EOL);
        }
        $output .= (PHP_EOL);
        return $output;
    }


    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showFlags(array $arguments): void
    {
        $this->showMessage('  Flags:' . PHP_EOL);
        foreach($arguments['flags'] as $key => $flags) {
            $this->showMessage("--$key" . " : ");
            foreach($flags as $key => $flagArgument) {
                $this->showMessage(PHP_EOL . "$flagArgument, ");
            }
            $this->showMessage(PHP_EOL);
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowFlagsOutput(array $arguments): string
    {
        $output = ('  Flags:' . PHP_EOL);
        foreach($arguments['flags'] as $key => $flags) {
            $output .= ("--$key" . " : ");
            foreach($flags as $key => $flagArgument) {
                $output .= (PHP_EOL . "$flagArgument, ");
            }
            $output .= (PHP_EOL);
        }
        return $output;
    }

}
