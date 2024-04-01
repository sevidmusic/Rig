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

$routeJson = <<<'JSON'
[
    {
        "module-name": "hello-world",
        "responds-to": [
            "homepage"
        ],
        "named-positions": [
            {
                "position-name": "roady-ui-main-content",
                "position": 10
            }
        ],
        "relative-path": "output\/hello-world.html"
    },
    {
        "module-name": "hello-world",
        "responds-to": [
            "hello-universe",
            "hello-world",
            "homepage"
        ],
        "named-positions": [
            {
                "position-name": "roady-ui-header",
                "position": 3
            }
        ],
        "relative-path": "output\/header.html"
    }
]
JSON;

$decodedRouteJson = json_decode($routeJson, true);


foreach ($decodedRouteJson as $route) {
    echo "\033[38;5;0m\033[48;5;0m";
    table(
        ['Route Defined By: ' . $route['module-name'], 'route-hash: ' . substr(hash('sha256', strval(json_encode($route))), 0, 17)],
        [
            ['responds-to', implode(', ', $route['responds-to'])],
            ['named-positions', strval(json_encode($route['named-positions']))],
            ['relative-path', $route['relative-path']],
        ]
    );
    echo "\033[38;5;0m";
}

