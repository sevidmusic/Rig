<?php

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;


use function Laravel\Prompts\intro;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;


enum ActionStatus
{

    case NOT_PROCESSED;
    case SUCCEEDED;
    case FAILED;

}

class MessageLog
{

    /** @var array<int, string> $messages */
    private array $messages = [];

    /**
     * Return the array of messages.
     *
     * @return array<int, string>
     *
     */
    public function messages(): array
    {
        return $this->messages;
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }
}

class Action
{
    private ActionStatus $actionStatus = ActionStatus::NOT_PROCESSED;

    public function __construct(private MessageLog $messageLog) { }

    public function do(): Action
    {
        $this->messageLog->addMessage('Perfomred action');
        $this->actionStatus = (rand(0, 1) ? ActionStatus::SUCCEEDED : ActionStatus::FAILED);
        return $this;
    }

    public function status(): ActionStatus
    {
        return $this->actionStatus;
    }

    public function messageLog(): MessageLog
    {
        return $this->messageLog;
    }

}

class Event
{
    public function __construct(private Action $action, private DateTime $dateTime) {}

    public function action(): Action
    {
        return $this->action;
    }

    public function dateTime(): DateTime
    {
        return $this->dateTime;
    }

}

class EventLog
{
    /** @var array<int, Event> $events */
    private $events = [];

    /** @return array<int, Event> $events */
    public function events(): array {
        return $this->events;
    }

    public function addEvent(Event $event) : void
    {
        $this->events[] = $event;
    }

}

class Command
{
    /** @var array<int, Action> $actions */
    private array $actions = [];

    public function __construct(private MessageLog $messageLog)
    {
        $this->actions[] = new Action($this->messageLog);
    }

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return $this->actions;
    }

    public function execute(): void {
        foreach($this->actions() as $action) {
            $action->do();
        }
    }

    public function messageLog(): MessageLog
    {
        return $this->messageLog;
    }

}

class Rig {

    public function run(Command $command): void {
        $command->execute();
        foreach ($command->messageLog()->messages() as $message) {
            info($message);
        }
    }
}


$rig = new Rig();

$rig->run(new Command(new MessageLog()));

/*
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
*/
