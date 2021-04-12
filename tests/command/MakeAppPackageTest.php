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
                $this->pathToInValidAppPackage()
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

    private function pathToInValidAppPackage(): string
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
