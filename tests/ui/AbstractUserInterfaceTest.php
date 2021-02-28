<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\abstractions\ui\AbstractUserInterface as AbstractDDMSUserInterface;

final class AbstractUserInterfaceTest extends TestCase
{

    public function testNotifyOutputsNoticeTypeNOTICEColonSpaceMessageIfNoNoticeTypeIsSpecified(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString(DDMSUserInterface::NOTICE . ': Foo');
        $ui->notify('Foo');
    }

    public function testNotifyOutputsNoticeTypeNOTICEColonSpaceMessageIfTheNOTICENoticeTypeIsSpecified(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString(DDMSUserInterface::NOTICE . ': Foo');
        $ui->notify('Foo', DDMSUserInterface::NOTICE);
    }

    public function testNotifyOutputsNoticeTypeERRORColonSpaceMessageIfTheERRORNoticeTypeIsSpecified(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString(DDMSUserInterface::ERROR . ': Foo');
        $ui->notify('Foo', DDMSUserInterface::ERROR);
    }

    public function testNotifyOutputsNoticeTypeWARNINGColonSpaceMessageIfTheWARNINGNoticeTypeIsSpecified(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString(DDMSUserInterface::WARNING . ': Foo');
        $ui->notify('Foo', DDMSUserInterface::WARNING);
    }

    public function testNotifyOutputsNoticeTypeSUCCESSColonSpaceMessageIfTheSUCCESSNoticeTypeIsSpecified(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString(DDMSUserInterface::SUCCESS . ': Foo');
        $ui->notify('Foo', DDMSUserInterface::SUCCESS);
    }

}
