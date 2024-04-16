<?php

declare(strict_types=1);

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
    protected ActionStatus $actionStatus = ActionStatus::NOT_PROCESSED;

    public function __construct(private MessageLog $messageLog) { }

    public function do(): Action
    {
        $this->messageLog->addMessage('Perfomred action: ' . $this::class);
        $this->actionStatus = ActionStatus::SUCCEEDED;
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

final class Rig {

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

class RigWebUI {


    private string $uiStart = <<<EOF

    <!DOCTYPE html>

    <html>

        <head>

            <title>Rig - Command line utility for the Roady PHP Framework</title>

            <meta charset="UTF-8">

            <meta name="description" content="A command line utility designed to aide in development with the Roady PHP framework. More information can be found on GitHub at https://github.com/sevidmusic/rig, or https://github.com/sevidmusic/roady">

            <meta name="keywords" content="rig, roady, php, web-development">

            <meta name="author" content="Sevi Darling">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

        </head>

        <body>

            <main class="roady-ui-main-content">

    EOF;

    private string $uiEnd = <<<EOF

            </main>

        </body>

    </html>

    EOF;


    public function __construct(private Rig $rig) {}

    private function displayMessages(): void
    {
        $command = $this->rig->lastCommandRun();
        if(!is_null($command)) {
            foreach ($command->messageLog()->messages() as $message) {
                echo '<div class="rig-web-ui-message" style="background: black; margin-bottom: 1rem; padding: 1rem; color: white;">';
                echo PHP_EOL;
                $this->info($message);
                echo PHP_EOL;
                echo '</div>';
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
            $this->table(
                ['Command', 'Status', 'Date/Time'],
                $commandStatusDateTime,
            );
        }
    }

    private function displayHeader(): void
    {
        $welcomeMessage = date('l Y, F jS h:i:s A');
        $welcomeMessage .= <<<'HEADER'

        <h1>Rig</h1>

        <p>For help use: rig --help</p>

        <p>For help with a specific command use: rig --help command-name</p>

        HEADER;
        $this->intro($welcomeMessage);

    }

    public function render(): void
    {
        echo $this->uiStart;
        $this->displayHeader();
        $this->displayMessages();
        $this->displayActionEventLog();
        echo $this->uiEnd;
    }

    private function info(string $info): void
    {
        echo <<<INFO

        <div class="rig-web-ui-info">

        <p>

        INFO;

        echo str_replace(PHP_EOL, '<br>', $info);

        echo <<<INFO

        </p>

        </div>

        INFO;
    }

    private function intro(string $intro): void
    {
        echo '<div class="rig-web-ui-intro">';
        echo '    <p>' . $intro . '</p>';
        echo '</div>';
    }

    /**
     * @param array<int, string> $columnNames
     * @param array<int, array<int, string>> $columnData
     */
    private function table(array $columnNames, array $columnData): void
    {
        echo '<table>';
        echo '<tr>';
        foreach($columnNames as $columnName) {
            echo '<th>' . $columnName . '</th>';
        }
        echo '</tr>';
        echo '<tr>';
        foreach($columnData as $datum) {
            foreach($datum as $data) {
                echo '<td>' . $data . '</td>';
            }
        }
        echo '</tr>';
        echo '</table>';
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

class GenerateHelpMessageAction extends Action
{
    public function do(): GenerateHelpMessageAction
    {
        $helpMessage = <<<'helpMESSAGE'

        The following commands are provided by rig:

        rig --delete-route
        rig --help
        rig --list-routes
        rig --new-module
        rig --new-route
        rig --start-servers
        rig --update-route
        rig --version
        rig --view-action-log
        rig --view-readme

        helpMESSAGE;
        $this->messageLog()->addMessage($helpMessage);
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class ReadREADMEAction extends Action
{
    public function do(): ReadREADMEAction
    {
        $this->messageLog()->addMessage(strval(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'README.md')));
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class DetermineVersionAction extends Action
{
    public function do(): DetermineVersionAction
    {
        $this->messageLog()->addMessage('rig version 2.0.0-alpha-9');
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

# COMMANDS #

class HelpCommand extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new GenerateHelpMessageAction($this->messageLog())];
    }
}

class ViewREADMECommand extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new ReadREADMEAction($this->messageLog())];
    }
}

class VersionCommand extends Command
{
    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new DetermineVersionAction($this->messageLog())];
    }
}

class ArgumentParser
{
    /** @return array<string, string> */
    public function getArguments(): array
    {
        #var_dump(getopt('h::v::', ['help::', 'version::']));
        return [];
    }

    private function determineNameOfCommandToRun(): string
    {
        $commandNameSpecifiedInArgv = (
            isset($GLOBALS['argv'])
                && is_array($GLOBALS['argv'])
                && isset($GLOBALS['argv'][1])
                && is_string($GLOBALS['argv'][1])
            ? $GLOBALS['argv'][1]
            : null
        );
        $commandNameSpecifiedInPost = (
            isset($_POST['rig'])
                && is_array($_POST['rig'])
                && isset($_POST['rig'][0])
                && is_string($_POST['rig'][0])
            ? $_POST['rig'][0]
            : null
        );
        $commandNameSpecifiedInGet = (
            isset($_GET['rig'])
                && is_array($_GET['rig'])
                && isset($_GET['rig'][0])
                && is_string($_GET['rig'][0])
            ? $_GET['rig'][0]
            : null
        );
        $specifiedCommandName = (
            is_string($commandNameSpecifiedInArgv)
            ? $commandNameSpecifiedInArgv
            : (
                is_string($commandNameSpecifiedInPost)
                ? $commandNameSpecifiedInPost
                : (
                    is_string($commandNameSpecifiedInGet)
                    ? $commandNameSpecifiedInGet
                    : '--command-not-specified'
                )
            )
        );
        return (
            ucfirst(
                str_replace(
                    '-',
                    '',
                    $specifiedCommandName,
                )
            ) . 'Command'
        );
    }

    public function commandToRun(): Command
    {
        $commandName = $this->determineNameOfCommandToRun();
        /*
         * @todo:
         * You will need to prefix the $commandName with the
         * appropriate namespace, for example:
         *
         * new ClassString(
         *     '\\Darling\\Rig\\classes\\commands' . $commandName
         * );
         *
         */
        $commandToRunClassString = new ClassString($commandName);
        $extendsClasses = class_parents(
            $commandToRunClassString->__toString()
        );
        if(
            is_array($extendsClasses)
            &&
            in_array(
                Command::class,
                $extendsClasses,
                true
            )
        )
        {
            /** @var Command $commandName */
            return new $commandName(
                new ActionEventLog(),
                new MessageLog()
            );
        }
        return new HelpCommand(
                new ActionEventLog(),
                new MessageLog()
        );
    }
}

$rig = new Rig();

$argumentParser = new ArgumentParser();

if(php_sapi_name() === 'cli') {

    $rigCLUI = new RigCLUI(
        $rig->run($argumentParser->commandToRun())
    );

    $rigCLUI->render();
    $rigCLUIAlreadyRendered = true;
}


if(!isset($rigCLUIAlreadyRendered)) {

    $rigWebUI = new RigWebUI(
        $rig->run($argumentParser->commandToRun())
    );

    $rigWebUI->render();

}
