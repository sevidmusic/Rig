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

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIsNotAvailableToCreateNewAppPackage(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                # Specifying current working directory as the target directory from
                # the context of this script will target the ddms directory, which
                # means the target directory in the context of this script will
                # contain a directory named ddms, i.e. path/to/ddms/ddms, so the
                # name ddms can be used for this test since a directory named ddms
                # will exist so an App Package named ddms should not be created
                # and run() should throw a RuntimeException.
                'ddms',
                '--path',
                realpath(strval(getcwd()))
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunThrowsRuntimeExceptionIfPathIsNotSpecifiedAndExpectedPathToNewAppPackageInCurrentWorkingDirectoryIsNotAvailable(): void
    {
        $this->expectException(RuntimeException::class);
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                # Not specifying the --path will result in ddms using the current
                # working directory as the target directory, which, from the
                # context of this script will be the ddms directory. This means
                # the target directory in the context of this script will contain
                # a directory named ddms, i.e. path/to/ddms/ddms, so the name ddms
                # can be used for this test since a directory named ddms will exist
                # so an App Package named ddms should not be created and run()
                # should throw a RuntimeException.
                'ddms',
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunCreatesNewAppPackageDirectoryInCurrentWorkingDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = 'NewTestAppPackage' . strval(rand(420, 4200));
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName)),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName)
        );
        if(file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName))) {
            self::removeDirectory($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName));
        }
    }

    private function expectedNewAppPackagePathIfPathIsNotSpecified(string $name): string {
        return strval(realpath(strval(getcwd()))) . DIRECTORY_SEPARATOR . $name;
    }

    private function expectedNewAppPackagePathIfPathIsSpecified(string $name, string $path): string {
        return strval(realpath($path)) . DIRECTORY_SEPARATOR . $name;
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

    private static function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $directoryListing = scandir($dir);
            $contents = (is_array($directoryListing) ? $directoryListing : []);
            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
                    (is_dir($itemPath) === true && is_link($itemPath) === false)
                        ? self::removeDirectory($itemPath)
                        : unlink($itemPath);
                }
            }
            rmdir($dir);
        }
    }

}
