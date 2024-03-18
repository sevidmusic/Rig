<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use function Laravel\Prompts\text;
use function Laravel\Prompts\table;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\info;

$welcomeMessage = <<<'HEADER'

       _
  ____(_)__ _
 / __/ / _ `/
/_/ /_/\_, /
      /___/

Welcome to rig, the command line utilitiy designed to aide in
development with the Roady PHP framework.

For help use: rig --help
For help with a specific command use: rig --help command-name

HEADER;

$response = spin(
    fn () => sleep(3),
    $welcomeMessage,
);

info(
    'Note: rig is still being developed. This file is just ' .
    'a placeholder for now.'
);

$userInput = text(
    'Enter some text:'
);

table(
    ['You Entered', 'Date/Time'],
    [
        [$userInput, date('l Y, F jS h:i:s A')],
    ]
);

