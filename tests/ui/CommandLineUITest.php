<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;

final class CommandLineUITest extends TestCase
{
    private const BANNER = 'banner';

    private function getExpectedOuputForNoticeType(string $message, string $noticeType = ''): string
    {
        return $message;
    }

    public function testNotifyOutputsMessageFormattedForNOTICETypeIfNoNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message);
        $this->expectOutputString($expectedOutput);
        $ui->showMessage($message);
    }

}
