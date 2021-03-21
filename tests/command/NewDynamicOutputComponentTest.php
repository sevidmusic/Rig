<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\command\NewDynamicOutputComponent;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewDynamicOutputComponentTest extends TestCase
{

    use TestsCreateApps;

    public function testTest(): void
    {
        $this->assertTrue(true);
    }

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments([]));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewDynamicOutputComponentForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsContainerTo_DynamicOutputComponents_IfContainerIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunSetsContainerTo_DynamicOutputComponents_IfContainerIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedContainerIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container', 'FooBarBaz*#$%*']));
    }

    public function testRunSetsContainerToSpecifiedContainerIfSpecifiedContainerIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--container', 'ValidContainerName']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunSetsPositionTo_0_IfPositionIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPositionIsNotNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position', 'FooBarBaz']));
    }

    public function testRunSetsPositionToSpecifiedPositionIfSpecifiedPositionIsNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--position', '420']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfDynamicOutputComponentAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName . '!@#$%^&*()_+=-\][\';"\\,.', '--for-app', $appName]);
        $this->expectException(RuntimeException::class);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetsNameToSpecifiedNameIfSpecifiedNameIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSets_for_app_As_app_name(): void
    {
        $appForApp = $this->createTestAppReturnName();
        $dynamicOutputComponentForApp = $appForApp . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentForApp, '--for-app', $appForApp]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsDynamicOutputFileTo_name_withExtension_php_IfFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunCreatesDynamicOutputFileNamed_name_WithExtension_php_InAppsDynamicOutputDirectoryIfFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $dynamicOutputComponentName . '.php'
            )
        );
    }

    public function testRunCreatesDynamicOutputFileNamed_name_WithExtension_php_InSharedDynamicOutputDirectoryIfSharedFlagsIsPresentAndFileNameIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--shared']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $dynamicOutputComponentName . '.php'
            )
        );
    }

    public function testRunSetsDynamicOutputFileTo_file_name_IfFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', 'FooBar' . strval(rand(420, 4200))]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedDynamicOutputComponentPhpContent($preparedArguments), $this->getNewDynamicOutputComponentContent($preparedArguments));
    }

    public function testRunCreatesDynamicOutputFileNamed_file_name_InAppsDynamicOutputDirectoryIfFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName]);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName
            )
        );
    }

    public function testRunCreatesDynamicOutputFileNamed_file_name_InSharedDynamicOutputDirectoryIfSharedFlagIsPresentAndFileNameIsSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName, '--shared']);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists(
                $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName
            )
        );
    }

    public function testRunDoesNotCreateDynamicOutputFileInAppsDynamicOutputDirectoryIfDynamicOutputFileAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $fileName = 'FooBarBaz' . strval(rand(420, 4200)) . '.html';
        $dynamicOutputComponentName = $appName . 'DynamicOutputComponent';
        $newDynamicOutputComponent = new NewDynamicOutputComponent();
        $preparedArguments = $newDynamicOutputComponent->prepareArguments(['--name', $dynamicOutputComponentName, '--for-app', $appName, '--file-name', $fileName]);
        $expectedFilePath = $this->expectedDynamicOutputFileDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $fileName;
        $expectedContent = 'Expected Content' . strval(rand(420, PHP_INT_MAX));
        file_put_contents($expectedFilePath, $expectedContent);
        $newDynamicOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($expectedFilePath));
        $this->assertEquals($expectedContent, file_get_contents($expectedFilePath));
    }

/**
testRunDoesNotCreateDynamicOutputFileInSharedDynamicOutputDirectoryIfSharedFlagIsPresentAndDynamicOutputFileAlreadyExists()

**/
    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedDynamicOutputFileDirectoryPath(array $preparedArguments): string
    {
        return (
            isset($preparedArguments['flags']['shared'])
            ? $this->expectedSharedDynamicOutputDirectoryPath($preparedArguments)
            : $this->expectedAppDynamicOutputDirectoryPath($preparedArguments)
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedAppDynamicOutputDirectoryPath(array $preparedArguments): string
    {
        return $this->expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'DynamicOutput';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedSharedDynamicOutputDirectoryPath(array $preparedArguments): string
    {
        return str_replace('Apps' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['for-app'][0], 'SharedDynamicOutput', $this->expectedAppDirectoryPath($preparedArguments));
    }


    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewDynamicOutputComponentContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedDynamicOutputComponentPath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedDynamicOutputComponentPath(array $preparedArguments): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['name'][0] . '.php';
    }

    private function createTestAppReturnName(): string
    {
        $appName = self::getRandomAppName();
        $newApp = new NewApp();
        $newAppPreparedArguments = $newApp->prepareArguments(['--name', $appName]);
        $newApp->run(new CommandLineUI(), $newAppPreparedArguments);
        return $appName;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineExpectedDynamicOutputComponentPhpContent(array $preparedArguments): string
    {
        return str_replace(
            [
                '_NAME_',
                '_POSITION_',
                '_CONTAINER_',
                '_FOR_APP_',
                '_DYNAMIC_OUTPUT_FILE_',
            ],
            [
                $preparedArguments['flags']['name'][0],
                ($preparedArguments['flags']['position'][0] ?? '0'),
                ($preparedArguments['flags']['container'][0] ?? 'DynamicOutputComponents'),
                $preparedArguments['flags']['for-app'][0],
                ($preparedArguments['flags']['file-name'][0] ?? $preparedArguments['flags']['name'][0] . '.php'),
            ],
            strval(file_get_contents($this->expectedTemplateFilePath()))
        );
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'DynamicOutputComponent.php';
    }

}
