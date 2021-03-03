<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;
use ddms\classes\command\DDMSHelp;

$ui = new DDMSCommandLineUI();
$banner = "\e[0m\e[94m    _    _\e[0m
\e[0m\e[93m __| |__| |_ __  ___\e[0m
\e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
\e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
\e[0m\e[103m                    \e[0m";

$ui->notify($banner, ':)');
$ddmsHelp = new DDMSHelp();
$ddmsHelp->run($ui, $ddmsHelp->prepareArguments($argv));
