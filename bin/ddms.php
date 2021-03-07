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
    $port = escapeshellarg($ddms->prepareArguments($argv)['flags']['port'][0] ?? strval(rand(8000, 8999)));
    $rootDirectory = escapeshellarg($ddms->prepareArguments($argv)['flags']['root-dir'][0] ?? escapeshellarg(__DIR__));
    shell_exec(
        'php -S localhost:' . $port . ' -t ' . $rootDirectory . ' >> ' . $serverLogPath .
        ' 2>> ' . $serverLogPath . ' &'
        #^ space before 2>> is required! or redirect will break!
    );
}
