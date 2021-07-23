<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\command\NewAppPackage;
use rig\classes\ui\CommandLineUI;
use rig\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class NewAppPackageTest extends TestCase
{

    use TestsCreateApps;

    /**
     * @var array <int, string> $generatedPaths
     */
    private array $generatedPaths = [];

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
                # the context of this script will target the rig directory, which
                # means the target directory in the context of this script will
                # contain a directory named rig, i.e. path/to/rig/rig, so the
                # name rig can be used for this test since a directory named rig
                # will exist so an App Package named rig should not be created
                # and run() should throw a RuntimeException.
                'rig',
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
                # Not specifying the --path will result in rig using the current
                # working directory as the target directory, which, from the
                # context of this script will be the rig directory. This means
                # the target directory in the context of this script will contain
                # a directory named rig, i.e. path/to/rig/rig, so the name rig
                # can be used for this test since a directory named rig will exist
                # so an App Package named rig should not be created and run()
                # should throw a RuntimeException.
                'rig',
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
    }

    public function testRunCreatesNewAppPackageDirectoryInCurrentWorkingDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
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
    }

    public function testRunCreatesNewAppPackagesCssDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'css'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'css'
        );
    }

    public function testRunCreatesNewAppPackagesJsDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'js'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'js'
        );
    }

    public function testRunCreatesNewAppPackagesDynamicOutputDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'DynamicOutput'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'DynamicOutput'
        );
    }

    public function testRunCreatesNewAppPackagesResourcesDirectoryIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'resources'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'resources'
        );
    }

    public function testRunCreatesNewAppPackagesMakeShIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'make.sh'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'make.sh'
        );
    }

    public function testRunSetsNewAppPackagesMakeShFilePermissionsTo0755(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            is_executable($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'make.sh'),
            'New App Package\'s make.sh permissions were not set to 0755. make.sh path: ' . $this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName)
        );
    }

    public function testRunCreatesNewAppPackagesMakeShWhoseContentMatchesExpectedContentIfPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertEquals(
            $this->expectedMakeShFileContent($appPackageName),
            file_get_contents($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'make.sh'),
        );
    }

    public function testRunSetDomainInNewAppPackagesMakeShToSepcifiedDomainIfDomanIsSpecifiedButPathIsNotSpecified(): void {
        $appPackageName = $this->getRandomName();
        $domain = 'http://localhost:' . strval(rand(8000, 8999)) . '/';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--domain',
                $domain
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertEquals(
            $this->expectedMakeShFileContent($appPackageName, $domain),
            file_get_contents($this->expectedNewAppPackagePathIfPathIsNotSpecified($appPackageName) . DIRECTORY_SEPARATOR . 'make.sh'),
        );
    }

    public function testRunCreatesNewAppPackageDirectoryAtSpecifiedPathIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path)),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path)
        );
    }

    public function testRunCreatesNewAppPackagesCssDirectoryIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'css'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'css'
        );
    }

    public function testRunCreatesNewAppPackagesJsDirectoryIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'js'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'js'
        );
    }

    public function testRunCreatesNewAppPackagesDynamicOutputDirectoryIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'DynamicOutput'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'DynamicOutput'
        );
    }

    public function testRunCreatesNewAppPackagesResourcesDirectoryIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'resources'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'resources'
        );
    }

    public function testRunCreatesNewAppPackagesMakeShIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertTrue(
            file_exists($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'make.sh'),
            'Expected New App Package Path: ' . $this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'make.sh'
        );
    }

    public function testRunCreatesNewAppPackagesMakeShWhoseContentMatchesExpectedContentIfPathIsSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertEquals(
            $this->expectedMakeShFileContent($appPackageName),
            file_get_contents($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'make.sh'),
        );
    }

    public function testRunSetDomainInNewAppPackagesMakeShToSepcifiedDomainIfDomanIsSpecifiedAndPathIsAlsoSpecified(): void {
        $appPackageName = $this->getRandomName();
        $path = DIRECTORY_SEPARATOR . 'tmp';
        $domain = 'http://localhost:' . strval(rand(8000, 8999)) . '/';
        $newAppPackage =  new NewAppPackage();
        $preparedArguments = $newAppPackage->prepareArguments(
            [
                '--name',
                $appPackageName,
                '--path',
                $path,
                '--domain',
                $domain
            ]
        );
        $newAppPackage->run($this->getUI(), $preparedArguments);
        $this->assertEquals(
            $this->expectedMakeShFileContent($appPackageName, $domain),
            file_get_contents($this->expectedNewAppPackagePathIfPathIsSpecified($appPackageName, $path) . DIRECTORY_SEPARATOR . 'make.sh'),
        );
    }

    private function expectedMakeShFileContent(string $appPackageName, string $domain = 'http://localhost:8080/'): string {
        return str_replace(['_NAME_', '_DOMAIN_'], [$appPackageName, $domain], strval(file_get_contents($this->determineMakeShFileTemplatePath())));
    }

    private function determineMakeShFileTemplatePath(): string {
        return strval(realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates' . DIRECTORY_SEPARATOR . 'make.sh', __DIR__)));
    }

    protected function tearDown(): void {
        $paths = array_unique($this->generatedPaths);
        foreach($paths as $path) {
            if(file_exists($path)) {
                self::removeDirectory($path);
            }
        }
    }

    private function getRandomName(): string {
        return 'NewTestAppPackage' . strval(rand(420, 4200));
    }

    private function expectedNewAppPackagePathIfPathIsNotSpecified(string $name): string {
        $path = strval(realpath(strval(getcwd()))) . DIRECTORY_SEPARATOR . $name;
        array_push($this->generatedPaths, $path);
        return $path;
    }

    private function expectedNewAppPackagePathIfPathIsSpecified(string $name, string $path): string {
        $path = strval(realpath($path)) . DIRECTORY_SEPARATOR . $name;
        array_push($this->generatedPaths, $path);
        return $path;
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
