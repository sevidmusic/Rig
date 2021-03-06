<?php

namespace ddms\classes\ui;

use ddms\interfaces\ui\UserInterface;

class CommandLineUI implements UserInterface
{

    public function showMessage(string $message): void
    {
        echo $message;
    }

}
