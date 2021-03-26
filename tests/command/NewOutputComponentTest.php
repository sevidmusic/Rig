<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewApp;
use ddms\classes\command\NewOutputComponent;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class NewOutputComponentTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIf_name_IsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--for-app', $appName, '--output', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIf_for_app_IsNotSpecified(): void
    {
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--name', 'Foo', '--output', 'Foo']));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--name', 'Foo', '--output', 'Foo', '--for-app', 'Baz' . strval(rand(10000,9999))]));
    }

    public function testRunCreatesNewOutputComponentForSpecifiedApp(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(file_exists($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsContainerTo_OutputComponents_IfContainerIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), $this->getNewOutputComponentContent($preparedArguments));
    }

    public function testRunSetsContainerTo_OutputComponents_IfContainerIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--container', '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedContainerIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--container', 'FooBarBaz*#$%*', '--output', 'Foo']));
    }

    public function testRunSetsContainerToSpecifiedContainerIfSpecifiedContainerIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--container', 'ValidContainerName', '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsPositionTo_0_IfPositionIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), $this->getNewOutputComponentContent($preparedArguments));
    }

    public function testRunSetsPositionTo_0_IfPositionIsSpecifiedWithNoValue(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--position', '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPositionIsNotNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--position', 'FooBarBaz', '--output', 'Foo']));
    }

    public function testRunSetsPositionToSpecifiedPositionIfSpecifiedPositionIsNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--position', '420', '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfOutputComponentAlreadyExists(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExpceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName . '!@#$%^&*()_+=-\][\';"\\,.', '--for-app', $appName, '--output', 'Foo']);
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunSetsNameToSpecifiedNameIfSpecifiedNameIsAlphaNumeric(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName, '--output', 'Foo']);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunSetsOutputToSpecifiedOutput(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentOutput = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $preparedArguments = $newOutputComponent->prepareArguments(['--name', $OutputComponentOutput, '--for-app', $appName, '--output', 'Foo \' Bar \' Baz !@#$%^ %^&&** ()_+ {}|' . "\' <- escaped single quoteFoo Bar " . strval(rand(100000, PHP_INT_MAX))]);
        $newOutputComponent->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals($this->determineExpectedOutputComponentPhpContent($preparedArguments), file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    public function testRunThrowsRuntimeExceptionIfOutputIsNotSpecified(): void
    {
        $appName = $this->createTestAppReturnName();
        $OutputComponentName = $appName . 'OutputComponent';
        $newOutputComponent = new NewOutputComponent();
        $this->expectException(RuntimeException::class);
        $newOutputComponent->run(new CommandLineUI(), $newOutputComponent->prepareArguments(['--name', $OutputComponentName, '--for-app', $appName]));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getNewOutputComponentContent($preparedArguments): string
    {
        return strval(file_get_contents($this->expectedOutputComponentPath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedOutputComponentPath(array $preparedArguments): string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $preparedArguments['flags']['name'][0] . '.php';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineExpectedOutputComponentPhpContent(array $preparedArguments): string
    {
        var_dump('OUTPUT', strval(implode(' ', ($preparedArguments['flags']['output'] ?? []))));
        return str_replace(
            [
                '_NAME_',
                '_POSITION_',
                '_CONTAINER_',
                '_OUTPUT_',
            ],
            [
                $preparedArguments['flags']['name'][0],
                ($preparedArguments['flags']['position'][0] ?? '0'),
                ($preparedArguments['flags']['container'][0] ?? 'OutputComponents'),
                str_replace(['\\', "'"], ['\\\\', "\'"], strval(implode(' ', $preparedArguments['flags']['output']))),
            ],
            strval(file_get_contents($this->expectedTemplateFilePath()))
        );
    }

    private function expectedTemplateFilePath(): string
    {
        return str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'OutputComponent.php';
    }

}
