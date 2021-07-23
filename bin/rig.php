<?php

$darlingDataManagementSystemAutoloader = strval(realpath(str_replace('darling' . DIRECTORY_SEPARATOR . 'rig' . DIRECTORY_SEPARATOR . 'bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'autoload.php'));
$standaloneAutoloader = strval(realpath(str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php'));
if(file_exists($darlingDataManagementSystemAutoloader)) {
    require $darlingDataManagementSystemAutoloader;
} else {
    require $standaloneAutoloader;
}

use rig\classes\ui\CommandLineUI;
use rig\classes\command\Help;
use rig\classes\command\Rig;
use rig\interfaces\command\Command;
use rig\interfaces\ui\UserInterface;
use rig\classes\factory\CommandFactory;

$ui = new CommandLineUI();
$rig = new Rig(new CommandFactory());

if($ui instanceof CommandLineUI) {
    $ui->showBanner();
}
try {
    $rig->run($ui, $rig->prepareArguments($argv));
} catch(\RuntimeException $rigError) {
    $ui->showMessage($rigError->getMessage());
}

