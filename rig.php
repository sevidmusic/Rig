<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use function Laravel\Prompts\intro;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

$welcomeMessage = date('l Y, F jS h:i:s A');

$welcomeMessage .= <<<'HEADER'

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

intro($welcomeMessage);

$routeJson = <<<'JSON'
[
    {
        "module-name": "hello-world",
        "responds-to": [
            "homepage"
        ],
        "named-positions": [
            {
                "position-name": "roady-ui-footer",
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

intro("# Routes");

foreach ($decodedRouteJson as $route) {
    echo "\033[38;5;0m\033[48;5;0m";
    table(
        ['route-hash:', substr(hash('sha256', strval(json_encode($route))), 0, 17)],
        [
            ['defined-by-module', $route['module-name']],
            ['responds-to', implode(', ', $route['responds-to'])],
            ['named-positions', strval(json_encode($route['named-positions']))],
            ['relative-path', $route['relative-path']],
        ]
    );
    echo "\033[38;5;0m" . PHP_EOL;
}

