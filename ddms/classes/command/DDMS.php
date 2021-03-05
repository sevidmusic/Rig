<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractDDMS;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class DDMS extends AbstractDDMS implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        if(!empty($preparedArguments['flags'])) {
            $flags = array_keys($preparedArguments['flags']);
            $command = $this->convertFlagToCommandName(array_shift($flags));
            $expectedCommandNamespace = "\\ddms\\classes\\command\\$command";
            if($this->commandExists($expectedCommandNamespace)) {
                return true;
            }
        }
        return throw new RuntimeException("ddms runtime Error: The first flag specified MUST correspond to an existing ddms command. Please use ddms --help for more information.");
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
