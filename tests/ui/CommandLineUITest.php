<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface;
use ddms\classes\ui\CommandLineUI;

final class CommandLineUITest extends TestCase
{

    public function testShowMessageOutputsSpecifiedMessage(): void {
        $message = PHP_EOL . "\e[0m\e[102m\e[30m    Command Line UI " . rand(1000, PHP_INT_MAX) . "    \e[0m" . PHP_EOL . PHP_EOL;
        $ui = new CommandLineUI();
        $this->expectOutputString($message);
        $ui->showMessage($message);
    }

    public function testShowBannerOutputsDDMSBanner(): void
    {
        $this->expectOutputString($this->expectedBanner());
        $ui = new CommandLineUI();
        $ui->showBanner();
    }

    public function testShowOptionsOutputsExpectedOutput(): void
    {
        $arguments = ['flags' => [], 'options' => ['foo', 'bar', 'baz']];
        $this->expectOutputString($this->expectedShowOptionsOutput($arguments));
        $ui = new CommandLineUI();
        $ui->showOptions($arguments);
    }

    public function testShowFlagsOutputsExpectedOutput(): void
    {
        $arguments = ['flags' => [], 'options' => ['foo', 'bar', 'baz']];
        $this->expectOutputString($this->expectedShowFlagsOutput($arguments));
        $ui = new CommandLineUI();
        $ui->showFlags($arguments);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    private function expectedShowOptionsOutput(array $arguments): string
    {
        $output = (PHP_EOL . '  Options:' . PHP_EOL);
        foreach($arguments['options'] as $key => $option) {
            $output .= ("\e[0m  \e[101m\e[30m $key \e[0m\e[105m\e[30m : \e[0m\e[104m\e[30m $option \e[0m" . PHP_EOL);
        }
        $output .= (PHP_EOL);
        return $output;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function expectedShowFlagsOutput(array $arguments): string
    {
        $output = ('  Flags:' . PHP_EOL);
        foreach($arguments['flags'] as $key => $flags) {
            $output .= ("\e[0m  \e[101m\e[30m --$key \e[0m" . " : ");
            foreach($flags as $key => $flagArgument) {
                $output .= ("\e[0m\e[104m\e[30m $flagArgument \e[0m" . "  ");
            }
            $output .= (PHP_EOL);
        }
        return $output;
    }

    private function expectedBanner():string
    {
        return
        "
  \e[0m\e[94m    _    _\e[0m
  \e[0m\e[93m __| |__| |_ __  ___\e[0m
  \e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
  \e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
  \e[0m\e[105m\e[30m  v0.0.9-beta  \e[0m\e[0m\e[101m\e[30m  " . date('h:i:s A') . "  \e[0m" . PHP_EOL . PHP_EOL;
    }

}
