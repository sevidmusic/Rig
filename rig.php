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
/**
 * To test via cli run:
 *
 * rig --delete-route --help foo --list-routes --new-module --new-route --start-servers --update-route --version --view-action-log --view-readme --authority localhost:8080 --defined-for-authorities localhost:8080 --defined-for-files homepage.html --defined-for-modules HelloWorld --defined-for-named-positions roady-ui-main-content --defined-for-positions 2 --defined-for-requests Homepage --for-authority localhost:8080 --module-name HelloWorld --named-positions roady-ui-main-content --no-boilerplate --open-in-browser --path-to-roady-project ./ --ports 3494 --relative-path homepage.html --responds-to Home --route-hash 2340984
 *
 */

declare(strict_types=1);

$_composer_autoload_path = $_composer_autoload_path ?? __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require $_composer_autoload_path;
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR  . 'Parsedown.php';


use \Darling\PHPFileSystemPaths\classes\paths\PathToExistingDirectory;
use \Darling\PHPFileSystemPaths\classes\paths\PathToExistingFile;
use \Darling\PHPTextTypes\classes\collections\SafeTextCollection;
use \Darling\PHPTextTypes\classes\strings\ClassString;
use \Darling\PHPTextTypes\classes\strings\Name;
use \Darling\PHPTextTypes\classes\strings\SafeText;
use \Darling\PHPTextTypes\classes\strings\Text;
use \Darling\Rig\classes\arguments\Arguments as ArgumentsInstance;
use \Darling\Rig\classes\logs\MessageLog;
use \Darling\Rig\classes\messages\Message;
use \Darling\Rig\enums\actions\ActionStatus;
use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;
use \Darling\Rig\interfaces\arguments\Arguments;
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

class Action
{

    private const MODULE_NAME_ARGUMENT = 'module-name';

    protected ActionStatus $actionStatus = ActionStatus::NOT_PROCESSED;

    public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog
    ) { }

    public function do(): Action
    {
        $this->messageLog->addMessage(
            new Message(
                'Perfomred action: ' .
                CLIColorizer::applyHighlightColor($this::class)
            ),
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

    protected function failIfModuleNameWasNotSpecified(): void
    {
        $specifiedModuleName = $this->specifiedModuleName();
        if(empty($specifiedModuleName->__toString())) {
            $this->messageLog()->addMessage(
                new Message(
                    CLIColorizer::applyFAILEDColor(
                        'Please specify a --module-name to use for ' .
                         'the new module'
                    )
                ),
            );
            $this->actionStatus = ActionStatus::FAILED;
        }
    }

    protected function specifiedModuleName(): Name
    {
        return new Name(new Text($this->arguments()->asArray()[self::MODULE_NAME_ARGUMENT]));
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
            if(
                !isset($lastAction)
                ||
                $lastAction->actionStatus() === ActionStatus::SUCCEEDED
            ) {
                $this->messageLog()->addMessage(
                    new Message(
                        'Executing action: ' .
                        CLIColorizer::applyHighlightColor($action::class)
                    ),
                );
                $this->actionEventLog->addActionEvent(
                    new ActionEvent(
                        $action->do(),
                        new DateTimeImmutable('now')
                    )
                );
                $lastAction = $action;
            }
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
                $this->info($message->__toString());
                echo PHP_EOL;
                echo '</div>';
            }
        }
    }

    private function displayActionEventLog(): void
    {
        $command = $this->rig->lastCommandRun();
        $actionStatusDateTime = [];
        if(!is_null($command)) {
            foreach(
                $command->actionEventLog()->actionEvents()
                as
                $actionEvent
            ) {
                $actionStatusDateTime[] = [
                    $actionEvent->action()::class,
                    $actionEvent->action()->actionStatus()->name,
                    $actionEvent->dateTime()->format('Y-m-d H:i:s A')
                ];
            }
            $this->table(
                ['Command', 'Status', 'Date/Time'],
                $actionStatusDateTime,
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
                info($message->__toString());
            }
        }
    }

    private function displayActionEventLog(): void
    {
        $command = $this->rig->lastCommandRun();
        $actionStatusDateTime = [];
        if(!is_null($command)) {
            foreach(
                $command->actionEventLog()->actionEvents()
                as
                $actionEvent
            ) {
                $actionStatusDateTime[] = [
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
                ['Actions', 'Status', 'Date/Time'],
                $actionStatusDateTime,
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
            new Message(
                CLIColorizer::applyHighlightColor(
                    'rig' . (!empty($topic) ? ' --' . $topic : ''),
                ),
            ),
        );
        $this->messageLog()->addMessage(new Message($helpMessage));
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
                new Message(
                    CLIColorizer::applyANSIColor($readme, 235)
                ),
            ),
            default => $this->messageLog()
                            ->addMessage(
                                new Message($parsedown->text($readme))
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
            new Message(
                'rig version ' .
                CLIColorizer::applyHighlightColor('2.0.0-alpha-12')
            ),
        );
        $this->actionStatus = ActionStatus::SUCCEEDED;
        return $this;
    }
}

class RoadyProjectPathInfo
{

    private const MODULES_DIRECTORY_NAME = 'modules';

    public function __construct(
        private Arguments $arguments,
    ) {}

    public function arguments(): Arguments
    {
        return $this->arguments;
    }

    public function relativePathToNewModuleDirectory(Name $moduleName): string
    {
        return self::MODULES_DIRECTORY_NAME .
            DIRECTORY_SEPARATOR .
            $moduleName->__toString();
    }

    public function expectedPathToRoadyProjectsRootDirectory(): PathToExistingDirectory
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

    public function currentDirectoryPath(): string
    {
        $realpath = realpath(__DIR__ . DIRECTORY_SEPARATOR);
        return strval(match(is_string($realpath)) {
            true => $realpath,
            false => sys_get_temp_dir(),
        });
    }

    public function pathToExistingDirectory(string $path): PathToExistingDirectory
    {
        return new PathToExistingDirectory(
            $this->pathToSafeTextCollection($path),
        );
    }

    public function pathToSafeTextCollection(string $path): SafeTextCollection
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

}

class CreateNewFileForRoadyProjectAction extends Action
{

    public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog,
        private RoadyProjectPathInfo $roadyProjectPathInfo,
        private string $relativePathToNewFile,
        private Name $fileName,
        private string $content,
    ) {
        parent::__construct($this->arguments, $this->messageLog);
    }

    public function do(): CreateNewFileForRoadyProjectAction
    {
        $this->attemptToCreateNewFile();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create new file ' .
                            $this->pathToNewFile()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created new file ' .
                            $this->pathToNewFile()
                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of new file '.
                            $this->pathToNewFile() .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function content(): string
    {
        return trim($this->content);
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return $this->roadyProjectPathInfo;
    }

    private function attemptToCreateNewFile(): void
    {
        $this->actionStatus = match(
            !file_exists($this->pathToNewFile()) &&
            (file_put_contents($this->pathToNewFile(), $this->content()) > 0)
        ) {
            true => ActionStatus::SUCCEEDED,
            false => ActionStatus::FAILED,
        };
    }

    private function fileName(): Name
    {
        return $this->fileName;
    }

    private function pathToNewFile(): string
    {
        $safePathParts = $this->roadyProjectPathInfo()
                              ->pathToSafeTextCollection(
                                  $this->relativePathToNewFile
                              );
        $safePath = DIRECTORY_SEPARATOR;
        foreach($safePathParts->collection() as $safePathPart) {
            $safePath .= $safePathPart->__toString() .
                         DIRECTORY_SEPARATOR;
        }
        $safePath .= $this->fileName();
        return $this->roadyProjectPathInfo()
                    ->expectedPathToRoadyProjectsRootDirectory()
                    ->__toString() .
                    $safePath;
    }

}

class CreateNewDirectoryForRoadyProjectAction extends Action
{

    public function __construct(
        private Arguments $arguments,
        private MessageLog $messageLog,
        private RoadyProjectPathInfo $roadyProjectPathInfo,
        private string $relativePathToNewDirectory,
    ) {
        parent::__construct($this->arguments, $this->messageLog);
    }

    public function do(): CreateNewDirectoryForRoadyProjectAction
    {
        $this->attemptToCreateNewDirectory();
        $this->messageLog()->addMessage(
            new Message(
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
                            'Creation of new directory '.
                            $this->pathToNewDirectory() .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return $this->roadyProjectPathInfo;
    }

    private function attemptToCreateNewDirectory(): void
    {
        $this->actionStatus = match(
            !is_dir($this->pathToNewDirectory()) &&
            mkdir($this->pathToNewDirectory())
        ) {
            true => ActionStatus::SUCCEEDED,
            false => ActionStatus::FAILED,
        };
    }

    private function pathToNewDirectory(): string
    {
        $safePathParts = $this->roadyProjectPathInfo()
                              ->pathToSafeTextCollection(
                                  $this->relativePathToNewDirectory
                              );
        $safePath = DIRECTORY_SEPARATOR;
        foreach($safePathParts->collection() as $safePathPart) {
            $safePath .= $safePathPart->__toString() .
                         DIRECTORY_SEPARATOR;
        }
        return $this->roadyProjectPathInfo()
                    ->expectedPathToRoadyProjectsRootDirectory()
                    ->__toString() .
                    $safePath;
    }

}

class CreateRootDirectoryForNewModuleAction extends Action
{

    public function do(): CreateRootDirectoryForNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModulesRootDirectory();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create root directory for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created root directory for new module ' .
                            $this->specifiedModuleName()

                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of root directory for new module ' .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function attemptToCreateNewModulesRootDirectory(): void
    {
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewDirectoryForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->roadyProjectPathInfo(),
                $this->roadyProjectPathInfo()
                     ->relativePathToNewModuleDirectory(
                        $this->specifiedModuleName()
                     ),
            );
        $createNewDirectoryForRoadyProjectAction->do();
        $this->actionStatus = match(
            $createNewDirectoryForRoadyProjectAction->actionStatus()
        ) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return new RoadyProjectPathInfo($this->arguments());
    }

}

class CreateCssDirectoryForNewModuleAction extends Action
{

    public function do(): CreateCssDirectoryForNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModulesCssDirectory();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create css directory for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created css directory for new module ' .
                            $this->specifiedModuleName()

                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of css directory for new module ' .
                            ' was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function attemptToCreateNewModulesCssDirectory(): void
    {
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewDirectoryForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->roadyProjectPathInfo(),
                $this->roadyProjectPathInfo()
                     ->relativePathToNewModuleDirectory(
                        $this->specifiedModuleName()
                     ) .
                     DIRECTORY_SEPARATOR .
                     'css',
            );
        if($this->actionStatus() !== ActionStatus::FAILED) {
            $createNewDirectoryForRoadyProjectAction->do();
        }
        $this->actionStatus = match(
            $createNewDirectoryForRoadyProjectAction->actionStatus()
        ) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return new RoadyProjectPathInfo($this->arguments());
    }

}

class CreateJsDirectoryForNewModuleAction extends Action
{

    public function do(): CreateJsDirectoryForNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModulesJsDirectory();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create js directory for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created js directory for new module ' .
                            $this->specifiedModuleName()

                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of js directory for new module ' .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function attemptToCreateNewModulesJsDirectory(): void
    {
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewDirectoryForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->roadyProjectPathInfo(),
                $this->roadyProjectPathInfo()
                     ->relativePathToNewModuleDirectory(
                        $this->specifiedModuleName()
                     ) .
                     DIRECTORY_SEPARATOR .
                     'js',
            );
        if($this->actionStatus() !== ActionStatus::FAILED) {
            $createNewDirectoryForRoadyProjectAction->do();
        }
        $this->actionStatus = match(
            $createNewDirectoryForRoadyProjectAction->actionStatus()
        ) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return new RoadyProjectPathInfo($this->arguments());
    }

}

class CreateOutputDirectoryForNewModuleAction extends Action
{

    public function do(): CreateOutputDirectoryForNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModulesOutputDirectory();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create output directory for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created output directory for new module ' .
                            $this->specifiedModuleName()

                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of output directory for new module ' .
                            $this->specifiedModuleName() .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function attemptToCreateNewModulesOutputDirectory(): void
    {
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewDirectoryForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->roadyProjectPathInfo(),
                $this->roadyProjectPathInfo()
                     ->relativePathToNewModuleDirectory(
                         $this->specifiedModuleName()
                     ) .
                     DIRECTORY_SEPARATOR .
                     'output',
            );
        if($this->actionStatus() !== ActionStatus::FAILED) {
            $createNewDirectoryForRoadyProjectAction->do();
        }
        $this->actionStatus = match(
            $createNewDirectoryForRoadyProjectAction->actionStatus()
        ) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return new RoadyProjectPathInfo($this->arguments());
    }

}

class CreateInitialOutputFileForNewModuleAction extends Action
{

    public function do(): CreateInitialOutputFileForNewModuleAction
    {
        $this->failIfModuleNameWasNotSpecified();
        $this->attemptToCreateNewModulesInitialOutputFile();
        $this->messageLog()->addMessage(
            new Message(
                match($this->actionStatus()) {
                    ActionStatus::FAILED =>
                        CLIColorizer::applyFAILEDColor(
                            'Failed to create initial output file for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::SUCCEEDED =>
                        CLIColorizer::applySUCCEEDEDColor(
                            'Created initial output file for new module ' .
                            $this->specifiedModuleName()
                        ),
                    ActionStatus::NOT_PROCESSED =>
                        CLIColorizer::applyNOT_PROCESSEDColor(
                            'Creation of intial output file for new module ' .
                            $this->specifiedModuleName() .
                            'was not processed.'
                        ),
                }
            ),
        );
        return $this;
    }

    private function attemptToCreateNewModulesInitialOutputFile(): void
    {
        $initialContent = <<<HTML
        <h1>{$this->specifiedModuleName()}</h1>
        <p>Initial output...</p>
        HTML;
        $createNewDirectoryForRoadyProjectAction =
            new CreateNewFileForRoadyProjectAction(
                $this->arguments(),
                $this->messageLog(),
                $this->roadyProjectPathInfo(),
                $this->roadyProjectPathInfo()
                     ->relativePathToNewModuleDirectory(
                        $this->specifiedModuleName()
                     ) . DIRECTORY_SEPARATOR . 'output',
                new Name(new Text($this->specifiedModuleName() . '.html')),
                $initialContent,
            );
        $createNewDirectoryForRoadyProjectAction->do();
        $this->actionStatus = match(
            $createNewDirectoryForRoadyProjectAction->actionStatus()
        ) {
            ActionStatus::FAILED => ActionStatus::FAILED,
            ActionStatus::SUCCEEDED => ActionStatus::SUCCEEDED,
            ActionStatus::NOT_PROCESSED => ActionStatus::NOT_PROCESSED,
        };
    }

    private function roadyProjectPathInfo(): RoadyProjectPathInfo
    {
        return new RoadyProjectPathInfo($this->arguments());
    }

}

class NewModuleCommand  extends Command
{

    /** @return array<int, Action> $actions */
    public function actions(): array
    {
        return [
            new CreateRootDirectoryForNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            ),
            new CreateCssDirectoryForNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            ),
            new CreateJsDirectoryForNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            ),
            new CreateOutputDirectoryForNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            ),
            new CreateInitialOutputFileForNewModuleAction(
                $this->arguments(),
                $this->messageLog()
            ),
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

class WebArguments extends ArgumentsInstance implements Arguments
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

class CLIArguments extends ArgumentsInstance implements Arguments
{

    private const DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT = ':';
    private const DELIMITER_FOR_ARGUMENT_THAT_DOES_NOT_ACCEPTS_USER_INPUT =
        self::DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT .
        self::DELIMITER_FOR_ARGUMENT_THAT_ACCEPTS_USER_INPUT;

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
        return (
            isset($opts[$name]) && is_string($opts[$name])
            ? $opts[$name]
            : ''
        );
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
    $rigCLUI = new RigCLUI(
        $rig->run(
            $commandDeterminator->commandToRun(
                new CLIArguments(),
                $actionEventLog,
                $messageLog,
            )
        ),
    );
    $rigCLUI->render();
    exit;
}

$rigWebUI = new RigWebUI(
    $rig->run(
        $commandDeterminator->commandToRun(
            new WebArguments(),
            $actionEventLog,
            $messageLog
        )
    )
);

$rigWebUI->render();

