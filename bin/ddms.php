<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as UserInterface;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\classes\factory\CommandFactory;

$ui = new UserInterface();
$ddms = new DDMS(new CommandFactory());

if($ui instanceof UserInterface) {
    $ui->showBanner();
}
try {
    $ddms->run($ui, $ddms->prepareArguments($argv));
} catch(\RuntimeException $ddmsError) {
    $ui->showMessage($ddmsError->getMessage());
}

