<?php

declare(strict_types=1);

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR  . 'Parsedown.php';


use \Darling\PHPTextTypes\classes\strings\ClassString;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\table;

class CLIColorizer
{

    /**
     * Apply the specified ANSI $backgroundColorCode to the specified
     * $string as the background color:
     *
     *     `\033[48;5;{$backgroundColorCode}m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * Note: This function is designed to format strings to be output
     *       to a terminal, using this function in any other context
     *       is harmless, though probably not appropriate.
     *
     * @param string $string The string to apply color to.
     *
     * @param int $backgroundColorCode The color code to apply as the
     *                                 background color.
     *
     *                                 Color code range: 0 - 255
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applyANSIColor("Foo", rand(1, 255));
     *
     */
    public static function applyANSIColor(string $string, int $backgroundColorCode): string {
        return "\033[0m" .      // reset color
            "\033[48;5;" .      // set background color to specified color
            strval($backgroundColorCode) . "m" .
            "\033[38;5;0m " .   // set foreground color to black
            $string .
            " \033[0m";         // reset color
    }


    public static function applySUCCEEDEDColor(string $string): string
    {
        return self::applyANSIColor($string, 83);
    }

    public static function applyFAILEDColor(string $string): string
    {
        return self::applyANSIColor($string, 160);
    }

    public static function applyNOT_PROCESSEDColor(string $string): string
    {
        return self::applyANSIColor($string, 250);
    }

    public static function applyHighlightColor(string $string): string
    {
        return self::applyANSIColor($string, 67);
    }

}

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
    protected ActionStatus $actionStatus = ActionStatus::NOT_PROCESSED;

    final public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog
    ) { }

    public function do(): Action
    {
        $this->messageLog->addMessage(
            'Perfomred action: ' . CLIColorizer::applyHighlightColor($this::class)
        );
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

    public function arguments(): Arguments
    {
        return $this->arguments;
    }

}

class ActionEvent
{
    final public function __construct(
        private Action $action,
        private DateTimeImmutable $dateTime
    ) {}

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

    final public function __construct(
        private Arguments $arguments,
        private ActionEventLog $actionEventLog,
        private MessageLog $messageLog,
    ) { }

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [new Action($this->arguments(), $this->messageLog)];
    }

    public function execute(): Command {
        foreach($this->actions() as $action) {
        $this->messageLog()->addMessage(
                'Executing action: ' .
                CLIColorizer::applyHighlightColor($action::class)
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

    public function arguments(): Arguments
    {
        return $this->arguments;
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


    final public function __construct(private Rig $rig) {}

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
            foreach(
                $command->actionEventLog()->actionEvents()
                as
                $actionEvent
            ) {
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
    private function table(
        array $columnNames,
        array $columnData
    ): void
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

    final public function __construct(private Rig $rig) {}

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
            foreach(
                $command->actionEventLog()->actionEvents()
                as
                $actionEvent
            ) {
                $commandStatusDateTime[] = [
                    CLIColorizer::applyANSIColor(
                        $actionEvent->action()::class,
                        backgroundColorCode: 87
                    ),
                    match($actionEvent->action()->actionStatus()) {
                        ActionStatus::SUCCEEDED => CLIColorizer::applySUCCEEDEDColor($actionEvent->action()->actionStatus()->name),
                        ActionStatus::FAILED => CLIColorizer::applyFAILEDColor($actionEvent->action()->actionStatus()->name),
                        ActionStatus::NOT_PROCESSED => CLIColorizer::applyNOT_PROCESSEDColor($actionEvent->action()->actionStatus()->name),
                    }, // todo color should be green if SUCCEEDED, red if FAILED, grey if NOT_PROCESSED
                    CLIColorizer::applyHighlightColor(
                        $actionEvent->dateTime()->format('Y-m-d H:i:s A'),
                    ),
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

        HEADER;
        $welcomeMessage .= PHP_EOL . 'For help use: ' . PHP_EOL;
        $welcomeMessage .= CLIColorizer::applyHighlightColor('rig --help') . PHP_EOL;
        $welcomeMessage .= PHP_EOL . 'For help with a specific command use: ' . PHP_EOL;
        $welcomeMessage .= CLIColorizer::applyHighlightColor('rig --help command-name') . PHP_EOL;
        intro($welcomeMessage);

    }

    public function render(): void
    {
        $this->displayHeader();
        $this->displayMessages();
        $this->displayActionEventLog();
    }

}

# Actions

class GenerateHelpMessageAction extends Action
{

    private function getDocumentation(string $name): string
    {
        $file = file(__DIR__ . DIRECTORY_SEPARATOR . 'README.md');
        $coordinates = [
            'installation' => [18, 57],
            'getting-started' => [76, 56],
            'help' => [145, 41],
            'delete-route' => [188, 27],
            'list-routes' => [216, 94],
            'new-module' => [311, 96],
            'new-route' => [408, 38],
            'start-servers' => [447, 31],
            'update-route' => [479, 43],
            'version' => [524, 10],
            'view-action-log' => [536, 9],
        ];
        $startingLine = ($coordinates[$name][0] ?? 0);
        $lineLimit = ($coordinates[$name][1] ?? 0);
        return implode(
            '',
            array_slice(
                (is_array($file) ? $file : []),
                $startingLine,
                $lineLimit
            )
        );
    }

    public function do(): GenerateHelpMessageAction
    {
        $arguments = $this->arguments()->asArray();
        $topic = str_replace('--', '', $arguments['help'] ?? '');
        $helpMessage = match($topic) {
            'about' => $this->defaultHelpMessage(),
            'delete-route' => $this->getDocumentation('delete-route'),
            'help' => $this->getDocumentation('help'),
            'installation' => $this->getDocumentation('installation'),
            'list-routes' => $this->getDocumentation('list-routes'),
            'new-module' => $this->getDocumentation('new-module'),
            'new-route' => $this->getDocumentation('new-route'),
            'start-servers' => $this->getDocumentation('start-servers'),
            'update-route' => $this->getDocumentation('update-route'),
            'version' => $this->getDocumentation('version'),
            'view-action-log' => $this->getDocumentation('view-action-log'),
            'view-readme' => 'View rig\'s README.md',
            'getting-started' => $this->getDocumentation('getting-started'),
            default => $this->defaultHelpMessage(),
        };

        $this->messageLog()->addMessage(
            CLIColorizer::applyHighlightColor(
                'rig' . (!empty($topic) ? ' --' . $topic : ''),
            ),
        );
        $this->messageLog()->addMessage($helpMessage);
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }

    private function defaultHelpMessage(): string
    {

        return <<<'HELPMESSAGE'

        rig is a command line utility designed to aide in development
        with the Roady PHP Framework.

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

        For more information about a command use `rig --help COMMAND`

        HELPMESSAGE;
    }
}

class ReadREADMEAction extends Action
{
    public function do(): ReadREADMEAction
    {
        $readme = strval(
            file_get_contents(
                __DIR__ . DIRECTORY_SEPARATOR . 'README.md'
            )
        );
        $parsedown = new Parsedown();
        match(php_sapi_name() === 'cli') {
            true => $this->messageLog()->addMessage(
                CLIColorizer::applyANSIColor($readme, 235)
            ),
            default => $this->messageLog()
                            ->addMessage(
                                $parsedown->text($readme)
                            ),
        };
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class DetermineVersionAction extends Action
{
    public function do(): DetermineVersionAction
    {
        $this->messageLog()->addMessage('rig version ' . CLIColorizer::applyHighlightColor('2.0.0-alpha-12'));
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class HelpCommand extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [
            new GenerateHelpMessageAction(
                $this->arguments(),
                $this->messageLog()
            )
        ];
    }
}

class ViewREADMECommand extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [
            new ReadREADMEAction(
                $this->arguments(),
                $this->messageLog()
            )
        ];
    }
}

class VersionCommand extends Command
{
    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [
            new DetermineVersionAction(
                $this->arguments(),
                $this->messageLog()
            )
        ];
    }
}

class CommandDeterminator
{

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

    public function commandToRun(
        Arguments $arguments,
        ActionEventLog $actionEventLog,
        MessageLog $messageLog
    ): Command
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
        $commandToRunClassString = new ClassString(
            $commandNamespace . $commandName
        );
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
                $arguments,
                $actionEventLog,
                $messageLog,
            );
        }
        return new HelpCommand(
                $arguments,
                $actionEventLog,
                $messageLog,
        );
    }
}

class Arguments
{

    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            // Commands
            'delete-route' => '',
            'help' => '',
            'list-routes' => '',
            'new-module' => '',
            'new-route' => '',
            'start-servers' => '',
            'update-route' => '',
            'version' => '',
            'view-action-log' => '',
            'view-readme' => '',
            // Command Options
            'authority' => '',
            'defined-for-authorities' => '',
            'defined-for-files' => '',
            'defined-for-modules' => '',
            'defined-for-named-positions' => '',
            'defined-for-positions' => '',
            'defined-for-requests' => '',
            'for-authority' => '',
            'module-name' => '',
            'named-positions' => '',
            'no-boilerplate' => '',
            'open-in-browser' => '',
            'path-to-roady-project' => '',
            'ports' => '',
            'relative-path' => '',
            'responds-to' => '',
            'route-hash' => '',
        ];
    }
}

class WebArguments extends Arguments
{

    private function parameterNameIfSpecified(string $name): string
    {
        return (
            isset($_POST[$name]) || isset($_GET[$name])
            ? $name
            : ''
        );
    }

    private function parameterValueIfSpecified(string $name): string
    {
        return (
            isset($_POST[$name])
            ? $_POST[$name]
            : (isset($_GET[$name]) ? $_GET[$name] : '')
        );
    }

    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            // Commands
            'delete-route' =>
                $this->parameterNameIfSpecified('delete-route'),
            'help' =>
                $this->parameterValueIfSpecified('help'),
            'list-routes' =>
                $this->parameterNameIfSpecified('list-routes'),
            'new-module' =>
                $this->parameterNameIfSpecified('new-module'),
            'new-route' =>
                $this->parameterNameIfSpecified('new-route'),
            'start-servers' =>
                $this->parameterNameIfSpecified('start-servers'),
            'update-route' =>
                $this->parameterNameIfSpecified('update-route'),
            'version' =>
                $this->parameterNameIfSpecified('version'),
            'view-action-log' =>
                $this->parameterNameIfSpecified('view-action-log'),
            'view-readme' =>
                $this->parameterNameIfSpecified('view-readme'),
            // Command Options
            'authority' =>
                $this->parameterValueIfSpecified('authority'),
            'defined-for-authorities' =>
                $this->parameterValueIfSpecified('defined-for-authorities'),
            'defined-for-files' =>
                $this->parameterValueIfSpecified('defined-for-files'),
            'defined-for-modules' =>
                $this->parameterValueIfSpecified('defined-for-modules'),
            'defined-for-named-positions' =>
                $this->parameterValueIfSpecified('defined-for-named-positions'),
            'defined-for-positions' =>
                $this->parameterValueIfSpecified('defined-for-positions'),
            'defined-for-requests' =>
                $this->parameterValueIfSpecified('defined-for-requests'),
            'for-authority' =>
                $this->parameterValueIfSpecified('for-authority'),
            'module-name' =>
                $this->parameterValueIfSpecified('module-name'),
            'named-positions' =>
                $this->parameterValueIfSpecified('named-positions'),
            'no-boilerplate' =>
                $this->parameterValueIfSpecified('no-boilerplate'),
            'open-in-browser' =>
                $this->parameterValueIfSpecified('open-in-browser'),
            'path-to-roady-project' =>
                $this->parameterValueIfSpecified('path-to-roady-project'),
            'ports' =>
                $this->parameterValueIfSpecified('ports'),
            'relative-path' =>
                $this->parameterValueIfSpecified('relative-path'),
            'responds-to' =>
                $this->parameterValueIfSpecified('responds-to'),
            'route-hash' =>
                $this->parameterValueIfSpecified('route-hash'),
        ];
    }

}

class CLIArguments extends Arguments
{

    /** @param array<mixed> $opts */
    private function parameterNameIfSpecified(
        array $opts,
        string $name
    ): string
    {
        return (isset($opts[$name]) ? $name : '');
    }

    /** @param array<mixed> $opts */
    private function parameterValueIfSpecified(
        array $opts,
        string $name
    ): string
    {
        return (isset($opts[$name]) && is_string($opts[$name]) ? $opts[$name] : '');
    }

    /** @return array<string, string> */
    public function asArray(): array
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
                    'delete-route' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'delete-route'
                        ),
                    'help' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'help'
                        ),
                    'list-routes' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'list-routes'
                        ),
                    'new-module' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'new-module'
                        ),
                    'new-route' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'new-route'
                        ),
                    'start-servers' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'start-servers'
                        ),
                    'update-route' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'update-route'
                        ),
                    'version' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'version'
                        ),
                    'view-action-log' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'view-action-log'
                        ),
                    'view-readme' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'view-readme'
                        ),
                     // Command Options
                    'authority' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'authority'
                        ),
                    'defined-for-authorities' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-authorities'
                        ),
                    'defined-for-files' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-files'
                        ),
                    'defined-for-modules' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-modules'
                        ),
                    'defined-for-named-positions' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-named-positions'
                        ),
                    'defined-for-positions' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-positions'
                        ),
                    'defined-for-requests' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'defined-for-requests'
                        ),
                    'for-authority' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'for-authority'
                        ),
                    'module-name' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'module-name'
                        ),
                    'named-positions' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'named-positions'
                        ),
                    'no-boilerplate' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'no-boilerplate'
                        ),
                    'open-in-browser' =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            'open-in-browser'
                        ),
                    'path-to-roady-project' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'path-to-roady-project'
                        ),
                    'ports' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'ports'
                        ),
                    'relative-path' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'relative-path'
                        ),
                    'responds-to' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'responds-to'
                        ),
                    'route-hash' =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            'route-hash'
                        ),
                ],
            default => []

        };
        return $cliArguments;
    }
}

$rig = new Rig();
$commandDeterminator = new CommandDeterminator();
$actionEventLog = new ActionEventLog();
$messageLog = new MessageLog();

if(php_sapi_name() === 'cli') {

    $arguments = new CLIArguments();
    $rigCLUI = new RigCLUI(
        $rig->run(
            $commandDeterminator->commandToRun(
                $arguments,
                $actionEventLog,
                $messageLog,
            )
        ),
    );

    $rigCLUI->render();
    exit;
    /**
     * To test via cli run:
     *
     * rig --delete-route --help foo --list-routes --new-module --new-route --start-servers --update-route --version --view-action-log --view-readme --authority localhost:8080 --defined-for-authorities localhost:8080 --defined-for-files homepage.html --defined-for-modules HelloWorld --defined-for-named-positions roady-ui-main-content --defined-for-positions 2 --defined-for-requests Homepage --for-authority localhost:8080 --module-name HelloWorld --named-positions roady-ui-main-content --no-boilerplate --open-in-browser --path-to-roady-project ./ --ports 3494 --relative-path homepage.html --responds-to Home --route-hash 2340984
     *
     */
}

$arguments = new WebArguments();
$rigWebUI = new RigWebUI(
    $rig->run(
        $commandDeterminator->commandToRun(
            $arguments,
            $actionEventLog,
            $messageLog
        )
    )
);
$rigWebUI->render();

/**
 * To test via web browser, start a server via
 * `php -S localhost:8080` and navigate to:
 *
 * http://localhost:8080/rig.php?delete-route&version&help=new-route&list-routes&new-module&new-route&start-servers&update-route&version&view-action-log&view-readme&authority=localhost:8080&defined-for-authorities=localhost:8080,%20roady.tech&defined-for-files=homepage.html&defined-for-modules=HelloWorld&defined-for-named-positions=roady-ui-footer&defined-for-positions=10,%2011&defined-for-requests=Homepage,%20HelloWorld&for-authority=localhost:8080&module-name=HelloWorld&named-positions=[{%22position-name%22:%22roady-ui-footer%22,%22position%22:10},%20{%22position-name%22:%22roady-ui-header%22,%22position%22:11}]&no-boilerplate&open-in-browser&path-to-roady-project=./&ports=8080&relative-path=output/Homepage.html&responds-to=Homepage&route-hash=234908
 *
 * To use curl:
 * curl -d 'delete-route&version&help=new-route&list-routes&new-module&new-route&start-servers&update-route&version&view-action-log&view-readme&authority=localhost:8080&defined-for-authorities=localhost:8080,%20roady.tech&defined-for-files=homepage.html&defined-for-modules=HelloWorld&defined-for-named-positions=roady-ui-footer&defined-for-positions=10,%2011&defined-for-requests=Homepage,%20HelloWorld&for-authority=localhost:8080&module-name=HelloWorld&named-positions=[{%22position-name%22:%22roady-ui-footer%22,%22position%22:10},%20{%22position-name%22:%22roady-ui-header%22,%22position%22:11}]&no-boilerplate&open-in-browser&path-to-roady-project=./&ports=8080&relative-path=output/Homepage.html&responds-to=Homepage&route-hash=234908' http://localhost:8080/rig.php
 */
