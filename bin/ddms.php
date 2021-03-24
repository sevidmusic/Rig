<?php

$darlingDataManagementSystemAutoloader = strval(realpath(str_replace('darling' . DIRECTORY_SEPARATOR . 'ddms' . DIRECTORY_SEPARATOR . 'bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'autoload.php'));
$standaloneAutoloader = strval(realpath(str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php'));
if(file_exists($darlingDataManagementSystemAutoloader)) {
    require $darlingDataManagementSystemAutoloader;
} else {
    require $standaloneAutoloader;
}

use ddms\classes\ui\CommandLineUI;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\interfaces\command\Command;
use ddms\interfaces\ui\UserInterface;
use ddms\classes\factory\CommandFactory;

$ui = new CommandLineUI();
$ddms = new DDMS(new CommandFactory());

if($ui instanceof CommandLineUI) {
    $ui->showBanner();
}
try {
    $ddms->run($ui, $ddms->prepareArguments($argv));
} catch(\RuntimeException $ddmsError) {
    $ui->showMessage($ddmsError->getMessage());
}

