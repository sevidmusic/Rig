<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\factory\CommandFactory;
use rig\classes\command\MockRigCommand;
use rig\classes\command\Rig;
use rig\abstractions\ui\AbstractUserInterface;
use rig\interfaces\ui\UserInterface;
use rig\interfaces\command\Command;
use \RuntimeException;

final class RigTest extends TestCase
{

    public const MOCK_COMMAND_OUTPUT = 'Command Output';

    public function testRunCommandReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToSpecifiedCommandsRunMethod(): void
    {
        $this->expectOutputString(self::MOCK_COMMAND_OUTPUT . self::MOCK_COMMAND_OUTPUT);
        $this->assertEquals(
            $this->getMockRigCommand()->run($this->getMockUserInterface(), $this->getMockRigCommand()->prepareArguments([])),
            $this->getMockRig()->runCommand($this->getMockUserInterface(), $this->getMockRigCommand(), [])
        );
    }

    public function testRunReturnValueAndOutputMatchesReturnValueAndOutputOfIndpendantCallToRunMethodOfExistingCommandThatCorrespondsToFirstFlag(): void
    {
        $mockRigCommand = $this->getMockRigCommand();;
        $rig = $this->getMockRig();
        $ui = $this->getMockUserInterface();
        $argv = ['--mock-rig-command'];
        $this->expectOutputString(self::MOCK_COMMAND_OUTPUT . self::MOCK_COMMAND_OUTPUT);
        $this->assertEquals(
            $mockRigCommand->run($ui, $mockRigCommand->prepareArguments($argv)),
            $rig->run($ui, $rig->prepareArguments($argv))
        );
    }

    public function testRunOutputIncludesOutputOfUserInterface_showFlags_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_flags(): void
    {
        $mockRigCommand = $this->getMockRigCommand();
        $rig = $this->getMockRig();
        $argv = ['--mock-rig-command', '--debug', 'flags'];
        $this->expectOutputString(
            self::MOCK_COMMAND_OUTPUT .
            $this->expectedShowFlagsOutput($rig->prepareArguments($argv))
        );
        $rig->run($this->getMockUserInterface(), $rig->prepareArguments($argv));
    }

    public function testRunOutputIncludesOutputOfUserInterface_showOptions_MethodIf_debug_FlagsIsSpecifiedWithFlagArgument_options(): void
    {
        $mockRigCommand = $this->getMockRigCommand();
        $rig = $this->getMockRig();
        $argv = ['--mock-rig-command', '--debug', 'options'];
        $this->expectOutputString(
            self::MOCK_COMMAND_OUTPUT .
            $this->expectedShowOptionsOutput($rig->prepareArguments($argv))
        );
        $rig->run($this->getMockUserInterface(), $rig->prepareArguments($argv));
    }

    public function testRunOutputs_help_HelpFileContentsAndThrowsARuntimeExceptionIfFlagsAreSpecifiedAndFirstFlagDoesNotCorrespondToAnExistingCommand(): void
    {
        $rig = $this->getMockRig();
        $this->expectOutputString( $this->get_help_file_contents($this->determineHelpFilePath('help')) );
        $this->expectException(RuntimeException::class);
        $rig->run($this->getMockUserInterface(), $rig->prepareArguments(['--command-does-not-exist', 'flagArg1', 'flagArg2']));
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
        $mockUI = new MockRigUserInterface();
        return $mockUI->expectedShowOptionsOutput($arguments);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowFlagsOutput(array $arguments): string
    {
        $mockUI = new MockRigUserInterface();
        return $mockUI->expectedShowFlagsOutput($arguments);
    }

    private function determineHelpFilePath(string $helpFlagName): string
    {
        return str_replace('tests/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . $helpFlagName . '.txt';
    }

    private function getMockRigCommand(): Command
    {

        return new MockRigCommand();

    }

    private function getMockUserInterface(): AbstractUserInterface
    {
        return new MockRigUserInterface();
    }

    /**
     * @return array<mixed>
     */
    private function mockArgvArrayWithFlagsAndOptions(): array
    {
        return ['OPTION1', 'OPTION2', '--flag1', 'arg1', 'arg2', '--flag2', '--flag3', 'arg'];
    }

    private function getMockRig(): Rig
    {
        return new Rig(new CommandFactory());
    }

}

final class MockRigUserInterface extends AbstractUserInterface implements UserInterface
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
