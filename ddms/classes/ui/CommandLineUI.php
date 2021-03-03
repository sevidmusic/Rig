<?php

namespace ddms\classes\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

class CommandLineUI implements DDMSUserInterface
{

    public const BANNER = 'banner';

    public function notify(string $message, string $noticeType = self::NOTICE): void
    {
        if($noticeType === self::BANNER){
             echo sprintf(
                "%s%s%s%s",
                PHP_EOL,
                $message,
                PHP_EOL,
                PHP_EOL,
            );
            return;
        }
        echo sprintf(
            "%s%s%s%s%s%s%s%s%s%s%s",
            PHP_EOL,
            "\e[0m\e[105m\e[30m    ",
            "\e[0m\e[92m " . date('Y-m-d @ H:i:s') . "  \e[0m ",
            "\e[0m\e[102m\e[30m" . (empty($noticeType) ? self::NOTICE : $noticeType) . "\e[0m\e[105m\e[30m    \e[0m",
            PHP_EOL,
            PHP_EOL,
            "\e[0m\e[107m\e[30m",
            $message,
            "\e[0m",
            PHP_EOL,
            PHP_EOL,
        );
    }

}
