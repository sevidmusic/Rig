<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface;
use ddms\classes\ui\CommandLineUI;

final class CommandLineUITest extends TestCase
{

    public function testShowMessageOutputsSpecifiedMessage(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new CommandLineUI();
        $this->expectOutputString($message);
        $ui->showMessage($message);
    }

}
