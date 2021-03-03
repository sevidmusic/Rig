<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help as HelpCommand;
use ddms\classes\ui\CommandLineUI as DDMSUserInterface;

final class HelpTest extends TestCase
{

    public function testRunOutputsHelpFile_Help_IfNoFlagsOrOptionsAreSpecified(): void
    {
        $this->expectOutputString($this->expectedOutput('help'));
        $this->getMockCommand()->run(new DDMSUserInterface());
    }

    public function testRunOutputsEmptyStringIfFlagsAreSpecifiedAndHelpFlagIsNotPresent(): void
    {
        $this->expectOutputString('');
        $this->getMockCommand()->run(new DDMSUserInterface(), ['flags' => ['flag' => []], 'options' => []]);
    }

    public function testRunOutputsHelpFile_Help_IfHelpFlagIsTheOnlyFlagSpecifiedAndHasNoArguments(): void
    {
        $this->expectOutputString($this->expectedOutput('help'));
        $this->getMockCommand()->run(new DDMSUserInterface(), ['flags' => ['help' => []], 'options' => []]);
    }

    private function expectedHelpFileOutput(string $helpFlagName): string
    {
        if(file_exists($this->expectedHelpFilePath($helpFlagName)))
        {
            $output = file_get_contents($this->expectedHelpFilePath($helpFlagName));
        }
        return (isset($output) && is_string($output) ? PHP_EOL . "\e[0m\e[45m\e[30m" . $output . "\e[0m" . PHP_EOL : '');
    }

    private function expectedHelpFilePath(string $helpFlagName): string
    {
        return str_replace('tests/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . $helpFlagName . '.txt';
    }

    private function getMockCommand(): HelpCommand
    {
        return new HelpCommand();
    }

    private function expectedOutput(string $helpFlagName): string
    {
         return sprintf(
            "%s%s%s%s%s%s%s%s%s%s%s",
            PHP_EOL,
            "\e[0m\e[105m\e[30m    ",
            "\e[0m\e[92m " . date('Y-m-d @ H:i:s') . "  \e[0m ",
            "\e[0m\e[102m\e[30m" . 'help' . "\e[0m\e[105m\e[30m    \e[0m",
            PHP_EOL,
            PHP_EOL,
            "\e[0m\e[107m\e[30m",
            $this->expectedHelpFileOutput($helpFlagName),
            "\e[0m",
            PHP_EOL,
            PHP_EOL,
        );

    }

}
