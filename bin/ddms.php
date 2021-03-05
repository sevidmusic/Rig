<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\classes\factory\CommandFactory;

$ui = new DDMSCommandLineUI();
$commandFactory = new CommandFactory();
$help = new Help();
$ddms = new DDMS();

$banner = "
  \e[0m\e[94m    _    _\e[0m
  \e[0m\e[93m __| |__| |_ __  ___\e[0m
  \e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
  \e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
  \e[0m\e[105m\e[30m  v0.0.1  \e[0m\e[0m\e[101m\e[30m  " . date('h:i:s A') . "  \e[0m

";

$ui->showMessage($banner);
$ddms->runCommand($ui, $help, $argv);
