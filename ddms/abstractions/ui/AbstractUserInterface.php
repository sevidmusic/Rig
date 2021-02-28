<?php

namespace ddms\abstractions\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

abstract class AbstractUserInterface implements DDMSUserInterface
{

    public function notify(string $message, string $noticeType = self::NOTICE): void
    {
        switch($noticeType) {
            case self::ERROR:
                echo self::ERROR . ': ' . $message;
                break;
            case self::WARNING:
                echo self::WARNING . ': ' . $message;
                break;
            case self::SUCCESS:
                echo self::SUCCESS . ': ' . $message;
                break;
            default:
                echo self::NOTICE . ': ' . $message;
                break;
        }
    }
}
