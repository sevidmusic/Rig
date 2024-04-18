<?php

declare(strict_types=1);

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR  . 'Parsedown.php';


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

            <style>

                body {
                    padding: 1svh 27svw;
                    background: black;
                    color: #87cefa;
                }

                .rig-web-ui-message {
                    margin-bottom: 1rem;
                    padding: 1rem;

                    h1, h2, h3, h4, h5, h6 { color: white; }

                    a { color: #FAB387; }

                    ul {
                        li {
                            list-style-type: none;
                            color: #95FA87;
                        }
                        br { display: none; }
                    }

                    code {
                        color: #95FA87;
                        background: #0c0c0c;
                        padding: 0.3svh 0.7svw;
                    }

                }

            </style>

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
                echo '<div class="rig-web-ui-message">';
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
        $readme = strval(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'README.md'));
        $parsedown = new Parsedown();
        match(php_sapi_name() === 'cli') {
            true => $this->messageLog()->addMessage($readme),
            default => $this->messageLog()->addMessage($parsedown->text($readme)),
        };
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
    public function getWebArguments(): array
    {
        return [
            // Commands
            'delete-route' => (isset($_POST['delete-route']) ? $_POST['delete-route'] : (isset($_GET['delete-route']) ? $_GET['delete-route'] : '')),
            'help' => (isset($_POST['help']) ? $_POST['help'] : (isset($_POST['help']) ? $_POST['help'] : '')),
            'list-routes' => (isset($_POST['list-routes']) ? $_POST['list-routes'] : (isset($_GET['list-routes']) ? $_GET['list-routes'] : '')),
            'new-module' => (isset($_POST['new-module']) ? $_POST['new-module'] : (isset($_GET['new-module']) ? $_GET['new-module'] : '')),
            'new-route' => (isset($_POST['new-route']) ? $_POST['new-route'] : (isset($_GET['new-route']) ? $_GET['new-route'] : '')),
            'start-servers' => (isset($_POST['start-servers']) ? $_POST['start-servers'] : (isset($_GET['start-servers']) ? $_GET['start-servers'] : '')),
            'update-route' => (isset($_POST['update-route']) ? $_POST['update-route'] : (isset($_GET['update-route']) ? $_GET['update-route'] : '')),
            'version' => (isset($_POST['version']) ? $_POST['version'] : (isset($_POST['version']) ? $_POST['version'] : '')),
            'view-action-log' => (isset($_POST['view-action-log']) ? $_POST['view-action-log'] : (isset($_POST['view-_GET-log']) ? $_POST['_GET-action-log'] : '')),
            'view-readme' => (isset($_POST['view-readme']) ? $_POST['view-readme'] : (isset($_GET['view-readme']) ? $_GET['view-readme'] : '')),
            // Command Options
            'authority' => (isset($_POST['authority']) ? $_POST['authority'] : (isset($_POST['authority']) ? $_POST['authority'] : '')),
            'defined-for-authorities' => (isset($_POST['defined-for-authorities']) ? $_POST['defined-for-authorities'] : (isset($_POST['defined-_GET-authorities']) ? $_POST['_GET-for-authorities'] : '')),
            'defined-for-files' => (isset($_POST['defined-for-files']) ? $_POST['defined-for-files'] : (isset($_POST['defined-_GET-files']) ? $_POST['_GET-for-files'] : '')),
            'defined-for-modules' => (isset($_POST['defined-for-modules']) ? $_POST['defined-for-modules'] : (isset($_POST['defined-_GET-modules']) ? $_POST['_GET-for-modules'] : '')),
            'defined-for-named-positions' => (isset($_POST['defined-for-named-positions']) ? $_POST['defined-for-named-positions'] : (isset($_POST['defined-for-named-_GET']) ? $_POST['defined-_GET-named-positions'] : '')),
            'defined-for-positions' => (isset($_POST['defined-for-positions']) ? $_POST['defined-for-positions'] : (isset($_POST['defined-_GET-positions']) ? $_POST['_GET-for-positions'] : '')),
            'defined-for-requests' => (isset($_POST['defined-for-requests']) ? $_POST['defined-for-requests'] : (isset($_POST['defined-_GET-requests']) ? $_POST['_GET-for-requests'] : '')),
            'for-authority' => (isset($_POST['for-authority']) ? $_POST['for-authority'] : (isset($_GET['for-authority']) ? $_GET['for-authority'] : '')),
            'module-name' => (isset($_POST['module-name']) ? $_POST['module-name'] : (isset($_GET['module-name']) ? $_GET['module-name'] : '')),
            'named-positions' => (isset($_POST['named-positions']) ? $_POST['named-positions'] : (isset($_GET['named-positions']) ? $_GET['named-positions'] : '')),
            'no-boilerplate' => (isset($_POST['no-boilerplate']) ? $_POST['no-boilerplate'] : (isset($_GET['no-boilerplate']) ? $_GET['no-boilerplate'] : '')),
            'open-in-browser' => (isset($_POST['open-in-browser']) ? $_POST['open-in-browser'] : (isset($_POST['open-_GET-browser']) ? $_POST['_GET-in-browser'] : '')),
            'path-to-roady-project' => (isset($_POST['path-to-roady-project']) ? $_POST['path-to-roady-project'] : (isset($_POST['path-to-roady-_GET']) ? $_POST['path-_GET-roady-project'] : '')),
            'ports' => (isset($_POST['ports']) ? $_POST['ports'] : (isset($_POST['ports']) ? $_POST['ports'] : '')),
            'relative-path' => (isset($_POST['relative-path']) ? $_POST['relative-path'] : (isset($_GET['relative-path']) ? $_GET['relative-path'] : '')),
            'responds-to' => (isset($_POST['responds-to']) ? $_POST['responds-to'] : (isset($_GET['responds-to']) ? $_GET['responds-to'] : '')),
            'route-hash' => (isset($_POST['route-hash']) ? $_POST['route-hash'] : (isset($_GET['route-hash']) ? $_GET['route-hash'] : '')),
        ];
    }

    /** @return array<string, string> */
    public function getCliArguments(): array
    {
        $shortOpts = 'h:v::';
        $longOpts = [
                    // Commands
                    'delete-route::',
                    'help:',
                    'list-routes::',
                    'new-module::',
                    'new-route::',
                    'start-servers::',
                    'update-route::',
                    'version::',
                    'view-action-log::',
                    'view-readme::',
                    // Command Options
                    'authority:',
                    'defined-for-authorities:',
                    'defined-for-files:',
                    'defined-for-modules:',
                    'defined-for-named-positions:',
                    'defined-for-positions:',
                    'defined-for-requests:',
                    'for-authority:',
                    'module-name:',
                    'named-positions:',
                    'no-boilerplate::',
                    'open-in-browser::',
                    'path-to-roady-project:',
                    'ports:',
                    'relative-path:',
                    'responds-to:',
                    'route-hash:',
        ];
        $opts = getopt($shortOpts, $longOpts);
        $cliArguments = match(is_array($opts)) {
            true =>
                [
                    // Commands
                    'delete-route' => (isset($opts['delete-route']) ? 'delete-route' : ''),
                    'help' => ($opts['help'] ?? ''),
                    'list-routes' => (isset($opts['list-routes']) ? 'list-routes' : ''),
                    'new-module' => (isset($opts['new-module']) ? 'new-module' : ''),
                    'new-route' => (isset($opts['new-route']) ? 'new-route' : ''),
                    'start-servers' => (isset($opts['start-servers']) ? 'start-servers' : ''),
                    'update-route' => (isset($opts['update-route']) ? 'update-route' : ''),
                    'version' => (isset($opts['version']) ? 'version' : ''),
                    'view-action-log' => (isset($opts['view-action-log']) ? 'view-action-log' : ''),
                    'view-readme' => (isset($opts['view-readme']) ? 'view-readme' : ''),
                    // Command Options
                    'authority' => ($opts['authority'] ?? ''),
                    'defined-for-authorities' => ($opts['defined-for-authorities'] ?? ''),
                    'defined-for-files' => ($opts['defined-for-files'] ?? ''),
                    'defined-for-modules' => ($opts['defined-for-modules'] ?? ''),
                    'defined-for-named-positions' => ($opts['defined-for-named-positions'] ?? ''),
                    'defined-for-positions' => ($opts['defined-for-positions'] ?? ''),
                    'defined-for-requests' => ($opts['defined-for-requests'] ?? ''),
                    'for-authority' => ($opts['for-authority'] ?? ''),
                    'module-name' => ($opts['module-name'] ?? ''),
                    'named-positions' => ($opts['named-positions'] ?? ''),
                    'no-boilerplate' => (isset($opts['no-boilerplate']) ? 'no-boilerplate' : ''),
                    'open-in-browser' => (isset($opts['open-in-browser']) ? 'open-in-browser' : ''),
                    'path-to-roady-project' => ($opts['path-to-roady-project'] ?? ''),
                    'ports' => ($opts['ports'] ?? ''),
                    'relative-path' => ($opts['relative-path'] ?? ''),
                    'responds-to' => ($opts['responds-to'] ?? ''),
                    'route-hash' => ($opts['route-hash'] ?? ''),
                ],
            default => []

        };
        return $cliArguments;
    }

    /** @return array<string, array<int, mixed>|string|false> */
    private function getArguments(): array
    {
        return match(php_sapi_name()) {
            'cli' => $this->getCliArguments(),
            default => $this->getWebArguments(),

        };
    }

    private function determineNameOfCommandToRun(): string
    {
        $arguments = $this->getArguments();
        dump(
            [
                'Arguments' => $arguments,
            ]
        );
        $commandNameSpecifiedInArgv = (
            isset($GLOBALS['argv'])
                && is_array($GLOBALS['argv'])
                && isset($GLOBALS['argv'][1])
                && is_string($GLOBALS['argv'][1])
            ? $GLOBALS['argv'][1]
            : null
        );
        $commandNameSpecifiedInPost = (
            !empty($_POST['rig'])
                && is_array($_POST['rig'])
                && isset($_POST['rig'][0])
                && is_string($_POST['rig'][0])
            ? $_POST['rig'][0]
            : null
        );
        $commandNameSpecifiedInGet = (
            !empty($_GET['rig'])
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
        $commandNamespace = ''; // @todo set appropriate namespace
        $commandToRunClassString = new ClassString($commandNamespace . $commandName);
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

    #$rigCLUI->render();
    $rigCLUIAlreadyRendered = true;
}


if(!isset($rigCLUIAlreadyRendered)) {

    $rigWebUI = new RigWebUI(
        $rig->run($argumentParser->commandToRun())
    );

    $rigWebUI->render();

}


