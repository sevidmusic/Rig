<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class MakeAppPackage extends AbstractCommand implements Command
{
    /**
     * @var UserInterface $currentUserInterface
     */
    private $currentUserInterface;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool {
        $this->currentUserInterface = $userInterface;
        $this->validateArguments($preparedArguments);
        $this->validateAppPackagesMakeSh($preparedArguments);
        $this->makeAppPackage($preparedArguments);
        $this->showMessage(
            '  Successfully made App Package at ' .
            $preparedArguments['flags']['path'][0] .
            ' into an App at ' .
            $preparedArguments['flags']['ddms-apps-directory-path'][0] .
            DIRECTORY_SEPARATOR . $this->determineAppName($preparedArguments)
        );
        return true;
    }

    private function showMessage(string $message): void
    {
        $this->currentUserInterface->showMessage(
            PHP_EOL . $message . PHP_EOL . PHP_EOL
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function makeAppPackage(array $preparedArguments): void {
        $this->removeAppIfItExists($preparedArguments);
        $this->showMessage(strval(shell_exec($this->determineMakeShPath($preparedArguments))));
        $this->copyAppPackageFilesAndDirectories($preparedArguments);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function removeAppIfItExists(array $preparedArguments) : void {
        $newAppPath = $this->newAppPath($preparedArguments);
        if(file_exists($newAppPath) && is_dir($newAppPath)) {
            self::removeDirectory($newAppPath);
        }
    }

    private static function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $contents = scandir($dir);
            $contents = (is_array($contents) ? $contents : []);
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

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function copyAppPackageFilesAndDirectories(array $preparedArguments): void
    {
        $appPackgePath = $this->appPackagePath($preparedArguments);
        $newAppPath = $this->newAppPath($preparedArguments);
        $this->copyFilesFromSourceRecursivlyecursive($appPackgePath, $newAppPath);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function appPackagePath(array $preparedArguments) : string
    {
        return $preparedArguments['flags']['path'][0];
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function newAppPath(array $preparedArguments) : string
    {
        return $preparedArguments['flags']['ddms-apps-directory-path'][0] .
               DIRECTORY_SEPARATOR .
               basename($this->appPackagePath($preparedArguments));
    }

    private function copyFilesFromSourceRecursivlyecursive(string $sourceDirectory, string $destinationDirectory) : void
    {
        $ls = $this->scanDirRecursive($sourceDirectory);
        foreach($ls as $source) {
            if(substr($source, -3, 3) === '.sh') { continue; }
            $destination = str_replace($sourceDirectory, $destinationDirectory, $source);
            # handle directories
            if(is_dir($source)) {
                if(!file_exists($destination)) {
                    mkdir($destination, permissions: 0755);
                }
                continue;
            }
            copy($source, $destination);
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function validateArguments(array $preparedArguments): void
    {
        if(!isset($preparedArguments['flags']['path'][0])) {
            throw new RuntimeException('  Please specify the path to the App Package to make.' . PHP_EOL);
        }
        if(!file_exists($preparedArguments['flags']['path'][0])) {
            throw new RuntimeException('  Please specify a path to an existing App Package.' . PHP_EOL);
        }
        if(!file_exists($preparedArguments['flags']['path'][0] . DIRECTORY_SEPARATOR . 'make.sh')) {
            throw new RuntimeException('  Please specify a path to an actual App Package.' . PHP_EOL);
        }
#        if(file_exists($preparedArguments['flags']['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $this->determineAppName($preparedArguments))) {
#            throw new RuntimeException('  An App named ' . $this->determineAppName($preparedArguments) . ' already exists. This App Package cannot be built unless it is removed.' . PHP_EOL);
#        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function validateAppPackagesMakeSh(array $preparedArguments): void
    {
        $this->validateMakeShCallsToDdmsNewApp($preparedArguments);
        $this->validateMakeShIsExecutable($preparedArguments);
        $this->validateMakeShsCallToDdmsNewAppUseCorrectName($preparedArguments);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getMakeShContent($preparedArguments): string
    {
        return strval(file_get_contents($this->determineMakeShPath($preparedArguments)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function validateMakeShCallsToDdmsNewApp(array $preparedArguments): void
    {
        $makeShContent = $this->getMakeShContent($preparedArguments);
        $callsToDdmsNewApp = substr_count($makeShContent, 'ddms --new-app');
        if($callsToDdmsNewApp !== 1) {
            switch($callsToDdmsNewApp < 1) {
                case true:
                    throw new RuntimeException(
                        '  The specified App Package\'s make.sh is not valid' . PHP_EOL .
                        '  because it does not define a call to `ddms --new-app`.' . PHP_EOL . PHP_EOL .
                        '  An App Package\'s make.sh MUST define exactly one call to `ddms --new-app`.' . PHP_EOL .
                        '  App Packages MUST create an App, and MUST NOT create more than one App.' . PHP_EOL
                    );
                default:
                    throw new RuntimeException(
                        '  The specified App Package\'s make.sh is not valid' . PHP_EOL .
                        '  because it defines more than one call to `ddms --new-app`.' . PHP_EOL . PHP_EOL .
                        '  An App Package\'s make.sh MUST define exactly one call to `ddms --new-app`.' . PHP_EOL .
                        '  App Packages MUST create an App, and MUST NOT create more than one App.' . PHP_EOL
                    );
            }
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function validateMakeShIsExecutable(array $preparedArguments): void
    {
        if(!is_executable($this->determineMakeShPath($preparedArguments))) {
           throw new RuntimeException('    Make sh not exec');
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function validateMakeShsCallToDdmsNewAppUseCorrectName(array $preparedArguments): void
    {
        $makeShContent = $this->getMakeShContent($preparedArguments);
        $expectedName = $this->determineAppName($preparedArguments);
        if(!str_contains($makeShContent, 'ddms --new-app --name ' . $expectedName)) {
           throw new RuntimeException('    Make sh not use correct name, expected: ' . $expectedName);
        }
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineAppName(array $preparedArguments): string
    {
        return strval(basename($preparedArguments['flags']['path'][0]));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineMakeShPath($preparedArguments): string
    {
        return $preparedArguments['flags']['path'][0] . DIRECTORY_SEPARATOR . 'make.sh';
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

}


