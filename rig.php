<?php

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;


use function Laravel\Prompts\intro;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use \Darling\PHPTextTypes\classes\strings\ClassString;

enum ActionStatus
{

    case NOT_PROCESSED;
    case SUCCEEDED;
    case FAILED;

}

# BASE CLASSES #
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

    public function actionStatus(): ActionStatus
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

    public function __construct(
        private ActionEventLog $actionEventLog,
        private MessageLog $messageLog
    ) { }

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new Action($this->messageLog)];
    }

    public function execute(): Command {
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
        return $this;
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

    private Command|null $lastCommandRun = null;

    public function run(Command $command): Rig {
        $this->setLastCommandRun($command->execute());
        return $this;
    }

    private function setLastCommandRun(Command $command): void
    {
        $this->lastCommandRun = $command;
    }

    public function lastCommandRun(): Command|null
    {
        return $this->lastCommandRun;
    }

}

class RigCLUI {

    public function __construct(private Rig $rig) {}

    private function displayMessages(): void
    {
        $command = $this->rig->lastCommandRun();
        if(!is_null($command)) {
            foreach ($command->messageLog()->messages() as $message) {
                info($message);
            }
        }
    }

    private function displayActionEventLog(): void
    {
        $command = $this->rig->lastCommandRun();
        $commandStatusDateTime = [];
        if(!is_null($command)) {
            foreach($command->actionEventLog()->actionEvents() as $actionEvent) {
                $commandStatusDateTime[] = [
                    $actionEvent->action()::class,
                    $actionEvent->action()->actionStatus()->name,
                    $actionEvent->dateTime()->format('Y-m-d H:i:s A')
                ];
            }
            table(
                ['Command', 'Status', 'Date/Time'],
                $commandStatusDateTime,
            );
        }
    }

    private function displayHeader(): void
    {
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

    }

    public function render(): void
    {
        $this->displayHeader();
        $this->displayMessages();
        $this->displayActionEventLog();
    }

}

# ACTIONS #
class ReadReadme extends Action
{
    public function do(): ReadReadme
    {
        $this->messageLog()->addMessage(strval(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'README.md')));
        return $this;
    }
}

class DetermineVersion extends Action
{
    public function do(): DetermineVersion
    {
        return $this;
    }
}

# COMMANDS #
class HelpCommand extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new ReadReadme($this->messageLog())];
    }
}

class VersionCommand extends Command
{
    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new DetermineVersion($this->messageLog())];
    }
}

class ArgumentParser
{
    /** @return array<string, string> */
    public function getArguments(): array
    {
        return [];
    }

    public function commandToRun(): Command
    {
        #var_dump(getopt('h::v::', ['help::', 'version::']));
        $argv = (isset($GLOBALS['argv']) && is_array($GLOBALS['argv']) ? $GLOBALS['argv'] : []);
        $commandName = (ucfirst(str_replace('--', '', ($argv[1] ?? '--help-should-be-default'))) . 'Command');
        $commandClassString = new ClassString($commandName);
        $extendsClasses = class_parents($commandClassString->__toString());
        if(in_array(Command::class, (is_array($extendsClasses) ? $extendsClasses : []), true))
        {
            /** @var Command $commandName */
            return new $commandName(
                new ActionEventLog(),
                new MessageLog()
            );
        }
        return new Command(
                new ActionEventLog(),
                new MessageLog()
        );
    }
}

$rig = new Rig();

$argumentParser = new ArgumentParser();

$rigCLUI = new RigCLUI(
    $rig->run($argumentParser->commandToRun())
);

$rigCLUI->render();
