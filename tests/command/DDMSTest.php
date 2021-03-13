<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\factory\CommandFactory;
use ddms\classes\command\MockDDMSCommand;
use ddms\classes\command\DDMS;
use ddms\abstractions\ui\AbstractUserInterface;
use ddms\interfaces\ui\UserInterface;
use ddms\interfaces\command\Command;
use \RuntimeException;

final class DDMSTest extends TestCase
{

    public const MOCK_COMMAND_OUTPUT = 'Command Output';

    public function testRunCommandReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToSpecifiedCommandsRunMethod(): void
    {
        $this->expectOutputString(self::MOCK_COMMAND_OUTPUT . self::MOCK_COMMAND_OUTPUT);
        $this->assertEquals(
            $this->getMockDDMSCommand()->run($this->getMockUserInterface(), $this->getMockDDMSCommand()->prepareArguments([])),
            $this->getMockDDMS()->runCommand($this->getMockUserInterface(), $this->getMockDDMSCommand(), [])
        );
    }

    public function testRunReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToRunMethodOfExistingCommandThatCorrespondsToFirstFlag(): void
    {
        $mockDDMSCommand = $this->getMockDDMSCommand();;
        $ddms = $this->getMockDDMS();
        $ui = $this->getMockUserInterface();
        $argv = ['--mock-ddms-command'];
        $this->expectOutputString(self::MOCK_COMMAND_OUTPUT . self::MOCK_COMMAND_OUTPUT);
        $this->assertEquals(
            $mockDDMSCommand->run($ui, $mockDDMSCommand->prepareArguments($argv)),
            $ddms->run($ui, $ddms->prepareArguments($argv))
        );
    }

    public function testRunOutputIncludesOutputOfUserInterface_showFlags_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_flags(): void
    {
        $mockDDMSCommand = $this->getMockDDMSCommand();
        $ddms = $this->getMockDDMS();
        $argv = ['--mock-ddms-command', '--debug', 'flags'];
        $this->expectOutputString(
            self::MOCK_COMMAND_OUTPUT .
            $this->expectedShowFlagsOutput($ddms->prepareArguments($argv))
        );
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments($argv));
    }

    public function testRunOutputIncludesOutputOfUserInterface_showOptions_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_options(): void
    {
        $mockDDMSCommand = $this->getMockDDMSCommand();
        $ddms = $this->getMockDDMS();
        $argv = ['--mock-ddms-command', '--debug', 'options'];
        $this->expectOutputString(
            self::MOCK_COMMAND_OUTPUT .
            $this->expectedShowOptionsOutput($ddms->prepareArguments($argv))
        );
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments($argv));
    }

    public function testRunOutputs_help_HelpFileContentsAndThrowsARuntimeExceptionIfFlagsAreSpecifiedAndFirstFlagDoesNotCorrespondToAnExistingCommand(): void
    {
        $ddms = $this->getMockDDMS();
        $this->expectOutputString( $this->get_help_file_contents($this->determineHelpFilePath('help')) );
        $this->expectException(RuntimeException::class);
        $ddms->run($this->getMockUserInterface(), $ddms->prepareArguments(['--command-does-not-exist', 'flagArg1', 'flagArg2']));
    }

    private function get_help_file_contents(string $path):string {
        if(file_exists($path)) {
            $output = file_get_contents($path);
            return strval($output);
        }
        return '';
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

    private function getMockDDMSCommand(): Command
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
        return ['OPTION1', 'OPTION2', '--flag1', 'arg1', 'arg2', '--flag2', '--flag3', 'arg'];
    }

    private function getMockDDMS(): DDMS
    {
        return new DDMS(new CommandFactory());
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
        $this->showMessage( '  Options:' );
        foreach($arguments['options'] as $key => $option) {
            $this->showMessage("    $key : $option" );
        }

    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowOptionsOutput(array $arguments): string
    {
        $output = ( '  Options:' );
        foreach($arguments['options'] as $key => $option) {
            $output .= ("    $key : $option" );
        }
        return $output;
    }


    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showFlags(array $arguments): void
    {
        $this->showMessage('  Flags:' );
        foreach($arguments['flags'] as $key => $flags) {
            $this->showMessage("--$key" . " : ");
            foreach($flags as $key => $flagArgument) {
                $this->showMessage( "$flagArgument, ");
            }

        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowFlagsOutput(array $arguments): string
    {
        $output = ('  Flags:' );
        foreach($arguments['flags'] as $key => $flags) {
            $output .= ("--$key" . " : ");
            foreach($flags as $key => $flagArgument) {
                $output .= ( "$flagArgument, ");
            }
        }
        return $output;
    }

}
