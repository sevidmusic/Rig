<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractRig;
use rig\interfaces\ui\UserInterface;
use \RuntimeException;
use rig\classes\factory\CommandFactory;

class Rig extends AbstractRig implements Command
{

    public function __construct(private CommandFactory $commandFactory) {}

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        if(!empty($flags)) {
            $flagNames = array_keys($flags);
            $command = $this->convertFlagToCommandName(array_shift($flagNames));
            $expectedCommandNamespace = "\\rig\\classes\\command\\$command";
            if($this->commandExists($expectedCommandNamespace)) {
                $result = $this->commandFactory->getCommandInstance($command, $userInterface)->run($userInterface, $preparedArguments);
                if(in_array('debug', $flagNames, true) && in_array('options', $flags['debug'], true)) {
                    $userInterface->showOptions($preparedArguments);
                }
                if(in_array('debug', $flagNames, true) && in_array('flags', $flags['debug'], true)) {
                    $userInterface->showFlags($preparedArguments);
                }
                return $result;
            }
            $this->commandFactory->getCommandInstance('Help', $userInterface)->run($userInterface, $this->prepareArguments(['--help']));
            return throw new RuntimeException($this->getInvalidCommandMsg());
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

    private function getInvalidCommandMsg(): string
    {
        return PHP_EOL .
                "\e[0m  \e[103m\e[30m" .
                str_replace(
                    [
                        'Error',
                        'rig --help'
                    ],
                    [
                        "\e[0m\e[102m\e[30mError\e[0m\e[103m\e[30m",
                        "\e[0m\e[104m\e[30mrig --help\e[0m\e[103m\e[30m"
                    ],
                    "Error: The first flag specified MUST correspond to an existing rig command. Please use rig --help for more information."
                ) . "\e[0m" . PHP_EOL;
    }
}
