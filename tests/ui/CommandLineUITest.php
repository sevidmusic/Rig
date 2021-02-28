<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;

final class CommandLineUITest extends TestCase
{

    private function getExpectedOuputForNoticeType(string $message, string $noticeType = ''): string
    {
         return sprintf(
            "%s%s%s%s%s%s%s%s%s%s%s",
            PHP_EOL,
            "\e[0m\e[105m\e[30m    ",
            "\e[0m\e[92m 2021-28-Feb@10:02:31\e[0m ",
            "\e[0m\e[102m\e[30m" . (empty($noticeType) ? DDMSCommandLineUI::NOTICE : $noticeType) . "\e[0m\e[105m\e[30m    \e[0m",
            PHP_EOL,
            PHP_EOL,
            "\e[0m\e[107m\e[30m",
            $message,
            "\e[0m",
            PHP_EOL,
            PHP_EOL,
        );

    }

    public function testNotifyOutputsMessageFormattedForNOTICETypeIfNoNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message);
        $this->expectOutputString($expectedOutput);
        $ui->notify($message);
    }

    public function testNotifyOutputsMessageFormattedForNOTICETypeIfNOTICENoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message, DDMSCommandLineUI::NOTICE);
        $this->expectOutputString($expectedOutput);
        $ui->notify($message, DDMSCommandLineUI::NOTICE);
    }

    public function testNotifyOutputsMessageFormattedForERRORTypeIfERRORNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message, DDMSCommandLineUI::ERROR);
        $this->expectOutputString($expectedOutput);
        $ui->notify($message, DDMSCommandLineUI::ERROR);
    }

    public function testNotifyOutputsMessageFormattedForWARNINGTypeIfWARNINGNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message, DDMSCommandLineUI::WARNING);
        $this->expectOutputString($expectedOutput);
        $ui->notify($message, DDMSCommandLineUI::WARNING);
    }

    public function testNotifyOutputsMessageFormattedForSUCCESSTypeIfSUCCESSNoticeTypeIsSpecified(): void {
        $message = "Foo bar baz. Bazzer foo bar baz bazzer.";
        $ui = new DDMSCommandLineUI();
        $expectedOutput = $this->getExpectedOuputForNoticeType($message, DDMSCommandLineUI::SUCCESS);
        $this->expectOutputString($expectedOutput);
        $ui->notify($message, DDMSCommandLineUI::SUCCESS);
    }
}
