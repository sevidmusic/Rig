<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\NewAppPackage;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class NewAppPackageTest extends TestCase
{

    use TestsCreateApps;

    public function testRunThrowsRuntimeExceptionIfNameIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments([]);
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedNameIsNotAlphaNumeric(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                'Package_Name'
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedDomainIsNotAValidDomain(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                'PackageName',
                '--domain',
                'invalid_domain'
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIsNotAvailable(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                'HelloWorld',
                '--path',
                $this->getUnavailablePath()
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    private function getUI(): UserInterface {
        return new CommandLineUI();
    }

    private function getUnavailablePath(): string {
        return __DIR__;
    }

    private function getAvailablePath(string $name): string {
        return strval(realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__))) . DIRECTORY_SEPARATOR . $name;
    }
}
