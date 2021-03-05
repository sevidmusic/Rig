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
$arguments = $ddms->prepareArguments($argv);
foreach($arguments['options'] as $key => $option) {
    $ui->showMessage(PHP_EOL . "\e[0m  \e[105m\e[30mOption \e[0m\e[101m\e[30m$key\e[0m\e[105m\e[30m: \e[0m\e[104m\e[30m$option\e[0m" . PHP_EOL);
}
foreach($arguments['flags'] as $key => $flags) {
    $ui->showMessage(PHP_EOL . "\e[0m  \e[105m\e[30mFlag \e[0m\e[101m\e[30m$key\e[0m" . PHP_EOL);
    foreach($flags as $key => $flagArgument) {
        $ui->showMessage(PHP_EOL . "\e[0m  \e[105m\e[30mFlag Argument \e[0m\e[101m\e[30m$key\e[0m\e[105m\e[30m: \e[0m\e[104m\e[30m$flagArgument\e[0m" . PHP_EOL);
    }
}

$ddms->runCommand($ui, $help, $argv);
try {
    $ddms->run($ui, $ddms->prepareArguments($argv));
} catch(\RuntimeException $ddmsError) {
    $ui->showMessage(PHP_EOL . "\e[0m  \e[103m\e[30m" . str_replace(['Error', 'ddms --help'], ["\e[0m\e[102m\e[30mError\e[0m\e[103m\e[30m", "\e[0m\e[104m\e[30mddms --help\e[0m\e[103m\e[30m"], $ddmsError->getMessage()) . "\e[0m" . PHP_EOL);
}
