<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\MakeAppPackage;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class MakeAppPackageTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIfPathIsNotSpecified(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments([]);
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIsNotValid(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                strval(rand(PHP_INT_MIN, PHP_INT_MAX))
            ]
        );
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathDoesNotInclude_make_sh_File(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                __DIR__
            ]
        );
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIncludesA_make_sh_FileThatDoesNotDefineExactlyOneCallTo_ddms_new_app(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToInvalidAppPackage_InvalidNumberOfCallsToDdmsNewApp()
            ]
        );
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIncludesA_make_sh_FileThatIsNotExecutable(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToAppPackageWhoseMakeShIsNotExecutable()
            ]
        );
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIncludesA_make_sh_WhoseCallToDdmsNewAppUsesWrongName(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToAppPackageWhoseMakeShCallToDdmsNewAppUsesWrongName()
            ]
        );
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfAppWasAlreadyMade(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $this->registerAppName(strval(basename($this->pathToValidAppPackage())));
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToValidAppPackage()
            ]
        );
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
        $this->expectException(RuntimeException::class);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
    }

    public function testRunResultsInANewAppWithExpectedFilesAndDirectoriesPresent(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToValidAppPackage()
            ]
        );
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments)),
            'ddms --make-app-package MUST make an App when run, a new App should have been created at ' . $this->expectedNewAppPath($preparedArguments)
        );
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php'),
            'ddms --make-app-package MUST make an App when run, a Components.php file should have been created for the new App at ' . $this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Components.php'
        );
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'css'),
            'ddms --make-app-package MUST make an App when run, a css directory should have been created for the new App at ' . $this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'css'
        );
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'js'),
            'ddms --make-app-package MUST make an App when run, a js directory should have been created for the new App at ' . $this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'js'
        );
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'resources'),
            'ddms --make-app-package MUST make an App when run, a resources directory should have been created for the new App at ' . $this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'resources'
        );
        $this->assertTrue(
            file_exists($this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'DynamicOutput'),
            'ddms --make-app-package MUST make an App when run, a DynamicOutput directory should have been created for the new App at ' . $this->expectedNewAppPath($preparedArguments) . DIRECTORY_SEPARATOR . 'DynamicOutput'
        );
    }

    protected function tearDown(): void
    {
        $makeAppPackage = new MakeAppPackage();
        foreach(self::$createdApps as $appName) {
            $preparedArguments = $makeAppPackage->prepareArguments(['--name', $appName]);
            self::removeDirectory(self::expectedAppDirectoryPath($preparedArguments));
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedNewAppPath(array $preparedArguments): string {
        $name = strval(basename($preparedArguments['flags']['path'][0]));
        return strval(
            realpath($preparedArguments['flags']['ddms-apps-directory-path'][0])
        ) .
        DIRECTORY_SEPARATOR . $name;
    }

    private function pathToValidAppPackage(): string
    {
        return strval(
            realpath(
                str_replace(
                    'tests' . DIRECTORY_SEPARATOR . 'command',
                    'testAppPackages' . DIRECTORY_SEPARATOR . 'ddmsTestAppPackageValidMakeSh',
                    __DIR__
                )
            )
        );
    }

    private function pathToAppPackageWhoseMakeShCallToDdmsNewAppUsesWrongName(): string
    {
        return strval(
            realpath(
                str_replace(
                    'tests' . DIRECTORY_SEPARATOR . 'command',
                    'testAppPackages' . DIRECTORY_SEPARATOR . 'ddmsTestAppPackageInValidMakeShWrongName',
                    __DIR__
                )
            )
        );
    }

    private function pathToAppPackageWhoseMakeShIsNotExecutable(): string
    {
        return strval(
            realpath(
                str_replace(
                    'tests' . DIRECTORY_SEPARATOR . 'command',
                    'testAppPackages' . DIRECTORY_SEPARATOR . 'ddmsTestAppPackageInValidMakeShNotExecutable',
                    __DIR__
                )
            )
        );
    }

    private function pathToInvalidAppPackage_InvalidNumberOfCallsToDdmsNewApp(): string
    {
        $invalidPackageNames = ['ddmsTestAppPackageInValidMakeShMultiCallNewApp', 'ddmsTestAppPackageInValidMakeShSingleCallNewApp'];
        return strval(
            realpath(
                str_replace(
                    'tests' . DIRECTORY_SEPARATOR . 'command',
                    'testAppPackages' . DIRECTORY_SEPARATOR . $invalidPackageNames[array_rand($invalidPackageNames)],
                    __DIR__
                )
            )
        );
    }
}
