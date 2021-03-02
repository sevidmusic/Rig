<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;
use ddms\classes\command\DDMSHelp;

$ui = new DDMSCommandLineUI();

$ui->notify(
    "ddms is still under development.\e[0m" . PHP_EOL . PHP_EOL .
    "\e[0m\e[107m\e[30mFor more information please visit: \e[0m\e[105m\e[30mhttps://github.com/sevidmusic/ddms\e[0m",
    DDMSCommandLineUI::WARNING
);

$ddmsHelp = new DDMSHelp();
$ddmsHelp->run($ui);
