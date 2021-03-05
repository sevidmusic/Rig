<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\Help;
use ddms\classes\ui\CommandLineUI;

final class HelpTest extends TestCase
{

    public function testRunOutputsHelpFile_Help_IfNoFlagsOrOptionsAreSpecified(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('help'));
        $this->getHelpInstance()->run(new CommandLineUI());
    }

    public function testRunOutputsEmptyStringIfFlagsAreSpecifiedAndHelpFlagIsNotPresent(): void
    {
        $this->expectOutputString('');
        $this->getHelpInstance()->run(new CommandLineUI(), ['flags' => ['flag' => []], 'options' => []]);
    }

    public function testRunOutputsHelpFile_Help_IfHelpFlagIsSpecifiedAndHasNoArguments(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('help'));
        $this->getHelpInstance()->run(new CommandLineUI(), ['flags' => ['help' => [], 'flag' => []], 'options' => []]);
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

    private function getHelpInstance(): Help
    {
        return new Help();
    }

}
