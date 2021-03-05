<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;
use ddms\classes\command\Help as DDMSHelp;
use ddms\classes\factory\CommandFactory;

$ui = new DDMSCommandLineUI();
$commandFactory = new CommandFactory();
$banner = "\e[0m\e[94m    _    _\e[0m
\e[0m\e[93m __| |__| |_ __  ___\e[0m
\e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
\e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
\e[0m\e[103m       v0.0.1      \e[0m";

$ui->showMessage($banner  . PHP_EOL . "\e[0m\e[101m\e[94m    " . date('h:i:s A') . "    \e[0m");

$ddmsHelp = $commandFactory->getCommandInstance((isset($argv[1]) ? str_replace('--', '', $argv[1]) : 'help'), $ui);;
$ddmsHelp->run($ui, $ddmsHelp->prepareArguments($argv));

