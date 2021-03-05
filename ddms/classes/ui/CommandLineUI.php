<?php

namespace ddms\classes\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

class CommandLineUI implements DDMSUserInterface
{

    public const BANNER = 'banner';

    public function showMessage(string $message): void
    {
        echo $message;
    }

}
