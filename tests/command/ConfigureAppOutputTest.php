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

    private function getConfigureAppOutput(): ConfigureAppOutput
    {
        if(!isset($this->configureAppOutput)) {
            $this->configureAppOutput = new ConfigureAppOutput();
        }
        return $this->configureAppOutput;
    }

    private function getUserInterface(): UserInterface
    {
        if(!isset($this->ui)) {
            $this->ui = new CommandLineUI();
        }
        return $this->ui;
    }

    public function testRunThrowsRuntimeExceptionIfForAppIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(['--configure-app-output'])
        );
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(
                ['--for-app', $appName]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfNitherOutputOrOutputSourceFileAreSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunThrowsEIfOutputNotSpecified';
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run(
            $this->getUserInterface(),
            $this->getConfigureAppOutput()->prepareArguments(
                    ['--for-app', $appName, '--name', $outputName]
            )
        );
    }

    public function testRunCreatesAppSpecifiedByForAppIfAppDoesNotAlreadyExist(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputCreatesAppIfAppDoesNotExist';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedCssDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'css';
        $expectedJsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'js';
        $expectedDynamicOutputDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput';
        $expectedOutputComponentsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents';
        $expectedResponsesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Responses';
        $expectedRequestsDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests';
        $expectedResourcesDirectoryPath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'resources';
        $expectedComponentsPhpFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Components.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->assertTrue(is_dir($expectedAppDirectoryPath), "ddms --configure-app-output MUST create the App specified by the --for-app flag if the App does not already exist. An App should have been created at $expectedAppDirectoryPath");
        $this->assertTrue(is_dir($expectedCssDirectoryPath), "ddms --configure-app-output MUST create the App's css directory if the App does not already exist. An css directory for the App should have been created at $expectedCssDirectoryPath");
        $this->assertTrue(is_dir($expectedJsDirectoryPath), "ddms --configure-app-output MUST create the App's js directory if the App does not already exist. An js directory for the App should have been created at $expectedJsDirectoryPath");
        $this->assertTrue(is_dir($expectedDynamicOutputDirectoryPath), "ddms --configure-app-output MUST create the App's DynamicOutput directory if the App does not already exist. An DynamicOutput directory for the App should have been created at $expectedDynamicOutputDirectoryPath");
        $this->assertTrue(is_dir($expectedOutputComponentsDirectoryPath), "ddms --configure-app-output MUST create the App's OutputComponents directory if the App does not already exist. An OutputComponents directory for the App should have been created at $expectedOutputComponentsDirectoryPath");
        $this->assertTrue(is_dir($expectedResponsesDirectoryPath), "ddms --configure-app-output MUST create the App's Responses directory if the App does not already exist. An Responses directory for the App should have been created at $expectedResponsesDirectoryPath");
        $this->assertTrue(is_dir($expectedRequestsDirectoryPath), "ddms --configure-app-output MUST create the App's Requests directory if the App does not already exist. An Requests directory for the App should have been created at $expectedRequestsDirectoryPath");
        $this->assertTrue(is_dir($expectedResourcesDirectoryPath), "ddms --configure-app-output MUST create the App's resources directory if the App does not already exist. An resources directory for the App should have been created at $expectedResourcesDirectoryPath");
        $this->assertTrue(file_exists($expectedComponentsPhpFilePath), "ddms --configure-app-output MUST create the App's Components.php directory if the App does not already exist. An Components.php directory for the App should have been created at $expectedComponentsPhpFilePath");
    }

    public function testRunConfiguresADynamicOutputComponentForTheOutputIfStaticFlagsIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedDynamicOutputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->assertTrue(file_exists($expectedDynamicOutputComponentConfigurationFilePath), "ddms --configure-app-output MUST configure a DynamicOutputComponent for the output if the --static flag is not specified. A DynamicOutputComponent configuration file should have been created at $expectedDynamicOutputComponentConfigurationFilePath");
        $this->assertTrue(str_contains(strval(file_get_contents($expectedDynamicOutputComponentConfigurationFilePath)), 'appComponentsFactory->buildDynamicOutputComponent'), 'DynamicOutputComponent configuration file was created at ' . $expectedDynamicOutputComponentConfigurationFilePath . ' but it does not define a call to appComponentsFactory->buildDynamicOutputComponent');
    }

    public function testRunThrowsRuntimeExceptionIfNameIsNotUnique(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileDoesNotExist(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $badFilePath = __DIR__ . DIRECTORY_SEPARATOR . strval(rand(PHP_INT_MIN, PHP_INT_MAX));
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $badFilePath
            ]
        );
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedOutputSourceFileIsNotAFile(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestOutputComponent';
        $badFilePath = __DIR__;
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $badFilePath
            ]
        );
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
    }

    public function testRunConfiguresAnOutputComponentForTheOutputIfStaticFlagsIsSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsOutputComponentIfStaticIspecified';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--static'
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedOutputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $this->assertTrue(file_exists($expectedOutputComponentConfigurationFilePath), "ddms --configure-app-output MUST configure a OutputComponent for the output if the --static flag is not specified. A OutputComponent configuration file should have been created at $expectedOutputComponentConfigurationFilePath");
        $this->assertTrue(str_contains(strval(file_get_contents($expectedOutputComponentConfigurationFilePath)), 'appComponentsFactory->buildOutputComponent'), 'OutputComponent configuration file was created at ' . $expectedOutputComponentConfigurationFilePath . ' but it does not define a call to appComponentsFactory->buildOutputComponent');
    }

    public function testRunSetsDynamicOutputFileContentsToMatchContentsOfSpecifiedOutputSourceFileIfOutputSourceFileFlagIsSpecifiedAndStaticFlagIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsDynamicOutputFileContentToSourceFileContent';
        $sourceFilePath = strval(realpath(__FILE__));
        $expectedOutput = strval(file_get_contents($sourceFilePath));
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $sourceFilePath
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedDynamicOutputFilePath = strval(realpath($expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput' . DIRECTORY_SEPARATOR . $outputName . '.php'));
        $dynamicOutputFileContents = strval(file_get_contents($expectedDynamicOutputFilePath));
        $this->assertEquals($expectedOutput, $dynamicOutputFileContents);
    }

    public function testRunSetsDynamicOutputFileContentsToMatchSpecifiedOutputIfOutputSourceFileFlagIsNotSpecifiedAndStaticFlagIsNotSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsDynamicOutputFileConentToSpecifiedOutput';
        $expectedOutput = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $expectedOutput
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedDynamicOutputFilePath = strval(realpath($expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'DynamicOutput' . DIRECTORY_SEPARATOR . $outputName . '.php'));
        $dynamicOutputFileContents = strval(file_get_contents($expectedDynamicOutputFilePath));
        $this->assertEquals($expectedOutput, $dynamicOutputFileContents);
    }

    /**
     * This method mocks the filtering performed by the NewOutputComponent command.
     */
    private function mockOutputFilteringPerfomedByNewOutputComponentCommand(string $output): string
    {
        return str_replace(["\\","'"], ["\\\\", "\'"], $output);
    }

    public function testRunConfiguresOutputToMatchContentsOfSpecifiedOutputSourceFileIfOutputSourceFileFlagIsSpecifiedAndStaticFlagIsAlsoSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsOutputFileContentToSourceFileContentIfStaticSpecified';
        $sourceFilePath = strval(realpath(__FILE__));
        $expectedOutput = $this->mockOutputFilteringPerfomedByNewOutputComponentCommand(strval(file_get_contents($sourceFilePath)));
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output-source-file',
                $sourceFilePath,
                '--static'
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedOutputComponentConfigurationFilePath = strval(realpath($expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php'));
        $outputComponentConfigurationFileContents = strval(file_get_contents($expectedOutputComponentConfigurationFilePath));
        $this->assertTrue(str_contains($outputComponentConfigurationFileContents, $expectedOutput), 'The configured output does not match the contents of the specified --output-source-file even though both the --output-source-file and --static flags were specified. The expected output was:' . $expectedOutput);
    }


    public function testRunConfiguresOutputToMatchSpecifiedOutputIfOutputSourceFileFlagIsNotSpecifiedAndStaticFlagIsSpecified(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsDynamicOutputFileConentToSpecifiedOutput';
        $expectedOutput = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $expectedOutput,
                '--static'
            ]
        );
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $expectedOutputConfigurationFilePath = strval(realpath($expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php'));
        $outputConfigurationFileContents = strval(file_get_contents($expectedOutputConfigurationFilePath));
        $this->assertTrue(str_contains($outputConfigurationFileContents, $expectedOutput), 'The configured output does not match the specified --output even though --output was specified without the --output-source-file flag. The expected output was:' . $expectedOutput);
    }

    public function testRunSetDynamicOutputComponentContainerTo_APPNAMEDynamicOutput(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $dynamicOutputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(file_get_contents($dynamicOutputComponentConfigurationFilePath));
        $expectedContainer = "${appName}DynamicOutput";
        $this->assertTrue(str_contains($dynamicOutputComponentConfigurationFileContents, $expectedContainer), 'The expected container was found in the DynamicOutputComponent\'s configuration file, the expected container was: ' . $expectedContainer);
    }

    public function testRunSetOutputComponentContainerTo_APPNAMEOutput(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--static'
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $outputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $outputComponentConfigurationFileContents = strval(file_get_contents($outputComponentConfigurationFilePath));
        $expectedContainer = "${appName}Output";
        $this->assertTrue(str_contains($outputComponentConfigurationFileContents, $expectedContainer), 'The expected container was found in the DynamicOutputComponent\'s configuration file, the expected container was: ' . $expectedContainer);
    }

    public function testRunSetsOutputComponentPositionToSpecifiedOPosition(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $expectedPosition = '4.25';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--static',
                '--o-position',
                $expectedPosition
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $dynamicOutputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(file_get_contents($dynamicOutputComponentConfigurationFilePath));
        $this->assertTrue(str_contains($dynamicOutputComponentConfigurationFileContents, $expectedPosition), 'The expected position was found in the DynamicOutputComponent\'s configuration file, the expected position was: ' . $expectedPosition);
    }

    public function testRunSetsDynamicOutputComponentPositionToSpecifiedOPosition(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsDynamicOutputComponentIfStaticNotSpecified';
        $output = $outputName . ' output';
        $expectedPosition = '4.25';
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--o-position',
                $expectedPosition
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $dynamicOutputComponentConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $outputName . '.php';
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $dynamicOutputComponentConfigurationFileContents = strval(file_get_contents($dynamicOutputComponentConfigurationFilePath));
        $this->assertTrue(str_contains($dynamicOutputComponentConfigurationFileContents, $expectedPosition), 'The expected position was found in the DynamicOutputComponent\'s configuration file, the expected position was: ' . $expectedPosition);
    }

    public function testRunConfiguresARequestForEachOfTheSpcifiedRelativeUrls(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunConfigsRequestForEachSpecifiedRelativeUrl';
        $output = $outputName . ' output';
        $relativeUrls = [
                'index.php?request=' . $outputName,
                'index.php',
                'index.php?page=' . $outputName
        ];
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--relative-urls',
                $relativeUrls[0],
                $relativeUrls[1],
                $relativeUrls[2]
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        foreach($relativeUrls as $key => $relativeUrl) {
            $expectedRequestConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $outputName . strval($key). '.php';
            $this->assertTrue(file_exists($expectedRequestConfigurationFilePath), "ddms --configure-app-output MUST configure a Request for the output if the --static flag is not specified. A Request configuration file should have been created at $expectedRequestConfigurationFilePath");
            $this->assertTrue(str_contains(strval(file_get_contents($expectedRequestConfigurationFilePath)), 'appComponentsFactory->buildRequest'), 'Request configuration file was created at ' . $expectedRequestConfigurationFilePath . ' but it does not define a call to appComponentsFactory->buildRequest');
            $this->assertTrue(str_contains(strval(file_get_contents($expectedRequestConfigurationFilePath)), $relativeUrl), 'Request configuration file does not contain the specified relative url, expected url was: ' . $relativeUrl . ' Configuration file path: '  . $expectedRequestConfigurationFilePath);
        }
    }

    public function testRunSetsRequestContainersTo_APPNAMERequests(): void
    {
        $appName = $this->getRandomAppName();
        $outputName = $appName . 'TestRunSetsRequestContainersToAPPNAMERequests';
        $output = $outputName . ' output';
        $relativeUrls = [
                'index.php?request=' . $outputName,
                'index.php',
                'index.php?page=' . $outputName
        ];
        $prepareArguments = $this->getConfigureAppOutput()->prepareArguments(
            [
                '--for-app',
                $appName,
                '--name',
                $outputName,
                '--output',
                $output,
                '--relative-urls',
                $relativeUrls[0],
                $relativeUrls[1],
                $relativeUrls[2]
            ]
        );
        $expectedAppDirectoryPath = $prepareArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $appName;
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $prepareArguments);
        $expectedContainer = "${appName}Requests";
        foreach($relativeUrls as $key => $relativeUrl) {
            $expectedRequestConfigurationFilePath = $expectedAppDirectoryPath . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $outputName . strval($key). '.php';
            $requestConfigurationFileContent = strval(file_get_contents($expectedRequestConfigurationFilePath));
            $this->assertTrue(str_contains($requestConfigurationFileContent, $expectedContainer), 'The expected container was not found in the Request\'s configuration file, the expected container was: ' . $expectedContainer . ' and the Request\'s configuration file was: ' . $requestConfigurationFileContent);
        }
    }

}

# Need testRunConfiguresDefaultRequestForOutputIfNoRelativeUrlsAreSpcecified()
