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

    public function expectedBanner():string
    {
        return
        "
      \e[0m\e[94m    _    _\e[0m
      \e[0m\e[93m __| |__| |_ __  ___\e[0m
      \e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
      \e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
      \e[0m\e[105m\e[30m  v0.0.3  \e[0m\e[0m\e[101m\e[30m  " . date('h:i:s A') . "  \e[0m";
    }

}
