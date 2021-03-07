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

if(in_array('start-server', array_keys($ddms->prepareArguments($argv)['flags']))) {
    $serverLogPath = escapeshellarg('/tmp/ddms-php-built-in-server.log');
    shell_exec(
        'php -S localhost:8080 >> ' . $serverLogPath .
        ' 2>> ' . $serverLogPath . ' &'
    );
}
