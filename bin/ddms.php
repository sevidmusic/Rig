<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI as DDMSCommandLineUI;

$ui = new DDMSCommandLineUI();

$ui->notify('ddms is still under development.', DDMSCommandLineUI::WARNING);
$ui->notify('For more information please visit https://github.com/sevidmusic/ddms');
