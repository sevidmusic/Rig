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

    public function testRunThrowsRuntimeExceptionIfSpecifiedPathIncludesA_make_sh_WhoseCallToDdmsNewAppUsesANameThatDoesNotMatchTheAppPackagesName(): void
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

    public function testRunReplacesExistingAppFilesThatCorrespondToAppPackageFilesWithAppPackageVersions(): void
    {
        $makeAppPackage = new MakeAppPackage();
        $this->registerAppName(strval(basename($this->pathToValidAppPackage())));
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $this->pathToValidAppPackage()
            ]
        );
        $relativePathToAppPackageFile = DIRECTORY_SEPARATOR . 'DynamicOutput' . DIRECTORY_SEPARATOR . 'DynamicOutputFile.txt';
        $appPackageFilePath = $this->pathToValidAppPackage() . $relativePathToAppPackageFile ;
        $expectedFilePathInApp = $this->expectedNewAppPath($preparedArguments) . $relativePathToAppPackageFile;
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
        $this->createOrModifyFileUsingRandomData($appPackageFilePath);
        $modifiedContent = file_get_contents($appPackageFilePath);
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
        $this->assertEquals(
            $modifiedContent,
            file_get_contents($expectedFilePathInApp),
            'ddms --make-app-package MUST always copy all files and directories defined by the App Package to the new App even if the file or directory was already created for the new App.'
        );
    }

    /**
     * Create or modify a randomfile.txt file in the specified path.
     */
    private function createOrModifyFileUsingRandomData(string $randomFilePath): void
    {
        $originalContent = (file_exists($randomFilePath) ? file_get_contents($randomFilePath) : '');
        $randomFileContent = 'Modified File' . PHP_EOL . str_shuffle('A Foo B bar x baz T foo bar bazz bazzer' . strval(rand(PHP_INT_MIN, PHP_INT_MAX)));
        if(file_exists($randomFilePath)) { unlink($randomFilePath); }
        file_put_contents($randomFilePath, $randomFileContent, LOCK_SH);
        /** Make sure content was modified **/
        $this->assertNotEquals(
            $originalContent,
            file_get_contents($randomFilePath),
            'WARNING: File at ' . $randomFilePath . ' was not modified, tests relying on this method can not be performed accurately.'
        );
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

    public function testRunCopiesAppPackageFilesAndDirectoriesToNewApp() : void
    {
        $makeAppPackage = new MakeAppPackage();
        $appPackagePath = $this->pathToValidAppPackage();
        $preparedArguments = $makeAppPackage->prepareArguments(
            [
                '--path',
                $appPackagePath
            ]
        );
        $makeAppPackage->run(new CommandLineUI(), $preparedArguments);
        $expectedFilePaths = $this->expectedNewAppFilePaths($appPackagePath, $preparedArguments);
        foreach($expectedFilePaths as $path) {
            $this->assertTrue(
                file_exists($path),
                'With the exception of files that end with extension .sh, ddms --make-app-package MUST copy all files and directories defined by the App Package to the new App. ' . PHP_EOL . 'The following file or directory should have been created: ' . PHP_EOL . PHP_EOL .'    ' . $path . PHP_EOL
            );
        }
    }

    /**
     * @param string $appPackagePath Path to the App Package that made the new App.
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array <int, string> Array of paths of all files and sub directories that are under the specified $path
     */
    private function expectedNewAppFilePaths(string $appPackagePath, array $preparedArguments) : array
    {
        $appPackageFilePaths = $this->scanDirRecursive($appPackagePath);
        $paths = [];
        foreach($appPackageFilePaths as $path) {
            if(substr($path, -3, 3) === '.sh') { continue; }
            $path = str_replace($appPackagePath, $this->expectedNewAppPath($preparedArguments), $path);
            array_push($paths, $path);
        }
        return $paths;
    }

    /**
     * @param string $path The path to scan.
     * @return array <int, string> Array of paths of all files and sub directories that are under the specified $path
     */
    private function scanDirRecursive(string $path) : array
    {
        $scan = scandir($path);
        $ls = array_diff((is_array($scan) ? $scan: []), ['.', '..']);
        $paths = [];
        foreach($ls as $listing) {
            $subPath = $path . DIRECTORY_SEPARATOR . $listing;
            array_push($paths, $subPath);
            if(is_dir($subPath)) {
                $paths = array_merge($paths, $this->scanDirRecursive($subPath));
            }
        }
        $paths = array_unique($paths);
        return $paths;
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
        return strval(realpath($preparedArguments['flags']['ddms-apps-directory-path'][0])) . DIRECTORY_SEPARATOR . $name;
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
