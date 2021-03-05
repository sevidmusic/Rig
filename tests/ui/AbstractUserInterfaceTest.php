<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface as DDMSUserInterface;
use ddms\abstractions\ui\AbstractUserInterface as AbstractDDMSUserInterface;

final class AbstractUserInterfaceTest extends TestCase
{

    public function testShowMessageOutputsSpecifiedMessage(): void {
        $ui = $this
            ->getMockBuilder(AbstractDDMSUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString('Foo');
        $ui->showMessage('Foo');
    }

}
