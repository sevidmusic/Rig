<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\command\Help;
use rig\classes\ui\CommandLineUI;

final class HelpTest extends TestCase
{

    public function testRunOutputsHelpFile_Help_IfNoFlagsOrOptionsAreSpecified(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('help'));
        $this->getHelpInstance()->run(new CommandLineUI());
    }

    public function testRunHasNoOutputsAndReturnsFalseIfFlagsAreSpecifiedButHelpFlagIsNotPresent(): void
    {
        $this->expectOutputString('');
        $this->assertFalse(
            $this->getHelpInstance()->run(
                new CommandLineUI(),
                ['flags' => ['flag1' => ['arg'], 'flag2' => ['arg']], 'options' => ['option']]
            )
        );
    }

    public function testRunOutputsHelpFile_Help_IfHelpFlagIsPresentAndNotFollowedByAnArgumentOrFlagThatCorrespondsToAHelpFile(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('help'));
        $this->getHelpInstance()->run(new CommandLineUI(), ['flags' => ['help' => ['foo'], 'flag1' => ['arg']], 'options' => ['option']]);
    }

    public function testRunOutputsHelpFileForSpecifiedArgumentIfHelpFlagIsPresentAndFirstArgumentCorrespondsToAHelpFile(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('view-active-servers'));
        $this->getHelpInstance()->run(new CommandLineUI(), ['flags' => ['help' => ['view-active-servers'], 'flag' => []], 'options' => []]);
    }

    public function testRunOutputsHelpFileForSpecifiedFlagIfHelpFlagIsPresentAndHasNoArgumentsAndFirstFlagAfterHelpFlagCorrespondsToAHelpFile(): void
    {
        $this->expectOutputString($this->expectedHelpFileOutput('view-active-servers'));
        $this->getHelpInstance()->run(new CommandLineUI(), ['flags' => ['help' => [], 'view-active-servers' => [], 'flag' => []], 'options' => []]);
    }

    private function expectedHelpFileOutput(string $helpFlagName): string
    {
        return ($this->getHelpFileContents($helpFlagName) ?? '');
    }

    private function getHelpFileContents(string $helpFlagName): string|null
    {
        if(file_exists($this->expectedHelpFilePath($helpFlagName)))
        {
            $output = file_get_contents($this->expectedHelpFilePath($helpFlagName));
            return (is_string($output) ? $output : '');
        }
        return null;
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
