<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\ConfigureAppOutput;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class ConfigureAppOutputTest extends TestCase
{

    use TestsCreateApps;
    private UserInterface $ui;
    private ConfigureAppOutput $configureAppOutput;
    private string $currentTestAppName = '';
    private string $currentOutputName = '';
    private string $currentOutput = '';
    private string $currentOutputSourceFile = '';
    private string $currentOPosition = '';
    private string $currentRPosition = '';
    /**
     * @var array<int, string> $currentRelativeUrls
     */
    private array $currentRelativeUrls = [];

    private function configureAppOutput(): ConfigureAppOutput
    {
        if(!isset($this->configureAppOutput)) {
            $this->configureAppOutput = new ConfigureAppOutput();
        }
        return $this->configureAppOutput;
    }

    private function userInterface(): UserInterface
    {
        if(!isset($this->ui)) {
            $this->ui = new CommandLineUI();
        }
        return $this->ui;
    }

    public function testRunThrowsRuntimeExceptionIfForAppIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run(
            $this->userInterface(),
            $this->configureAppOutput()->prepareArguments([])
        );
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run(
            $this->userInterface(),
            $this->configureAppOutput()->prepareArguments(
                $this->getTestArgsForSpecifiedFlags(['--for-app'], __METHOD__)
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfNitherOutputOrOutputSourceFileAreSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run(
            $this->userInterface(),
            $this->configureAppOutput()->prepareArguments(
                $this->getTestArgsForSpecifiedFlags(['--for-app', '--name'], __METHOD__)
            )
        );
    }

    public function testRunCreatesAppSpecifiedByForAppIfAppDoesNotAlreadyExist(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output'], __METHOD__)
        );
        $expectedAppDirectoryPath = $this->determineCurrentAppsDirectoryPath($preparedArguments);
        $expectedCssDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'css';
        $expectedJsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'js';
        $expectedDynamicOutputDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput';
        $expectedOutputComponentsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents';
        $expectedResponsesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Responses';
        $expectedRequestsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests';
        $expectedResourcesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'resources';
        $expectedComponentsPhpFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php';
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->assertTrue(
            is_dir($expectedAppDirectoryPath),
            "ddms --configure-app-output MUST create the App specified by the --for-app " .
            "flag if the App does not already exist. An App should have been created at $expectedAppDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedCssDirectoryPath),
            "ddms --configure-app-output MUST create the App's css directory if the App does " .
            "not already exist. An css directory for the App should have been created at $expectedCssDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedJsDirectoryPath),
            "ddms --configure-app-output MUST create the App's js directory if the App does " .
            "not already exist. An js directory for the App should have been created at $expectedJsDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedDynamicOutputDirectoryPath),
            "ddms --configure-app-output MUST create the App's DynamicOutput directory if the " .
            "App does not already exist. An DynamicOutput directory for the App should have been " .
            "created at $expectedDynamicOutputDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedOutputComponentsDirectoryPath),
            "ddms --configure-app-output MUST create the App's OutputComponents directory if the " .
            "App does not already exist. An OutputComponents directory for the App should have " .
            "been created at $expectedOutputComponentsDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedResponsesDirectoryPath),
            "ddms --configure-app-output MUST create the App's Responses directory if the " .
            "App does not already exist. An Responses directory for the App should have been " .
            "created at $expectedResponsesDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedRequestsDirectoryPath),
            "ddms --configure-app-output MUST create the App's Requests directory if the " .
            "App does not already exist. An Requests directory for the App should have been " .
            "created at $expectedRequestsDirectoryPath"
        );
        $this->assertTrue(
            is_dir($expectedResourcesDirectoryPath),
            "ddms --configure-app-output MUST create the App's resources directory if the " .
            "App does not already exist. An resources directory for the App should have been " .
            "created at $expectedResourcesDirectoryPath"
        );
        $this->assertTrue(
            file_exists($expectedComponentsPhpFilePath),
            "ddms --configure-app-output MUST create the App's Components.php directory if the " .
            "App does not already exist. An Components.php directory for the App should have " .
            "been created at $expectedComponentsPhpFilePath"
        );
    }

    public function testRunConfiguresADynamicOutputComponentForTheOutputIfStaticFlagsIsNotSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output'], __METHOD__)
        );
        $expectedDynamicOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->assertTrue(
            file_exists($expectedDynamicOutputComponentConfigurationFilePath),
            "ddms --configure-app-output MUST configure a DynamicOutputComponent for the " .
            "output if the --static flag is not specified. A DynamicOutputComponent configuration file " .
            "should have been created at $expectedDynamicOutputComponentConfigurationFilePath"
         );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($expectedDynamicOutputComponentConfigurationFilePath)),
                'appComponentsFactory->buildDynamicOutputComponent'
            ),
            'DynamicOutputComponent configuration file was created at ' .
            $expectedDynamicOutputComponentConfigurationFilePath .
            ' but it does not define a call to appComponentsFactory->buildDynamicOutputComponent'
        );
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotUnique(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output'], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileDoesNotExist(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                ['--for-app', '--name', '--output-source-file'],
                __METHOD__,
                badSourceFilePath: true
            )
        );
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileIsNotAFile(): void
    {
        /** @devNote: Intentionally not using getTestArgsForSpecifiedFlags() in this test. */
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            [
                '--for-app',
                $this->getRandomAppName(),
                '--name',
                __METHOD__ . 'TestOutputComponent',
                '--output-source-file',
                __DIR__
            ]
        );
        $this->expectException(RuntimeException::class);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
    }

    public function testRunConfiguresAnOutputComponentForTheOutputIfStaticFlagsIsSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--static'], __METHOD__)
        );
        $expectedOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->assertTrue(
            file_exists($expectedOutputComponentConfigurationFilePath),
            "ddms --configure-app-output MUST configure a OutputComponent for the output if " .
            "the --static flag is not specified. A OutputComponent configuration file should " .
            "have been created at $expectedOutputComponentConfigurationFilePath"
        );
        $this->assertTrue(
            str_contains(strval(file_get_contents($expectedOutputComponentConfigurationFilePath)),
            'appComponentsFactory->buildOutputComponent'), 'OutputComponent configuration file was " .
            "created at ' . $expectedOutputComponentConfigurationFilePath . ' but it does " .
            "not define a call to appComponentsFactory->buildOutputComponent'
        );
    }

    public function testRunSetsDynamicOutputFileContentsToMatchContentsOfSpecifiedOutputSourceFileIfOutputSourceFileFlagIsSpecifiedAndStaticFlagIsNotSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output-source-file'], __METHOD__)
        );
        $expectedOutput = strval(file_get_contents($this->currentOutputSourceFile));
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $dynamicOutputFileContents = strval(
            file_get_contents(
                $this->determineConfigurationFilePath(
                    'DynamicOutput',
                    $preparedArguments
                )
            )
        );
        $this->assertEquals($expectedOutput, $dynamicOutputFileContents);
    }

    public function testRunSetsDynamicOutputFileContentsToMatchSpecifiedOutputIfOutputSourceFileFlagIsNotSpecifiedAndStaticFlagIsNotSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output'], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $dynamicOutputFileContents = strval(
            file_get_contents(
                $this->determineConfigurationFilePath('DynamicOutput', $preparedArguments)
            )
        );
        $this->assertEquals($this->currentOutput, $dynamicOutputFileContents);
    }

    /**
     * This method mocks the filtering performed by the NewOutputComponent command on the specified --output.
     */
    private function mockOutputFilteringPerfomedByNewOutputComponentCommand(string $output): string
    {
        return str_replace(["\\","'"], ["\\\\", "\'"], $output);
    }

    public function testRunConfiguresOutputToMatchContentsOfSpecifiedOutputSourceFileIfOutputSourceFileFlagIsSpecifiedAndStaticFlagIsAlsoSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output-source-file', '--static'], __METHOD__)
        );
        $expectedOutput = $this->mockOutputFilteringPerfomedByNewOutputComponentCommand(
            strval(file_get_contents($this->currentOutputSourceFile))
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $expectedOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $outputComponentConfigurationFileContents = strval(
            file_get_contents($expectedOutputComponentConfigurationFilePath)
        );
        $this->assertTrue(
            str_contains($outputComponentConfigurationFileContents, $expectedOutput),
            'The configured output does not match the contents of the specified ' .
            '--output-source-file even though both the --output-source-file and --static' .
            'flags were specified. The expected output was:' . $expectedOutput
        );
    }


    public function testRunConfiguresOutputToMatchSpecifiedOutputIfOutputSourceFileFlagIsNotSpecifiedAndStaticFlagIsSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsDynamicOutputFileConentToSpecifiedOutput';
        $expectedOutput = $outputName . ' output' . strval(rand(PHP_INT_MIN, PHP_INT_MAX));
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--static'], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $expectedOutputConfigurationFilePath =
        $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $outputConfigurationFileContents = strval(
            file_get_contents(
                $this->determineConfigurationFilePath(
                    'OutputComponents',
                    $preparedArguments
                )
            )
        );
        $this->assertTrue(
            str_contains(
                $outputConfigurationFileContents,
                $this->mockOutputFilteringPerfomedByNewOutputComponentCommand($this->currentOutput),
            ),
            'The configured output does not match the specified --output even though ' .
            '--output was specified without the --output-source-file flag.' .
            PHP_EOL .
            'The expected output was:' . $this->currentOutput .
            PHP_EOL .
            'The configuration file\'s content was:' . $this->currentOutput .
            PHP_EOL
        );
    }

    public function testRunSetDynamicOutputComponentContainerTo_APPNAMEDynamicOutput(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output',], __METHOD__)
        );
        $dynamicOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(
            file_get_contents($dynamicOutputComponentConfigurationFilePath)
        );
        $expectedContainer = $this->currentTestAppName . 'DynamicOutput';
        $this->assertTrue(
            str_contains($dynamicOutputComponentConfigurationFileContents, $expectedContainer),
            'The expected container was found in the DynamicOutputComponent\'s configuration ' .
            'file, the expected container was: ' . $expectedContainer
        );
    }

    public function testRunSetOutputComponentContainerTo_APPNAMEOutput(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app','--name','--output','--static'], __METHOD__)
        );
        $outputComponentConfigurationFilePath = $this->determineConfigurationFilePath('OutputComponents', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $outputComponentConfigurationFileContents = strval(file_get_contents($outputComponentConfigurationFilePath));
        $expectedContainer = $this->currentTestAppName . 'Output';
        $this->assertTrue(
            str_contains($outputComponentConfigurationFileContents, $expectedContainer),
            'The expected container was found in the DynamicOutputComponent\'s configuration ' .
            'file, the expected container was: ' . $expectedContainer
        );
    }

    public function testRunSetsOutputComponentPositionToSpecifiedOPosition(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--static',
                    '--o-position',
                ],
                __METHOD__
            )
        );
        $dynamicOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath('OutputComponents', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(
            file_get_contents($dynamicOutputComponentConfigurationFilePath)
        );
        $this->assertTrue(
            str_contains($dynamicOutputComponentConfigurationFileContents, $this->currentOPosition),
            'The expected position was found in the DynamicOutputComponent\'s configuration ' .
            PHP_EOL .
            'file, the expected position was: ' . $this->currentOPosition .
            PHP_EOL .
            'The configuration file\'s content was:' .
            PHP_EOL .
            $dynamicOutputComponentConfigurationFileContents .
            PHP_EOL
        );
    }

    public function testRunSetsDynamicOutputComponentPositionToSpecifiedOPosition(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--o-position',
                ],
                __METHOD__
            )
        );
        $dynamicOutputComponentConfigurationFilePath = $this->determineConfigurationFilePath(
            'OutputComponents',
            $preparedArguments
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(
            file_get_contents($dynamicOutputComponentConfigurationFilePath)
        );
        $this->assertTrue(
            str_contains($dynamicOutputComponentConfigurationFileContents, $this->currentOPosition),
            'The expected position was found in the DynamicOutputComponent\'s configuration ' .
            'file, the expected position was: ' . $this->currentOPosition
        );
    }

    public function testRunConfiguresARequestForEachOfTheSpcifiedRelativeUrls(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--relative-urls' ], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        foreach($this->currentRelativeUrls as $key => $relativeUrl) {
            $expectedRequestConfigurationFilePath = str_replace(
                ['.php'],
                [strval($key) . '.php'],
                $this->determineConfigurationFilePath('Requests', $preparedArguments)
            );
            $this->assertTrue(
                file_exists($expectedRequestConfigurationFilePath),
                'ddms --configure-app-output MUST configure a Request for the output ' .
                'if the --static flag is not specified. A Request configuration file ' .
                'should have been created at' .  $expectedRequestConfigurationFilePath
            );
            $this->assertTrue(
                str_contains(strval(file_get_contents($expectedRequestConfigurationFilePath)),
                'appComponentsFactory->buildRequest'), 'Request configuration file ' .
                'was created at ' . $expectedRequestConfigurationFilePath . ' but ' .
                'it does not define a call to appComponentsFactory->buildRequest'
            );
            $this->assertTrue(
                str_contains(strval(file_get_contents($expectedRequestConfigurationFilePath)), $relativeUrl),
                'Request configuration file does not contain the specified relative ' .
                'url, expected url was: ' . $relativeUrl . ' Configuration file path' .
                ': '  . $expectedRequestConfigurationFilePath
            );
        }
    }

    public function testRunSetsRequestContainersTo_APPNAMERequests(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--relative-urls'], __METHOD__)
        );
        $expectedAppDirectoryPath = $preparedArguments['flags']['ddms-apps-directory-path'][0] .
                DIRECTORY_SEPARATOR . $this->currentTestAppName;
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $expectedContainer = $this->currentTestAppName . 'Requests';
        foreach($this->currentRelativeUrls as $key => $relativeUrl) {
            $expectedRequestConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR .
                'Requests' . DIRECTORY_SEPARATOR . $this->currentOutputName . strval($key). '.php';
            $requestConfigurationFileContent = strval(file_get_contents($expectedRequestConfigurationFilePath));
            $this->assertTrue(
                str_contains($requestConfigurationFileContent, $expectedContainer),
                'The expected container was not found in the Request\'s configuration ' .
                'file, the expected container was: ' . $expectedContainer . ' and the Request\'s ' .
                'configuration file was: ' . $requestConfigurationFileContent
            );
        }
    }

    public function testRunConfiguresDefaultRequestForOutputIfNoRelativeUrlsAreSpcecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', ], __METHOD__)
        );
        $expectedDefaultRelativeUrl = 'index.php?request=' . $this->currentOutputName;
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $expectedRequestConfigurationFilePath = $this->determineConfigurationFilePath('Requests', $preparedArguments);
        $this->assertTrue(
            file_exists($expectedRequestConfigurationFilePath),
            'ddms --configure-app-output MUST configure a Request for the output ' .
            'even if no relative urls are specified. A Request configuration file should have ' .
            'been created at '. $expectedRequestConfigurationFilePath
        );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($expectedRequestConfigurationFilePath)),
                'appComponentsFactory->buildRequest'
            ),
            'Request configuration file was created at ' . $expectedRequestConfigurationFilePath .
            ' but it does not define a call to appComponentsFactory->buildRequest'
        );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($expectedRequestConfigurationFilePath)),
                $expectedDefaultRelativeUrl
            ),
            'Request configuration file does not contain the specified relative url' .
            ', expected url was: ' . $expectedDefaultRelativeUrl . ' Configuration ' .
            'file path: '  . $expectedRequestConfigurationFilePath
        );
    }

    public function testRunConfiguresDefaultRequestForOutputEvenIfRelativeUrlsAreSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--relative-urls'], __METHOD__)
        );
        $relativeUrl = 'index.php?request=' . $this->currentOutputName;
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $expectedRequestConfigurationFilePath = $this->determineConfigurationFilePath('Requests', $preparedArguments);
        $this->assertTrue(
            file_exists($expectedRequestConfigurationFilePath),
            'ddms --configure-app-output MUST configure a Request for the output ' .
            'even if no relative urls are specified. A Request configuration file ' .
            'should have been created at' . $expectedRequestConfigurationFilePath
        );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($expectedRequestConfigurationFilePath)),
                'appComponentsFactory->buildRequest'
            ),
            'Request configuration file was created at ' . $expectedRequestConfigurationFilePath .
            ' but it does not define a call to appComponentsFactory->buildRequest'
        );
        $this->assertTrue(
            str_contains(strval(file_get_contents($expectedRequestConfigurationFilePath)), $relativeUrl),
            'Request configuration file does not contain the specified relative url' .
            ', expected url was: ' . $relativeUrl . ' Configuration file path: '  .
            $expectedRequestConfigurationFilePath
        );
    }

    /**
     * @param array <int, string> $flagNames
     * @return array <int, string>
     */
    private function getTestArgsForSpecifiedFlags(array  $flagNames, string $testName, bool $badSourceFilePath = false): array
    {
        $this->currentTestAppName = $this->getRandomAppName();
        $this->currentOutputName = $this->convertToAlphanumeric($this->currentTestAppName . $testName);
        $this->currentOutput = $this->currentTestAppName . $testName . ' output.';
        $this->currentOutputSourceFile = (
            $badSourceFilePath === true
            ? $this->currentTestAppName . strval(rand(PHP_INT_MIN, PHP_INT_MAX))
            : strval(realpath(__FILE__))
        );
        $this->currentOPosition = strval(rand(-100, 100));
        $this->currentRPosition = strval(rand(-100, 100));
        $this->currentRelativeUrls = [
            'index.php',
            'index.php?request=' . $this->currentOutputName,
            'index.php?foo=bar' . strval(rand(0, PHP_INT_MAX))
        ];
        return [
            (in_array('--static', $flagNames) ? '--static' : ''),
            (in_array('--global', $flagNames) ? '--global' : ''),
            (in_array('--for-app', $flagNames) ? '--for-app' : ''),
            (in_array('--for-app', $flagNames) ? $this->currentTestAppName : ''),
            (in_array('--name', $flagNames) ? '--name' : ''),
            (in_array('--name', $flagNames) ? $this->currentOutputName : ''),
            (in_array('--output', $flagNames) ? '--output' : ''),
            (in_array('--output', $flagNames) ? $this->currentOutput : ''),
            (in_array('--output-source-file', $flagNames) ? '--output-source-file' : ''),
            (in_array('--output-source-file', $flagNames) ? $this->currentOutputSourceFile : ''),
            (in_array('--o-position', $flagNames) ? '--o-position' : ''),
            (in_array('--o-position', $flagNames) ? $this->currentOPosition : ''),
            (in_array('--r-position', $flagNames) ? '--r-position' : ''),
            (in_array('--r-position', $flagNames) ? $this->currentRPosition : ''),
            (in_array('--relative-urls', $flagNames) ? '--relative-urls' : ''),
            (in_array('--relative-urls', $flagNames) ? ($this->currentRelativeUrls[0] ?? '') : ''),
            (in_array('--relative-urls', $flagNames) ? ($this->currentRelativeUrls[1] ?? '') : ''),
            (in_array('--relative-urls', $flagNames) ? ($this->currentRelativeUrls[2] ?? '') : ''),
        ];
    }

    private function convertToAlphanumeric(string $string): string
    {
        return strval(preg_replace("/[^a-zA-Z0-9]+/", "", $string));
    }


    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineCurrentAppsDirectoryPath(array $preparedArguments): string
    {
        return $preparedArguments['flags']['ddms-apps-directory-path'][0] .
            DIRECTORY_SEPARATOR . $this->currentTestAppName;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineConfigurationFilePath(string $configurationDirectoryName, array $preparedArguments): string
    {
        return $this->determineCurrentAppsDirectoryPath($preparedArguments) .
            DIRECTORY_SEPARATOR . $configurationDirectoryName . DIRECTORY_SEPARATOR . $this->currentOutputName . '.php';
    }

    public function testRunConfiguresAResponseForTheOutputIfGlobalFlagIsNotSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output'], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->determineConfigurationFilePath('Responses', $preparedArguments)),
            'ddms --configure-app-output MUST configure a Response for the ' .
            'output if the --static flag is not specified. A Response configuration file ' .
            'should have been created at' . $this->determineConfigurationFilePath('Responses', $preparedArguments)
         );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($this->determineConfigurationFilePath('Responses', $preparedArguments))),
                'appComponentsFactory->buildResponse'
            ),
            'Response configuration file was created at ' .
            $this->determineConfigurationFilePath('Responses', $preparedArguments) .
            ' but it does not define a call to appComponentsFactory->buildResponse'
        );
    }

    public function testRunConfiguresAGlobalResponseForTheOutputIfGlobalFlagIsSpecified(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(['--for-app', '--name', '--output', '--global'], __METHOD__)
        );
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->determineConfigurationFilePath('Responses', $preparedArguments)),
            'ddms --configure-app-output MUST configure a GlobalResponse for the ' .
            'output if the --static flag is not specified. A GlobalResponse configuration file ' .
            'should have been created at' . $this->determineConfigurationFilePath('Responses', $preparedArguments)
         );
        $this->assertTrue(
            str_contains(
                strval(file_get_contents($this->determineConfigurationFilePath('Responses', $preparedArguments))),
                'appComponentsFactory->buildGlobalResponse'
            ),
            'GlobalResponse configuration file was created at ' .
            $this->determineConfigurationFilePath('Responses', $preparedArguments) .
            ' but it does not define a call to appComponentsFactory->buildGlobalResponse'
        );
    }
###

    public function testRunSetsResponsePositionToRPosition(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--static',
                    '--r-position',
                ],
                __METHOD__
            )
        );
        $responseConfigurationFilePath = $this->determineConfigurationFilePath('Responses', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $responseConfigurationFileContents = strval(
            file_get_contents($responseConfigurationFilePath)
        );
        $this->assertTrue(
            str_contains($responseConfigurationFileContents, $this->currentRPosition),
            'The expected position was found in the Response\'s configuration ' .
            PHP_EOL .
            'file, the expected position was: ' . $this->currentRPosition .
            PHP_EOL .
            'The configuration file\'s content was:' .
            PHP_EOL .
            $responseConfigurationFileContents .
            PHP_EOL
        );
    }

    public function testRunSetsGlobalResponsePositionToRPosition(): void
    {
        $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--r-position',
                    '--global'
                ],
                __METHOD__
            )
        );
        $responseConfigurationFilePath = $this->determineConfigurationFilePath('Responses', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $responseConfigurationFileContents = strval(
            file_get_contents($responseConfigurationFilePath)
        );
        $this->assertTrue(
            str_contains($responseConfigurationFileContents, $this->currentRPosition),
            'The expected position was found in the GlobalResponse\'s configuration ' .
            PHP_EOL .
            'file, the expected position was: ' . $this->currentRPosition .
            PHP_EOL .
            'The configuration file\'s content was:' .
            PHP_EOL .
            $responseConfigurationFileContents .
            PHP_EOL
        );
    }

    public function testRunAssignsDynamicOutputComponentToAppropriateResponseIfGlobalFlagIsNotSpecified(): void
    {
         $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--r-position',
                ],
                __METHOD__
            )
        );
        $responseConfigurationFilePath = $this->determineConfigurationFilePath('Responses', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $responseConfigurationFileContents = strval(
            file_get_contents($responseConfigurationFilePath)
        );
        $configContents = str_replace([' ', '\n', '\r', PHP_EOL], '', $responseConfigurationFileContents);
        $expectedAssignment = $this->currentOutputName . '\',DynamicOutputComponent::class';
        $this->assertTrue(
            str_contains($configContents, $expectedAssignment),
            'The DynamicOutputComponent configured for the output was not assigned in the appropriate Response\'s configuration ' .
            PHP_EOL .
            'file, the expected DynamicOutputComponent to be assigned was: ' . $this->currentOutputName .
            PHP_EOL .
            'The configuration file\'s content was:' .
            PHP_EOL .
            $responseConfigurationFileContents .
            PHP_EOL
        );
    }

    public function testRunAssignsOutputComponentToAppropriateResponseIfGlobalFlagIsNotSpecifiedAndStaticFlagIsSpecified(): void
    {
         $preparedArguments = $this->configureAppOutput()->prepareArguments(
            $this->getTestArgsForSpecifiedFlags(
                [
                    '--for-app',
                    '--name',
                    '--output',
                    '--static',
                    '--r-position',
                ],
                __METHOD__
            )
        );
        $responseConfigurationFilePath = $this->determineConfigurationFilePath('Responses', $preparedArguments);
        $this->configureAppOutput()->run($this->userInterface(), $preparedArguments);
        $responseConfigurationFileContents = strval(
            file_get_contents($responseConfigurationFilePath)
        );
        $configContents = str_replace([' ', '\n', '\r', PHP_EOL], '', $responseConfigurationFileContents);
        $expectedAssignment = $this->currentOutputName . '\',OutputComponent::class';
        $this->assertTrue(
            str_contains($configContents, $expectedAssignment),
            'The OutputComponent configured for the output was not assigned in the appropriate Response\'s configuration ' .
            PHP_EOL .
            'file, the expected OutputComponent to be assigned was: ' . $this->currentOutputName .
            PHP_EOL .
            'The configuration file\'s content was:' .
            PHP_EOL .
            $responseConfigurationFileContents .
            PHP_EOL
        );
    }
}

