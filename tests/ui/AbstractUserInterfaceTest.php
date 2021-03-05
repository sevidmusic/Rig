<?php

namespace tests\ui;

use PHPUnit\Framework\TestCase;
use ddms\interfaces\ui\UserInterface;
use ddms\abstractions\ui\AbstractUserInterface;

final class AbstractUserInterfaceTest extends TestCase
{

    public function testShowMessageOutputsSpecifiedMessage(): void {
        $ui = $this
            ->getMockBuilder(AbstractUserInterface::class)
            ->getMockForAbstractClass();

        $this->expectOutputString('Foo');
        $ui->showMessage('Foo');
    }

}
