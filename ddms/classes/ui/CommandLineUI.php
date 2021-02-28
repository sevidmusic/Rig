<?php

namespace ddms\classes\ui;

use ddms\interfaces\ui\UserInterface as DDMSUserInterface;

class CommandLineUI implements DDMSUserInterface
{
    public function notify(string $message, string $noticeType = self::NOTICE): void
    {
        $output = sprintf(
            "%s%s%s%s%s%s%s%s%s%s%s",
            PHP_EOL,
            "\e[0m\e[105m\e[30m    ",
            "\e[0m\e[92m 2021-28-Feb@10:02:31\e[0m ",
            "\e[0m\e[102m\e[30m" . $noticeType . "\e[0m\e[105m\e[30m    \e[0m",
            PHP_EOL,
            PHP_EOL,
            "\e[0m\e[107m\e[30m",
            $message,
            "\e[0m",
            PHP_EOL,
            PHP_EOL,
        );
        echo $output;
    }

}
