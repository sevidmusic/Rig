<?php

namespace Darling\Rig\classes\arguments;

use \Darling\Rig\interfaces\arguments\Arguments as ArgumentsInterface;
use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;

class Arguments implements ArgumentsInterface
{
    /** @return array<string, string> */
    public function asArray(): array
    {
        return $this->rigArgumentsArray();
    }

    /** @return array<string, string> */
    private function rigCommandsArray(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = '';
        }
        return $rigCommands;
    }

    /** @return array<string, string> */
    private function rigCommandArgumentsArray(): array
    {
        $rigCommandArguments = [];
        foreach(RigCommandArgument::cases() as $case) {
            $rigCommandArguments[$case->value] = '';
        }
        return $rigCommandArguments;
    }

    /** @return array<string, string> */
    private function rigArgumentsArray(): array
    {
        $arguments = [];
        foreach(
            $this->rigCommandsArray()
            as
            $rigCommandName => $rigCommandDefaultValue
        ) {
            $arguments[$rigCommandName] = $rigCommandDefaultValue;
        }
        foreach($this->rigCommandArgumentsArray() as $rigCommandArgumentName => $rigCommandArgumentDefaultValue) {
            $arguments[$rigCommandArgumentName] = $rigCommandArgumentDefaultValue;
        }
        return $arguments;
    }
}

