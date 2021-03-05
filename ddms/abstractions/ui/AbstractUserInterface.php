<?php

namespace ddms\abstractions\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

abstract class AbstractUserInterface implements DDMSUserInterface
{

    public function showMessage(string $message): void
    {
        echo $message;
    }
}
