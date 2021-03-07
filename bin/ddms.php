<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as UserInterface;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\classes\factory\CommandFactory;

$ui = new UserInterface();
$commandFactory = new CommandFactory();
$help = new Help();
$ddms = new DDMS($commandFactory);
$arguments = $ddms->prepareArguments($argv);

$ui->showBanner();

try {
    $ddms->run($ui, $ddms->prepareArguments($argv));
} catch(\RuntimeException $ddmsError) {
    $ui->showMessage(getDevInvalidCommandMsg($ddmsError));
}

if(in_array('DEBUGOPTIONS' ,$arguments['options'])) {
    $ui->showOptions($arguments);
}

if(in_array('DEBUGFLAGS' ,$arguments['options'])) {
    $ui->showFlags($arguments);
}

function getDevInvalidCommandMsg(\RuntimeException $ddmsError): string
{
    return PHP_EOL .
            "\e[0m  \e[103m\e[30m" .
            str_replace(
                [
                    'Error',
                    'ddms --help'
                ],
                [
                    "\e[0m\e[102m\e[30mError\e[0m\e[103m\e[30m",
                    "\e[0m\e[104m\e[30mddms --help\e[0m\e[103m\e[30m"
                ],
                $ddmsError->getMessage()
            ) . "\e[0m" . PHP_EOL;
}
