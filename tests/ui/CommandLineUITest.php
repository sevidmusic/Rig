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

}
