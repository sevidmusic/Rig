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
        ['flags' => $flags] = $this->validateArguments($preparedArguments);
#        $this->showMessage('  Creating new App Package, ' . $flags['name'][0] . ' at path ' . $flags['path'][0] . PHP_EOL . PHP_EOL);
        return true;
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
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function validateArguments(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new App Package.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new App Package.');
        }
        if(!isset($flags['path'][0])) {
            $flags['path'][0] = 'http://localhost:8080/';
        }
        if(file_exists($flags['path'][0])) {
            throw new RuntimeException('  The path, ' . $flags['path'][0] . ', is not available. Please specify an available path for the new App Package.');
        }
        if(!filter_var($flags['domain'][0], FILTER_VALIDATE_URL)) {
            throw new RuntimeException('  The domain, ' . $flags['domain'][0] . ', does not appear to be a valid domain. Please specify a domain that will pass PHP\'s FILTER_VALIDATE_URL filter.');
        }
        return $preparedArguments;
    }


}
