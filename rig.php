<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\info;

$longopts = [
    'module-name::',
    'named-positions:',
    'path-to-roady-project:',
    'relative-path-to-file::',
    'responds-to::',
];
$options = getopt(short_options: '', long_options:  $longopts);

$welcomeMessage = <<<'LOGO'
 ____  ___ ____
|  _ \|_ _/ ___|
| |_) || | |  _
|  _ < | | |_| |
|_| \_\___\____|

Welcome to Rig, the command line utilitiy designed to aide in
development with the Roady PHP framework.

For help use: rig --help
For help with a specific command use: rig --help command-name

LOGO;

$response = spin(
    fn () => sleep(3),
    $welcomeMessage,
);


$moduleName = text('What is module would you like information on?');

table(
    ['Module Name', 'Version'],
    [
        [$moduleName, strval(rand(0, 100)) . '.' . strval(rand(0, 100))],
    ]
);

