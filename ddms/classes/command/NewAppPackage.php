<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
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
        return true;
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function createAppPackage(array $flags): void {
        $this->determineNewAppPackagePath($flags);
        if(!mkdir($this->determineNewAppPackagePath($flags), 0755))
        {
            throw new RuntimeException('  Failed to create NewAppPackage\'s directory at ' . $this->determineNewAppPackagePath($flags));
        }
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
            $flags['domain'][0] = 'http://localhost:8080/';
        }
        if(!isset($flags['path'][0])) {
            $flags['path'][0] = strval(realpath(strval(getcwd())));
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
        return $flags['path'][0] . DIRECTORY_SEPARATOR . $flags['name'][0];
    }

}
