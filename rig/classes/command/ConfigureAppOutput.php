<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;
use rig\classes\command\NewApp;
use rig\classes\command\NewRequest;
use rig\classes\command\NewGlobalResponse;
use rig\classes\command\AssignToResponse;
use rig\classes\command\NewResponse;
use rig\classes\command\NewDynamicOutputComponent;
use rig\classes\command\NewOutputComponent;
use \RuntimeException;

class ConfigureAppOutput extends AbstractCommand implements Command
{

    /**
     * @var UserInterface $ui
     */
    private $ui;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        $this->validateFlags($flags);
        $this->createAppIfItDoesNotExist($userInterface, $flags);
        $this->configureAppropriateOutputComponent($userInterface, $flags);
        $this->configureAppropriateRequests($userInterface, $flags);
        $this->configureAppropriateResponses($userInterface, $flags);
        $this->assignAppropriateComponentsToAppropriateResponses($userInterface, $flags);
        return false;
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function createAppIfItDoesNotExist(UserInterface $userInterface, array $flags): void
    {
        $expectedAppDirectoryPath = $flags['path-to-apps-directory'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0];
        if(!file_exists($expectedAppDirectoryPath)) {
            self::newApp()->run(
                $userInterface,
                self::newApp()->prepareArguments(
                    [
                        '--name',
                        $flags['for-app'][0]
                    ]
                )
            );
        }
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function configureAppropriateOutputComponent(UserInterface $userInterface, array $flags): void
    {
        $initialOutputFile = ($flags['output-source-file'][0] ?? null);
        if(!isset($flags['static'])) {
            self::newDynamicOutputComponent()->run(
                $userInterface,
                self::newDynamicOutputComponent()->prepareArguments(
                    [
                        '--for-app',
                        $flags['for-app'],
                        '--name',
                        $flags['name'],
                        (!is_null($initialOutputFile) ? '--initial-output-file' : ''),
                        ($initialOutputFile ?? ''),
                        '--container',
                        $flags['for-app'][0] . 'DynamicOutput',
                        '--position',
                        ($flags['o-position'][0] ?? '0')
                    ]
                )
            );
            if(is_null($initialOutputFile)) {
                $dynamicOutputFilePath = strval(realpath($flags['path-to-apps-directory'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'DynamicOutput' . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php'));
                file_put_contents($dynamicOutputFilePath, trim(implode(' ', $flags['output'])));
            }
            return;
        }
        if(!is_null($initialOutputFile)) {
            $sourceFilePath = strval(realpath($initialOutputFile));
            $sourceFileContents = strval(file_get_contents($sourceFilePath));
        }
        self::newOutputComponent()->run(
            $userInterface,
            self::newOutputComponent()->prepareArguments(
                [
                    '--for-app',
                    $flags['for-app'],
                    '--name',
                    $flags['name'],
                    '--output',
                    ($sourceFileContents ?? trim(implode(' ', $flags['output']))),
                    '--container',
                    $flags['for-app'][0] . 'Output',
                    '--position',
                    ($flags['o-position'][0] ?? '0')
                ]
            )
        );
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function configureAppropriateRequests(UserInterface $userInterface, array $flags) : void
    {
        if(!empty($flags['relative-urls'])) {
            foreach($flags['relative-urls'] as $key => $relativeUrl) {
                self::newRequest()->run(
                    $userInterface,
                    self::newRequest()->prepareArguments(
                        [
                            '--for-app',
                            $flags['for-app'][0],
                            '--name',
                            $flags['name'][0] . strval($key),
                            ($relativeUrl === '/' ? '' : '--relative-url'),
                            ($relativeUrl === '/' ? '' : $relativeUrl),
                            '--container',
                            $flags['for-app'][0] . 'Requests'
                        ]
                    )
                );
            }
        }
        self::newRequest()->run(
            $userInterface,
            self::newRequest()->prepareArguments(
                [
                    '--for-app',
                    $flags['for-app'][0],
                    '--name',
                    $flags['name'][0],
                    '--relative-url',
                    'index.php?request=' . $flags['name'][0],
                    '--container',
                    $flags['for-app'][0] . 'Requests'
                ]
            )
        );
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function configureAppropriateResponses(UserInterface $userInterface, array $flags) : void
    {
        if(isset($flags['global'])) {
            self::newGlobalResponse()->run(
                $userInterface,
                self::newGlobalResponse()->prepareArguments(
                    [
                        '--for-app',
                        $flags['for-app'][0],
                        '--name',
                        $flags['name'][0],
                        '--position',
                        ($flags['r-position'][0] ?? '0')
                    ]
                )
            );
            return;
        }
        self::newResponse()->run(
            $userInterface,
            self::newResponse()->prepareArguments(
                [
                    '--for-app',
                    $flags['for-app'][0],
                    '--name',
                    $flags['name'][0],
                    '--position',
                    ($flags['r-position'][0] ?? '0')
                ]
            )
        );
    }

    private static function newAssignToResponse(): AssignToResponse
    {
        return new AssignToResponse();
    }

    private static function newGlobalResponse(): NewGlobalResponse
    {
        return new NewGlobalResponse();
    }

    private static function newResponse(): NewResponse
    {
        return new NewResponse();
    }

    private static function newRequest(): NewRequest
    {
        return new NewRequest();
    }

    private static function newApp(): NewApp
    {
        return new NewApp();
    }

    private static function newDynamicOutputComponent(): NewDynamicOutputComponent
    {
        return new NewDynamicOutputComponent();
    }

    private static function newOutputComponent(): NewOutputComponent
    {
        return new NewOutputComponent();
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function validateFlags(array $flags): void
    {
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  You must specify a the name of the App the output is for via the --for-app flag. For help use rig --help --configure-app-output');
        }
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  You must specify a name to use for the Components that will be configured for the output via the --name flag. For help use rig --help --configure-app-output');
        }
        if(!isset($flags['output'][0]) && !isset($flags['output-source-file'][0])) {
            throw new RuntimeException('  You must specify either the --output, or --output-source-file flag. For help use rig --help --configure-app-output');
        }
        if(isset($flags['output-source-file'][0]) && !file_exists($flags['output-source-file'][0])) {
            throw new RuntimeException('  The specified --output-source-file does not exist at ' . $flags['output-source-file'][0]);
        }
        if(isset($flags['output-source-file'][0]) && file_exists($flags['output-source-file'][0]) && !is_file($flags['output-source-file'][0])) {
            throw new RuntimeException('  The specified --output-source-file at ' . $flags['output-source-file'][0] . ' is not a file. Please specify a path to an actual file.');
        }
        $this->verifyOutputNameDoesNotConflictWithExistingConfiguredComponents($flags);
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function verifyOutputNameDoesNotConflictWithExistingConfiguredComponents(array $flags): void
    {
        $appDirectoryPath = $flags['path-to-apps-directory'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0];
        $outputComponentsDirectoryPath = $appDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents';
        $requestsDirectoryPath = $appDirectoryPath . DIRECTORY_SEPARATOR . 'Requests';
        $responseDirectoryPath = $appDirectoryPath . DIRECTORY_SEPARATOR . 'Responses';
        if(file_exists($outputComponentsDirectoryPath . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php')) {
            throw new RuntimeException('  An OutputComponent or DynamicOutputComponent is already configured named ' . $flags['name'][0] . '. Pease specify a unique name for the output.');
        }
        if(file_exists($requestsDirectoryPath . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php')) {
            throw new RuntimeException('  An Request is already configured named ' . $flags['name'][0] . '. Pease specify a unique name for the output.');
        }
        if(!empty($flags['relative-urls'])) {
            foreach($flags['relative-urls'] as $key => $value) {
                if(file_exists($requestsDirectoryPath . DIRECTORY_SEPARATOR . $flags['name'][0] . strval($key) . '.php')) {
                    throw new RuntimeException('  An Request is already configured named ' . $flags['name'][0] . '. Pease specify a unique name for the output.');
                }
            }
        }
        if(file_exists($responseDirectoryPath . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php')) {
            throw new RuntimeException('  An Reqponse or GlobalResponse is already configured named ' . $flags['name'][0] . '. Pease specify a unique name for the output.');
        }

    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function assignAppropriateComponentsToAppropriateResponses(UserInterface $userInterface, array $flags): void {
        $arguments = [
                    '--response',
                    $flags['name'][0],
                    (isset($flags['static']) ? '--output-components' : '--dynamic-output-components'),
                    $flags['name'][0],
                    '--for-app',
                    $flags['for-app'][0],
                    (!isset($flags['global']) ? '--requests' : ''), # @devNote: Requests MUST be defined last so Requests for urls can be added as final arguments!
                    (!isset($flags['global']) ? $flags['name'][0] : ''),
        ];
        if(!isset($flags['global']) && isset($flags['relative-urls']) && is_array($flags['relative-urls'])) {
            foreach($flags['relative-urls'] as $key => $relativeUrl) {
                array_push($arguments, $flags['name'][0] . strval($key));
            }
        }
        self::newAssignToResponse()->run(
            $userInterface,
            self::newAssignToResponse()->prepareArguments($arguments)
        );
    }
}
