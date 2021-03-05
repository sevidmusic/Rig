<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface;
use ddms\classes\ui\CommandLineUI;

final class CommandLineUITest extends TestCase
{

    private function getExpectedOuput(string $message): string
    {
        return $message;
    }

    public function testNotifyOutputsMessageFormattedForNOTICETypeIfNoNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new CommandLineUI();
        $this->expectOutputString($this->getExpectedOuput($message));
        $ui->showMessage($message);
    }

}
