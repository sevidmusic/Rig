<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;
use ddms\classes\command\Help as DDMSHelp;

$ui = new DDMSCommandLineUI();
$banner = "\e[0m\e[94m    _    _\e[0m
\e[0m\e[93m __| |__| |_ __  ___\e[0m
\e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
\e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
\e[0m\e[103m       v0.0.1      \e[0m";

$ui->notify($banner  . PHP_EOL . "\e[0m\e[101m\e[94m    " . date('h:i:s A') . "    \e[0m", 'banner');
$ddmsHelp = new DDMSHelp();
$ddmsHelp->run($ui, $ddmsHelp->prepareArguments($argv));


# $ddmsCommandFactory = new DDMSCommandFactory();
# $ddmsHelp = $ddmsCommandFactory->buidHelpInstance();
# $ddmsHelp->run($ui, $ddmsHelp->prepareArguments($argv));

