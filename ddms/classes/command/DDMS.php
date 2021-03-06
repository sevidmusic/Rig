<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractDDMS;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
use ddms\classes\factory\CommandFactory;

class DDMS extends AbstractDDMS implements Command
{

    public function __construct(private CommandFactory $commandFactory) {}

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        if(!empty($flags)) {
            $flags = array_keys($flags);
            $command = $this->convertFlagToCommandName(array_shift($flags));
            $expectedCommandNamespace = "\\ddms\\classes\\command\\$command";
            if($this->commandExists($expectedCommandNamespace)) {
                return $this->commandFactory->getCommandInstance($command, $userInterface)->run($userInterface, $preparedArguments);
            }
            $this->commandFactory->getCommandInstance('Help', $userInterface)->run($userInterface, $this->prepareArguments(['--help']));
            return throw new RuntimeException("Error: The first flag specified MUST correspond to an existing ddms command. Please use ddms --help for more information.");
        }
        return false;
    }

    private function commandExists(string $expectedCommandNamespace): bool
    {
        if(class_exists($expectedCommandNamespace)) {
            $classImplements = (is_array(class_implements($expectedCommandNamespace)) ? class_implements($expectedCommandNamespace) : []);
            if(in_array(Command::class, $classImplements)) {
                return true;
            }
        }
        return false;
    }

    private function convertFlagToCommandName(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

}
