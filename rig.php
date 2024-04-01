<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use function Laravel\Prompts\table;

$welcomeMessage = <<<'HEADER'

       _
  ____(_)__ _
 / __/ / _ `/
/_/ /_/\_, /
      /___/

Welcome to rig, the command line utilitiy designed to aide in
development with the Roady PHP framework.

Note: rig is still being developed and is not yet ready for use
in production.

For help use: rig --help
For help with a specific command use: rig --help command-name

HEADER;

table(
    ['Rig Status'],
    [
        [date('l Y, F jS h:i:s A')],
        [$welcomeMessage],
    ]
);

