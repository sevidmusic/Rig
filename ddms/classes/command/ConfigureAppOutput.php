<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class ConfigureAppOutput extends AbstractCommand implements Command
{

    /**
     * @var UserInterface $ui
     */
    private $ui;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        $this->validateFlags($flags);
        return false;
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function validateFlags(array $flags): void
    {
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  You must specify a the name of the App the output is for via the --for-app flag. For help use ddms --help --configure-app-output');
        }
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  You must specify a name to use for the Components that will be configured for the output via the --name flag. For help use ddms --help --configure-app-output');
        }
        if(!isset($flags['output'][0]) && !isset($flags['output-source-file'][0])) {
            throw new RuntimeException('  You must specify either the --output, or --output-source-file flag. For help use ddms --help --configure-app-output');
        }

    }
}
