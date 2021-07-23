<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;
use \RuntimeException;

class NewAppPackage extends AbstractCommand implements Command
{
    /**
     * @var UserInterface $currentUserInterface
     */
    private $currentUserInterface;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool {
        $this->currentUserInterface = $userInterface;
        $flags = $this->validateArgumentsAndReturnFlags($preparedArguments);
        $this->createAppPackage($flags);
        $this->showMessage('  Created new App Package at ' . $this->determineNewAppPackagePath($flags) . ' which will generate an App named "' . $flags['name'][0]  .  '" whose default domain will be "' . $flags['domain'][0] . '"');
        return true;
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function createAppPackage(array $flags): void {
        $this->determineNewAppPackagePath($flags);
        if(!mkdir($this->determineNewAppPackagePath($flags), 0755)) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s directory at ' . $this->determineNewAppPackagePath($flags));
        }
        $this->showMessage('  Created new App Package\'s directory at ' . $this->determineNewAppPackagePath($flags));
        if(!mkdir($this->determineNewAppPackagesCssDirectoryPath($flags), 0755)) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s css directory at ' . $this->determineNewAppPackagesCssDirectoryPath($flags));
        }
        $this->showMessage('  Created new App Package\'s css directory at ' . $this->determineNewAppPackagesCssDirectoryPath($flags));
        if(!mkdir($this->determineNewAppPackagesJsDirectoryPath($flags), 0755)) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s js directory at ' . $this->determineNewAppPackagesJsDirectoryPath($flags));
        }
        $this->showMessage('  Created new App Package\'s js directory at ' . $this->determineNewAppPackagesJsDirectoryPath($flags));
        if(!mkdir($this->determineNewAppPackagesDynamicOutputDirectoryPath($flags), 0755)) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s DynamicOutput directory at ' . $this->determineNewAppPackagesDynamicOutputDirectoryPath($flags));
        }
        $this->showMessage('  Created new App Package\'s DynamicOutput directory at ' . $this->determineNewAppPackagesDynamicOutputDirectoryPath($flags));
        if(!mkdir($this->determineNewAppPackagesResourcesDirectoryPath($flags), 0755)) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s resources directory at ' . $this->determineNewAppPackagesResourcesDirectoryPath($flags));
        }
        $this->showMessage('  Created new App Package\'s resoureces directory at ' . $this->determineNewAppPackagesResourcesDirectoryPath($flags));
        if(file_put_contents($this->determineNewAppPackagesMakeShPath($flags), $this->generateMakeShFileContent($flags)) === false) {
            throw new RuntimeException('  Failed to create NewAppPackage\'s make.sh at ' . $this->determineNewAppPackagesMakeShPath($flags));
        }
        if(!chmod($this->determineNewAppPackagesMakeShPath($flags), 0755)) {
            $this->showMessage('  Failed to set NewAppPackage\'s make.sh permissions to 0755, this will have to be done manually! Path to make.sh: ' . $this->determineNewAppPackagesMakeShPath($flags));
        }
        $this->showMessage('  Created new App Package\'s make.sh at ' . $this->determineNewAppPackagesMakeShPath($flags));
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function generateMakeShFileContent($flags): string {
       return str_replace(['_NAME_', '_DOMAIN_'], [$flags['name'][0], $flags['domain'][0]], $this->getMakeShTemplate());
    }

    private function getMakeShTemplate(): string {
        return strval(file_get_contents($this->makeShTemplatePath()));
    }

    private function makeShTemplatePath(): string {
        return strval(
            realpath(
                str_replace(
                    'rig' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command',
                    'FileTemplates' . DIRECTORY_SEPARATOR . 'make.sh',
                    __DIR__
                )
            )
        );
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineNewAppPackagesMakeShPath(array $flags): string {
        return $this->determineNewAppPackagePath($flags) . DIRECTORY_SEPARATOR . 'make.sh';
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineNewAppPackagesCssDirectoryPath(array $flags): string {
        return $this->determineNewAppPackagePath($flags) . DIRECTORY_SEPARATOR . 'css';
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineNewAppPackagesJsDirectoryPath(array $flags): string {
        return $this->determineNewAppPackagePath($flags) . DIRECTORY_SEPARATOR . 'js';
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineNewAppPackagesDynamicOutputDirectoryPath(array $flags): string {
        return $this->determineNewAppPackagePath($flags) . DIRECTORY_SEPARATOR . 'DynamicOutput';
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineNewAppPackagesResourcesDirectoryPath(array $flags): string {
        return $this->determineNewAppPackagePath($flags) . DIRECTORY_SEPARATOR . 'resources';
    }

    private function showMessage(string $message) : void
    {
        $this->currentUserInterface->showMessage(
            PHP_EOL .
            $message .
            PHP_EOL
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array<string, array<int, string>>
     */
    private function validateArgumentsAndReturnFlags(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new App Package.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new App Package.');
        }
        if(!isset($flags['domain'][0])) {
            $this->showMessage('  --domain was not specified, domain will be "http://localhost:8080/"');
            $flags['domain'][0] = 'http://localhost:8080/';
        }
        if(!isset($flags['path'][0])) {
            $path = strval(realpath(strval(getcwd())));
            $this->showMessage('  --path was not specified, path will be assumed to be "' . $path . '"');
            $flags['path'][0] = $path;
        }
        if(file_exists($this->determineNewAppPackagePath($flags))) {
            throw new RuntimeException('  The path, ' . $this->determineNewAppPackagePath($flags) . ', is not available. Please specify an available path for the new App Package.');
        }
        if(!filter_var($flags['domain'][0], FILTER_VALIDATE_URL)) {
            throw new RuntimeException('  The domain, ' . $flags['domain'][0] . ', does not appear to be a valid domain. Please specify a domain that will pass PHP\'s FILTER_VALIDATE_URL filter.');
        }
        return $flags;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    public function determineNewAppPackagePath(array $flags) : string {
        return strval(realpath($flags['path'][0])) . DIRECTORY_SEPARATOR . $flags['name'][0];
    }

}
