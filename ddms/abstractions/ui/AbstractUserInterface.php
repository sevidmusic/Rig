<?php

namespace ddms\abstractions\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

abstract class AbstractUserInterface implements DDMSUserInterface
{

    public function notify(string $message, string $noticeType = self::NOTICE): void
    {
        echo $noticeType . ': ' . $message;
    }
}
