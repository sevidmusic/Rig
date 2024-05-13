<?php

namespace Darling\Rig\classes\arguments;

use \Darling\Rig\interfaces\arguments\Arguments as ArgumentsInterface;
use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;

class Arguments implements ArgumentsInterface
{


    /** @param array<mixed> $specifiedArgumentData */
    public function __construct(private array $specifiedArgumentData = []) { }

    /** @return array<string, string> */
    public function asArray(): array
    {
        return $this->rigArgumentsArray();
    }

    public function specifiedArgumentData(): array
    {
        return $this->specifiedArgumentData;
    }

    /** @return array<string, string> */
    private function arrayThatDefinesRigCommandKeys(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = '';
        }
        return $rigCommands;
    }

    /** @return array<string, string> */
    private function arrayThatDefinesRigCommandArgumentKeys(): array
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
            $this->arrayThatDefinesRigCommandKeys()
            as
            $rigCommandName => $rigCommandDefaultValue
        ) {
            $arguments[$rigCommandName] =
                $this->argumentValueIfSpecified($rigCommandName);
        }
        foreach(
            $this->arrayThatDefinesRigCommandArgumentKeys()
            as
            $rigCommandArgumentName => $testArgumentDefaultValue
        ) {
            $arguments[$rigCommandArgumentName] =
                $this->argumentValueIfSpecified(
                    $rigCommandArgumentName
                );
        }
        return $arguments;
    }

    /**
     * Return an array of all the string values from the
     * specified array.
     *
     * If the specified array does not contain any strings,
     * an empty array will be returned.
     *
     * @param array<mixed> $array
     *
     * @return array<int|string, string>
     */
    public function deriveStringsFromArray(array $array): array
    {
        $strings = [];
        foreach($array as $key => $value) {
            if(is_string($value)) {
                $strings[$key] = $value;
            }
        }
        return $strings;
    }

    private function argumentValueIfSpecified(string $name): string
    {
        $specifiedArgumentStrings = $this->deriveStringsFromArray(
            $this->specifiedArgumentData()
        );
        return match(in_array($name, $specifiedArgumentStrings, true)) {
            // stand-alone argument
            true => $name,
            // argument has a value
            false => match(
                key_exists($name, $specifiedArgumentStrings)
                &&
                !empty($specifiedArgumentStrings[$name])
            ) {
                true => $specifiedArgumentStrings[$name],
                false => '',
            },
        };
    }
}

