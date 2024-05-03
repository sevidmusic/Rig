<?php

/**
 * To test via web browser, start a server via
 * `php -S localhost:8080` and navigate to:
 *
 * http://localhost:8080/rig.php?delete-route&version&help=new-route&list-routes&new-module&new-route&start-servers&update-route&version&view-action-log&view-readme&authority=localhost:8080&defined-for-authorities=localhost:8080,%20roady.tech&defined-for-files=homepage.html&defined-for-modules=HelloWorld&defined-for-named-positions=roady-ui-footer&defined-for-positions=10,%2011&defined-for-requests=Homepage,%20HelloWorld&for-authority=localhost:8080&module-name=HelloWorld&named-positions=[{%22position-name%22:%22roady-ui-footer%22,%22position%22:10},%20{%22position-name%22:%22roady-ui-header%22,%22position%22:11}]&no-boilerplate&open-in-browser&path-to-roady-project=./&ports=8080&relative-path=output/Homepage.html&responds-to=Homepage&route-hash=234908
 *
 * To use curl:
 * curl -d 'delete-route&version&help=new-route&list-routes&new-module&new-route&start-servers&update-route&version&view-action-log&view-readme&authority=localhost:8080&defined-for-authorities=localhost:8080,%20roady.tech&defined-for-files=homepage.html&defined-for-modules=HelloWorld&defined-for-named-positions=roady-ui-footer&defined-for-positions=10,%2011&defined-for-requests=Homepage,%20HelloWorld&for-authority=localhost:8080&module-name=HelloWorld&named-positions=[{%22position-name%22:%22roady-ui-footer%22,%22position%22:10},%20{%22position-name%22:%22roady-ui-header%22,%22position%22:11}]&no-boilerplate&open-in-browser&path-to-roady-project=./&ports=8080&relative-path=output/Homepage.html&responds-to=Homepage&route-hash=234908' http://localhost:8080/rig.php
 */

declare(strict_types=1);

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR  . 'Parsedown.php';


use Darling\PHPFileSystemPaths\classes\paths\PathToExistingDirectory;
use \Darling\PHPTextTypes\classes\strings\ClassString;
use \Darling\PHPTextTypes\classes\strings\Text;
use \Darling\PHPTextTypes\classes\strings\Name;
use \Darling\PHPTextTypes\classes\strings\SafeText;
use \Darling\PHPTextTypes\classes\collections\SafeTextCollection;
use \Darling\RoadyModuleUtilities\classes\paths\PathToDirectoryOfRoadyModules;
use \Darling\RoadyModuleUtilities\classes\paths\PathToRoadyModuleDirectory;
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
    public static function applyANSIColor(
        string $string,
        int $backgroundColorCode
    ): string {
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

    public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog
    ) { }

    public function do(): Action
    {
        $this->messageLog->addMessage(
            'Perfomred action: ' .
            CLIColorizer::applyHighlightColor($this::class)
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
                        ActionStatus::SUCCEEDED =>
                            CLIColorizer::applySUCCEEDEDColor(
                                $actionEvent->action()
                                            ->actionStatus()->name
                            ),
                        ActionStatus::FAILED =>
                            CLIColorizer::applyFAILEDColor(
                                $actionEvent->action()
                                            ->actionStatus()->name
                            ),
                        ActionStatus::NOT_PROCESSED =>
                            CLIColorizer::applyNOT_PROCESSEDColor(
                                $actionEvent->action()
                                            ->actionStatus()->name
                            ),
                    },
                    CLIColorizer::applyHighlightColor(
                        $actionEvent->dateTime()
                                    ->format('Y-m-d H:i:s A'),
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

        $welcomeMessage = PHP_EOL;
        $welcomeMessage .= date('l Y, F jS h:i:s A');
        $welcomeMessage .= PHP_EOL;
        $welcomeMessage .= <<<'HEADER'
               _
          ____(_)__ _
         / __/ / _ `/
        /_/ /_/\_, /
              /___/

        HEADER;
        $welcomeMessage .= PHP_EOL;
        $welcomeMessage .= 'For help use: ';
        $welcomeMessage .= str_repeat(PHP_EOL, 2);
        $welcomeMessage .= CLIColorizer::applyHighlightColor('rig --help');
        $welcomeMessage .= str_repeat(PHP_EOL, 2);
        $welcomeMessage .= 'For help with a specific command use: ';
        $welcomeMessage .= str_repeat(PHP_EOL, 2);
        $welcomeMessage .= CLIColorizer::applyHighlightColor(
                               'rig --help command-name'
                           );
        $welcomeMessage .= PHP_EOL;
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

    private const GETTING_STARTED_TOPIC = 'getting-started';
    private const INSTALLTION_TOPIC = 'installation';
    private const ABOUT_TOPIC = 'about';

    private function getDocumentation(string $name): string
    {
        $file = file(__DIR__ . DIRECTORY_SEPARATOR . 'README.md');
        $coordinates = [
            self::INSTALLTION_TOPIC => [18, 57],
            self::GETTING_STARTED_TOPIC => [76, 56],
            RigCommand::Help->value => [145, 41],
            RigCommand::DeleteRoute->value => [188, 27],
            RigCommand::ListRoutes->value => [216, 94],
            RigCommand::NewModule->value => [311, 96],
            RigCommand::NewRoute->value => [408, 38],
            RigCommand::StartServers->value => [447, 31],
            RigCommand::UpdateRoute->value => [479, 43],
            RigCommand::Version->value => [524, 10],
            RigCommand::ViewActionLog->value => [536, 9],
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
        $topic = str_replace('--', '', $arguments[RigCommand::Help->value] ?? '');
        $helpMessage = match($topic) {
            self::ABOUT_TOPIC => $this->defaultHelpMessage(),
            RigCommand::DeleteRoute->value => $this->getDocumentation(RigCommand::DeleteRoute->value),
            RigCommand::Help->value => $this->getDocumentation(RigCommand::Help->value),
            self::INSTALLTION_TOPIC => $this->getDocumentation(self::INSTALLTION_TOPIC),
            RigCommand::ListRoutes->value => $this->getDocumentation(RigCommand::ListRoutes->value),
            RigCommand::NewModule->value => $this->getDocumentation(RigCommand::NewModule->value),
            RigCommand::NewRoute->value => $this->getDocumentation(RigCommand::NewRoute->value),
            RigCommand::StartServers->value => $this->getDocumentation(RigCommand::StartServers->value),
            RigCommand::UpdateRoute->value => $this->getDocumentation(RigCommand::UpdateRoute->value),
            RigCommand::Version->value => $this->getDocumentation(RigCommand::Version->value),
            RigCommand::ViewActionLog->value => $this->getDocumentation(RigCommand::ViewActionLog->value),
            RigCommand::ViewReadme->value => 'View rig\'s README.md',
            self::GETTING_STARTED_TOPIC => $this->getDocumentation(self::GETTING_STARTED_TOPIC),
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

        $helpMessage = <<<'HELPMESSAGE'

        rig is a command line utility designed to aide in development
        with the Roady PHP Framework.

        The following commands are provided by rig:

        HELPMESSAGE;

        foreach(RigCommand::cases() as $value) {
            $helpMessage .= PHP_EOL . 'rig --' . $value->value;
        }

        $helpMessage .= PHP_EOL;

        $helpMessage .= <<<'HELPMESSAGE'

        For more information about a command use `rig --help COMMAND`

        HELPMESSAGE;

        return $helpMessage;
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
        $this->messageLog()->addMessage(
            'rig version ' .
            CLIColorizer::applyHighlightColor('2.0.0-alpha-12')
        );
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class CreateNewDirectoryForRoadyProjectAction extends Action
{

    public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog,
        private string $relativePathToNewDirectory,
    ) {
        parent::__construct($this->arguments, $this->messageLog);
    }

    public function do(): CreateNewDirectoryForRoadyProjectAction
    {
        $this->attemptToCreateNewDirectory();
        $this->messageLog()->addMessage(
            match($this->actionStatus()) {
                ActionStatus::FAILED =>
                    CLIColorizer::applyFAILEDColor(
                        'Failed to create new directory ' .
                        $this->pathToNewDirectory()
                    ),
                ActionStatus::SUCCEEDED =>
                    CLIColorizer::applySUCCEEDEDColor(
                        'Created new directory ' .
                        $this->pathToNewDirectory()
                    ),
                ActionStatus::NOT_PROCESSED =>
                    CLIColorizer::applyNOT_PROCESSEDColor(
                        'Creation of '. $this->pathToNewDirectory() . 'was not processed.'
                    ),
            }
        );
        return $this;
    }

    private function attemptToCreateNewDirectory(): void
    {
        dump($this->pathToNewDirectory());
        $this->actionStatus = match(mkdir($this->pathToNewDirectory())) {
            true => ActionStatus::SUCCEEDED,
            false => ActionStatus::FAILED,
        };
    }

    private function pathToSafeTextCollection(string $path): SafeTextCollection
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, $path);
        $safeTextParts = [];
        foreach($pathParts as $part) {
            if(!empty($part)) {
                $safeTextParts[] = new SafeText(new Text($part));
            }
        }
        return new SafeTextCollection(...$safeTextParts);
    }

    private function pathToExistingDirectory(string $path): PathToExistingDirectory
    {
        return new PathToExistingDirectory(
            $this->pathToSafeTextCollection($path),
        );
    }

    private function currentDirectoryPath(): string
    {
        $realpath = realpath(__DIR__ . DIRECTORY_SEPARATOR);
        return match(is_string($realpath)) {
            true => $realpath,
            false => sys_get_temp_dir(),
        };
    }

    private function pathToNewDirectory(): string
    {
        $safePathParts = $this->pathToSafeTextCollection($this->relativePathToNewDirectory);
        $safePath = DIRECTORY_SEPARATOR;
        foreach($safePathParts->collection() as $safePathPart) {
            $safePath .= $safePathPart->__toString() . DIRECTORY_SEPARATOR;
        }
        return $this->expectedPathToRoadyProjectsRootDirectory()->__toString() . $safePath;
    }

    private function expectedPathToRoadyProjectsRootDirectory(): PathToExistingDirectory
    {
        $specifiedPath = $this->arguments()
                              ->asArray()[RigCommandArgument::PathToRoadyProject->value];
        $pathToRoadyProject = match(
            empty($specifiedPath)
        ) {
            true => $this->currentDirectoryPath(),
            false => $specifiedPath . DIRECTORY_SEPARATOR,
        };
        return $this->pathToExistingDirectory($pathToRoadyProject);
    }
}

class CreateNewModuleAction extends Action
{

    private const MODULES_DIRECTORY_NAME = 'modules';
    private const MODULE_NAME_ARGUMENT = 'module-name';

    public function do(): CreateNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModuleDirectory();
        if(
            $this->actionStatus() !== ActionStatus::FAILED
            &&
            $this->noBoilerplateSpecified() === false
        ) {
            $this->attemptToCreateNewModulesCssDirectory();
            $this->attemptToCreateNewModulesJsDirectory();
            $this->attemptToCreateNewModulesOutputDirectory();
            $this->attemptToCreateNewModulesInitialOutputFile();
            $this->attemptToCreateNewModulesInitialRoutesConfigurationFile();
        }
        $this->messageLog()->addMessage(
            match($this->actionStatus()) {
                ActionStatus::FAILED =>
                    CLIColorizer::applyFAILEDColor(
                        'Failed to create new module ' . $this->specifiedModuleName()
                    ),
                ActionStatus::SUCCEEDED =>
                    CLIColorizer::applySUCCEEDEDColor(
                        'Created new module ' . $this->specifiedModuleName()

                    ),
                ActionStatus::NOT_PROCESSED =>
                    CLIColorizer::applyNOT_PROCESSEDColor(
                        'Creation of new module ' . $this->specifiedModuleName() . 'was not processed.'
                    ),
            }
        );
        return $this;
    }

    private function failIfModuleNameWasNotSpecified(): void
    {
        $specifiedModuleName = $this->specifiedModuleName();
        if(empty($specifiedModuleName)) {
            $this->messageLog()->addMessage(
                CLIColorizer::applyFAILEDColor(
                    'Please specify a --module-name to use for ' .
                     'the new module'
                )
            );
            $this->actionStatus = ActionStatus::FAILED;
        }
    }

    private function specifiedModuleName(): string
    {
        return $this->arguments()->asArray()[self::MODULE_NAME_ARGUMENT];
    }

    private function relativePathToNewModuleDirectory(): string
    {
        return self::MODULES_DIRECTORY_NAME .
            DIRECTORY_SEPARATOR .
            $this->specifiedModuleName();
    }

    private function attemptToCreateNewModuleDirectory(): void
    {
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewDirectoryForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->relativePathToNewModuleDirectory(),
            );
        $createNewDirectoryForRoadyProjectAction->do();
        $this->actionStatus = match($createNewDirectoryForRoadyProjectAction->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function attemptToCreateNewModulesCssDirectory(): void
    {
        $this->actionStatus = match($this->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            default => match(
                mkdir(
                    $this->specifiedModuleName() .
                        DIRECTORY_SEPARATOR .
                        'css'
                )
            ) {
                true => ActionStatus::SUCCEEDED,
                false => ActionStatus::FAILED,
            },
        };
    }

    private function attemptToCreateNewModulesJsDirectory(): void
    {
        $this->actionStatus = match($this->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            default => match(
                mkdir(
                    $this->specifiedModuleName() .
                        DIRECTORY_SEPARATOR .
                        'js'
                )
            ) {
                true => ActionStatus::SUCCEEDED,
                false => ActionStatus::FAILED,
            },
        };
    }

    private function attemptToCreateNewModulesInitialOutputFile(): void
    {
        $specifiedModuleName = $this->specifiedModuleName();
        $initialOutput = <<<HTML
        <h1>{$specifiedModuleName}</h1>
        <p>Initial output...</p>
        HTML;
        $this->actionStatus = match($this->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            default => match(
                file_put_contents(
                    $this->specifiedModuleName() .
                        DIRECTORY_SEPARATOR .
                        'output'.
                        DIRECTORY_SEPARATOR .
                        $specifiedModuleName . '.html',
                    $initialOutput
                ) > 0
            ) {
                true => ActionStatus::SUCCEEDED,
                false => ActionStatus::FAILED,
            },
        };
    }

    private function attemptToCreateNewModulesInitialRoutesConfigurationFile(): void
    {
        $specifiedModuleName = $this->specifiedModuleName();
        $json = <<<"JSON"
        [
            {
                "module-name": "{$specifiedModuleName}",
                "responds-to": [
                    "homepage"
                ],
                "named-positions": [
                    {
                        "position-name": "roady-ui-main-content",
                        "position": 0
                    }
                ],
                "relative-path": "output\/{$specifiedModuleName}.html"
            }
        ]
        JSON;
        $this->actionStatus = match($this->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            default => match(
                file_put_contents(
                    $this->specifiedModuleName() .
                        DIRECTORY_SEPARATOR .
                        'localhost.8080.json',
                    $json
                ) > 0
            ) {
                true => ActionStatus::SUCCEEDED,
                false => ActionStatus::FAILED,
            },
        };
    }
    private function attemptToCreateNewModulesOutputDirectory(): void
    {
        $this->actionStatus = match($this->actionStatus()) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            default => match(
                mkdir(
                    $this->specifiedModuleName() .
                        DIRECTORY_SEPARATOR .
                        'output'
                )
            ) {
                true => ActionStatus::SUCCEEDED,
                false => ActionStatus::FAILED,
            },
        };
    }

    private function noBoilerplateSpecified(): bool
    {
        return !empty($this->arguments()->asArray()['no-boilerplate']);
    }

}

# Commands
/*
 // DONE
 rig --help
 rig --version
 rig --view-readme
 rig --new-module
 // TODO
 rig --new-route
 rig --list-routes
 rig --delete-route
 rig --update-route
 rig --start-servers
 rig --view-action-log
*/

class NewModuleCommand  extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [
            new CreateNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            )
        ];
    }
}

class NewRouteCommand extends Command
{

}

class ListRoutesCommand extends Command
{

}

class DeleteRouteCommand extends Command
{

}

class UpdateRouteCommand extends Command
{

}

class StartServersCommand extends Command
{

}

class ViewActionLogCommand extends Command
{

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

enum RigCommand: string
{
    case DeleteRoute = 'delete-route';
    case Help = 'help';
    case ListRoutes = 'list-routes';
    case NewModule = 'new-module';
    case NewRoute = 'new-route';
    case StartServers = 'start-servers';
    case UpdateRoute = 'update-route';
    case Version = 'version';
    case ViewActionLog = 'view-action-log';
    case ViewReadme = 'view-readme';
}

enum RigCommandArgument: string
{
    // Command Options
    case Authority = 'authority';
    case DefinedForAuthorities = 'defined-for-authorities';
    case DefinedForFiles = 'defined-for-files';
    case DefinedForModules = 'defined-for-modules';
    case DefinedForNamedPositions = 'defined-for-named-positions';
    case DefinedForPositions = 'defined-for-positions';
    case DefinedForRequests = 'defined-for-requests';
    case ForAuthority = 'for-authority';
    case ModuleName = 'module-name';
    case NamedPositions = 'named-positions';
    case NoBoilerplate = 'no-boilerplate';
    case OpenInBrowser = 'open-in-browser';
    case PathToRoadyProject = 'path-to-roady-project';
    case Ports = 'ports';
    case RelativePath = 'relative-path';
    case RespondsTo = 'responds-to';
    case RouteHash = 'route-hash';

}

class Arguments
{

    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            // Commands
            RigCommand::DeleteRoute->value => '',
            RigCommand::Help->value => '',
            RigCommand::ListRoutes->value => '',
            RigCommand::NewModule->value => '',
            RigCommand::NewRoute->value => '',
            RigCommand::StartServers->value => '',
            RigCommand::UpdateRoute->value => '',
            RigCommand::Version->value => '',
            RigCommand::ViewActionLog->value => '',
            RigCommand::ViewReadme->value => '',
            // Command Options
            RigCommandArgument::Authority->value => '',
            RigCommandArgument::DefinedForAuthorities->value => '',
            RigCommandArgument::DefinedForFiles->value => '',
            RigCommandArgument::DefinedForModules->value => '',
            RigCommandArgument::DefinedForNamedPositions->value => '',
            RigCommandArgument::DefinedForPositions->value => '',
            RigCommandArgument::DefinedForRequests->value => '',
            RigCommandArgument::ForAuthority->value => '',
            RigCommandArgument::ModuleName->value => '',
            RigCommandArgument::NamedPositions->value => '',
            RigCommandArgument::NoBoilerplate->value => '',
            RigCommandArgument::OpenInBrowser->value => '',
            RigCommandArgument::PathToRoadyProject->value => '',
            RigCommandArgument::Ports->value => '',
            RigCommandArgument::RelativePath->value => '',
            RigCommandArgument::RespondsTo->value => '',
            RigCommandArgument::RouteHash->value => '',
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
            RigCommand::DeleteRoute->value =>
                $this->parameterNameIfSpecified(RigCommand::DeleteRoute->value),
            RigCommand::Help->value =>
                $this->parameterValueIfSpecified(RigCommand::Help->value),
            RigCommand::ListRoutes->value =>
                $this->parameterNameIfSpecified(RigCommand::ListRoutes->value),
            RigCommand::NewModule->value =>
                $this->parameterNameIfSpecified(RigCommand::NewModule->value),
            RigCommand::NewRoute->value =>
                $this->parameterNameIfSpecified(RigCommand::NewRoute->value),
            RigCommand::StartServers->value =>
                $this->parameterNameIfSpecified(RigCommand::StartServers->value),
            RigCommand::UpdateRoute->value =>
                $this->parameterNameIfSpecified(RigCommand::UpdateRoute->value),
            RigCommand::Version->value =>
                $this->parameterNameIfSpecified(RigCommand::Version->value),
            RigCommand::ViewActionLog->value =>
                $this->parameterNameIfSpecified(RigCommand::ViewActionLog->value),
            RigCommand::ViewReadme->value =>
                $this->parameterNameIfSpecified(RigCommand::ViewReadme->value),
            RigCommandArgument::Authority->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::Authority->value),
            RigCommandArgument::DefinedForAuthorities->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForAuthorities->value),
            RigCommandArgument::DefinedForFiles->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForFiles->value),
            RigCommandArgument::DefinedForModules->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForModules->value),
            RigCommandArgument::DefinedForNamedPositions->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForNamedPositions->value),
            RigCommandArgument::DefinedForPositions->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForPositions->value),
            RigCommandArgument::DefinedForRequests->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::DefinedForRequests->value),
            RigCommandArgument::ForAuthority->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::ForAuthority->value),
            RigCommandArgument::ModuleName->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::ModuleName->value),
            RigCommandArgument::NamedPositions->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::NamedPositions->value),
            RigCommandArgument::NoBoilerplate->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::NoBoilerplate->value),
            RigCommandArgument::OpenInBrowser->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::OpenInBrowser->value),
            RigCommandArgument::PathToRoadyProject->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::PathToRoadyProject->value),
            RigCommandArgument::Ports->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::Ports->value),
            RigCommandArgument::RelativePath->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::RelativePath->value),
            RigCommandArgument::RespondsTo->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::RespondsTo->value),
            RigCommandArgument::RouteHash->value =>
                $this->parameterValueIfSpecified(RigCommandArgument::RouteHash->value),
        ];
    }

}

class CLIArguments extends Arguments
{

    private const DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT = ':';
    private const DELIMITER_FOR_ARGUMENT_THAT_DOES_NOT_ACCEPTS_USER_INPUT = self::DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT . self::DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT;

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


    private function generateLongOptionForArgumentThatAcceptsUserInput(RigCommand|RigCommandArgument $argument): string
    {
        return $argument->value .
            self::DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT;
    }

    private function generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand|RigCommandArgument $argument): string
    {
        return $argument->value .
            self::DELIMITER_FOR_ARGUMENT_THAT_DOES_NOT_ACCEPTS_USER_INPUT;
    }

    /** @return array<string, string> */
    public function asArray(): array
    {
        $longOpts = [
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::DeleteRoute),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommand::Help),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::ListRoutes),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::NewModule),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::NewRoute),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::StartServers),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::UpdateRoute),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::Version),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::ViewActionLog),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommand::ViewReadme),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::Authority),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForAuthorities),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForFiles),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForModules),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForNamedPositions),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForPositions),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::DefinedForRequests),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::ForAuthority),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::ModuleName),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::NamedPositions),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommandArgument::NoBoilerplate),
                    $this->generateLongOptionForArgumentThatDoesNotAcceptsUserInput(RigCommandArgument::OpenInBrowser),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::PathToRoadyProject),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::Ports),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::RelativePath),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::RespondsTo),
                    $this->generateLongOptionForArgumentThatAcceptsUserInput(RigCommandArgument::RouteHash),
        ];
        $opts = getopt('', $longOpts);
        $cliArguments = match(is_array($opts)) {
            true =>
                [
                    RigCommand::DeleteRoute->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::DeleteRoute->value
                        ),
                    RigCommand::Help->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommand::Help->value
                        ),
                    RigCommand::ListRoutes->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::ListRoutes->value
                        ),
                    RigCommand::NewModule->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::NewModule->value
                        ),
                    RigCommand::NewRoute->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::NewRoute->value
                        ),
                    RigCommand::StartServers->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::StartServers->value
                        ),
                    RigCommand::UpdateRoute->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::UpdateRoute->value
                        ),
                    RigCommand::Version->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::Version->value
                        ),
                    RigCommand::ViewActionLog->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::ViewActionLog->value
                        ),
                    RigCommand::ViewReadme->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommand::ViewReadme->value
                        ),
                    RigCommandArgument::Authority->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::Authority->value
                        ),
                    RigCommandArgument::DefinedForAuthorities->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForAuthorities->value
                        ),
                    RigCommandArgument::DefinedForFiles->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForFiles->value
                        ),
                    RigCommandArgument::DefinedForModules->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForModules->value
                        ),
                    RigCommandArgument::DefinedForNamedPositions->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForNamedPositions->value
                        ),
                    RigCommandArgument::DefinedForPositions->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForPositions->value
                        ),
                    RigCommandArgument::DefinedForRequests->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::DefinedForRequests->value
                        ),
                    RigCommandArgument::ForAuthority->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::ForAuthority->value
                        ),
                    RigCommandArgument::ModuleName->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::ModuleName->value
                        ),
                    RigCommandArgument::NamedPositions->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::NamedPositions->value
                        ),
                    RigCommandArgument::NoBoilerplate->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommandArgument::NoBoilerplate->value
                        ),
                    RigCommandArgument::OpenInBrowser->value =>
                        $this->parameterNameIfSpecified(
                            $opts,
                            RigCommandArgument::OpenInBrowser->value
                        ),
                    RigCommandArgument::PathToRoadyProject->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::PathToRoadyProject->value
                        ),
                    RigCommandArgument::Ports->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::Ports->value
                        ),
                    RigCommandArgument::RelativePath->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::RelativePath->value
                        ),
                    RigCommandArgument::RespondsTo->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::RespondsTo->value
                        ),
                    RigCommandArgument::RouteHash->value =>
                        $this->parameterValueIfSpecified(
                            $opts,
                            RigCommandArgument::RouteHash->value
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

