<?php

namespace ddms\abstractions\ui;

use ddms\interfaces\ui\UserInterface;

abstract class AbstractUserInterface implements UserInterface
{

    public function showMessage(string $message): void
    {
        echo $message;
    }
}
