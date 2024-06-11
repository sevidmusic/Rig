<?php

namespace Darling\Rig\classes\arguments;

use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;
use \Darling\Rig\interfaces\arguments\Arguments as ArgumentsInterface;

class Arguments implements ArgumentsInterface
{

    private const EMPTY_STRING = '';

    /**
     * Instantiate a new Arguments instance.
     *
     * If provided, use the $specifiedArgumentData to define the
     * Argument data.
     *
     * If no $specifiedArgumentData is provided, then the Arguments
     * assigned value's will all be empty strings.
     *
     * @param array<mixed> $specifiedArgumentData
     *
     */
    public function __construct(
        private array $specifiedArgumentData = []
    ) { }

    public function asArray(): array
    {
        return $this->rigArgumentsArray();
    }

    public function specifiedArgumentData(): array
    {
        return $this->specifiedArgumentData;
    }

    /**
     * Return an array whose keys are defined by the RigCommand
     * enum's cases, and whose values are all empty strings.
     *
     * @return array<string, string>
     *
     */
    private function arrayThatDefinesRigCommandKeys(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = self::EMPTY_STRING;
        }
        return $rigCommands;
    }

    /**
     * Return an array whose keys are defined by the
     * RigCommandArgument enum's cases, and whose
     * values are all empty strings.
     *
     * @return array<string, string>
     *
     */
    private function arrayThatDefinesRigCommandArgumentKeys(): array
    {
        $rigCommandArguments = [];
        foreach(RigCommandArgument::cases() as $case) {
            $rigCommandArguments[$case->value] = self::EMPTY_STRING;
        }
        return $rigCommandArguments;
    }

    /**
     * Return an array whose keys are defined by the
     * RigCommand enum, and RigCommandArgument enum's
     * cases.
     *
     * The values of the array will be derived from the
     * array returned by the specifiedArgumentData() method,
     * and will be indexed by the RigCommand or RigCommandArgument
     * they are associated with.
     *
     * Only items whose value or key match a RigCommand
     * case or RigCommandArgument case will be included
     * in the returned array.
     *
     * Note: All RigCommand and RigCommandArgument cases will
     * be represented in the returned array. Any cases that do
     * not have a corresponding value in the array returned by
     * the specifiedArgumentData() method will be assigned an
     * empty string.
     *
     * For example, if the array of $specifiedArgumentData was:
     *
     * ```
     * array(3) {
     *   [0]=>
     *   string(12) "--new-module"
     *   ["--module-name"]=>
     *   string(11) "hello-wolrd"
     *   ["--path-to-roady-project"]=>
     *   string(21) "/home/darling/Git/Rig"
     * }
     * ```
     *
     * Then the returned array will be:
     *
     * ```
     * {
     *   ["--delete-route"]=>
     *   string(0) ""
     *   ["--help"]=>
     *   string(0) ""
     *   ["--list-routes"]=>
     *   string(0) ""
     *   ["--new-module"]=>
     *   string(12) "--new-module"
     *   ["--new-route"]=>
     *   string(0) ""
     *   ["--start-servers"]=>
     *   string(0) ""
     *   ["--update-route"]=>
     *   string(0) ""
     *   ["--version"]=>
     *   string(0) ""
     *   ["--view-action-log"]=>
     *   string(0) ""
     *   ["--view-readme"]=>
     *   string(0) ""
     *   ["--authority"]=>
     *   string(0) ""
     *   ["--defined-for-authorities"]=>
     *   string(0) ""
     *   ["--defined-for-files"]=>
     *   string(0) ""
     *   ["--defined-for-modules"]=>
     *   string(0) ""
     *   ["--defined-for-named-positions"]=>
     *   string(0) ""
     *   ["--defined-for-positions"]=>
     *   string(0) ""
     *   ["--defined-for-requests"]=>
     *   string(0) ""
     *   ["--for-authority"]=>
     *   string(0) ""
     *   ["--module-name"]=>
     *   string(11) "hello-wolrd"
     *   ["--named-positions"]=>
     *   string(0) ""
     *   ["--no-boilerplate"]=>
     *   string(0) ""
     *   ["--open-in-browser"]=>
     *   string(0) ""
     *   ["--path-to-roady-project"]=>
     *   string(21) "/home/darling/Git/Rig"
     *   ["--ports"]=>
     *   string(0) ""
     *   ["--relative-path"]=>
     *   string(0) ""
     *   ["--responds-to"]=>
     *   string(0) ""
     *   ["--route-hash"]=>
     *   string(0) ""
     * }
     * ```
     *
     * @return array<string, string>
     *
     */
    private function rigArgumentsArray(): array
    {
        $arguments = [];
        foreach(
            $this->arrayThatDefinesRigCommandKeys()
            as
            $rigCommandName => $defaultValue
        ) {
            $arguments[$rigCommandName] =
                $this->keyOrValueFromSpecifiedArgumentData($rigCommandName);
        }
        foreach(
            $this->arrayThatDefinesRigCommandArgumentKeys()
            as
            $rigCommandArgumentName => $defaultValue
        ) {
            $arguments[$rigCommandArgumentName] =
                $this->keyOrValueFromSpecifiedArgumentData(
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

    /**
     * If the specified $keyOrValue matches a value in the array
     * returned by the specifiedArgumentData() method then the
     * specified $keyOrValue will be returned unmodified.
     *
     * If the $keyOrValue matches a key defined in the array returned
     * by the specifiedArgumentData() method, and the value assigned
     * to that key is a string, then that value will be returned.
     *
     * If the item does not exist in the array returned by the
     * specifiedArgumentData() method as a value or a key,
     * then an empty string will be returned.
     *
     * @param string $keyOrValue The key or value to search for.
     *                           in the array returned by the
     *                           specifiedArgumentData() method.
     *
     */
    private function keyOrValueFromSpecifiedArgumentData(string $keyOrValue): string
    {
        $specifiedArgumentStrings = $this->deriveStringsFromArray(
            $this->specifiedArgumentData()
        );
        return match(in_array($keyOrValue, $specifiedArgumentStrings, true)) {
            // $keyOrValue matches numerically indexed value
            true => $keyOrValue,
            // $keyOrValue matches associatively indexed value
            false => match(
                key_exists($keyOrValue, $specifiedArgumentStrings)
                &&
                !empty($specifiedArgumentStrings[$keyOrValue])
            ) {
                true => $specifiedArgumentStrings[$keyOrValue],
                false => '',
            },
        };
    }
}

