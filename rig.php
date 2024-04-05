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
        $this->messageLog->addMessage('Perfomred action: ' . $this::class);
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

class ActionEvent
{
    public function __construct(private Action $action, private DateTimeImmutable $dateTime) {}

    public function action(): Action
    {
        return $this->action;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

}

class ActionEventLog
{
    /** @var array<int, ActionEvent> $actionEvents */
    private $actionEvents = [];

    /** @return array<int, ActionEvent> $actionEvents */
    public function actionEvents(): array {
        return $this->actionEvents;
    }

    public function addActionEvent(ActionEvent $event) : void
    {
        $this->actionEvents[] = $event;
    }

}

class Command
{
    /** @var array<int, Action> $actions */
    private array $actions = [];

    public function __construct(
        private ActionEventLog $actionEventLog,
        private MessageLog $messageLog
    ) {
        $this->actions[] = new Action($this->messageLog);
        $this->actions[] = new Action($this->messageLog);
        $this->actions[] = new Action($this->messageLog);
        $this->actions[] = new Action($this->messageLog);
        $this->actions[] = new Action($this->messageLog);
        $this->actions[] = new Action($this->messageLog);
    }

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return $this->actions;
    }

    public function execute(): void {
        foreach($this->actions() as $action) {
        $this->messageLog()->addMessage(
                'Executing action: ' . $action::class
            );
            $this->actionEventLog->addActionEvent(
                new ActionEvent(
                    $action->do(),
                    new DateTimeImmutable('now')
                )
            );
        }
    }

    public function messageLog(): MessageLog
    {
        return $this->messageLog;
    }

    public function actionEventLog(): ActionEventLog
    {
        return $this->actionEventLog;
    }
}

class Rig {

    public function run(Command $command): void {
        $command->execute();
        $this->displayMessages($command);
        $this->displayActionEventLog($command);
    }

    private function displayMessages(Command $command): void
    {
        foreach ($command->messageLog()->messages() as $message) {
            echo "\033[38;5;0m\033[48;5;0m";
            info($message);
            echo "\033[0m" . PHP_EOL;
        }
    }

    private function displayActionEventLog(Command $command): void
    {
        $commandStatusDateTime = [];
        foreach($command->actionEventLog()->actionEvents() as $actionEvent) {
            $commandStatusDateTime[] = [
                $actionEvent->action()::class,
                $actionEvent->action()->status()->name,
                $actionEvent->dateTime()->format('Y-m-d H:i:s A')
            ];
        }
        echo "\033[38;5;33m\033[48;5;0m";
        table(
            ['Command', 'Status', 'Date/Time'],
            $commandStatusDateTime,
        );
        echo "\033[0m" . PHP_EOL;
    }

}

$welcomeMessage = date('l Y, F jS h:i:s A');

$welcomeMessage .= <<<'HEADER'

       _
  ____(_)__ _
 / __/ / _ `/
/_/ /_/\_, /
      /___/

For help use: rig --help
For help with a specific command use: rig --help command-name

HEADER;

intro($welcomeMessage);


$rig = new Rig();

$messageLog = new MessageLog();

$messageLog->addMessage('Note: Rig is still being developed.');
$messageLog->addMessage('Some commands may not work yet.');

$rig->run(new Command(new ActionEventLog(), $messageLog));

