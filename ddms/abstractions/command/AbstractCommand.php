<?php

namespace ddms\abstractions\command;

use ddms\interfaces\command\Command;
use ddms\interfaces\ui\UserInterface;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;


abstract class AbstractCommand implements Command
{

    public function prepareArguments(array $argv): array
    {
        return $this->validateArguments($this->distiguishFlagsAndOptions($this->flattenArray($this->convertValuesToStrings($argv))));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $flagsAndOptions
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>} $flagsAndOptions
     */
    private function validateArguments(array $flagsAndOptions): array
    {
        ['flags' => $flags] = $flagsAndOptions;
        if(!in_array('ddms-internal-flag-pwd', array_keys($flags)) || !isset($flags['ddms-internal-flag-pwd'][0]) || !file_exists($flags['ddms-internal-flag-pwd'][0])) {
            $flagsAndOptions['flags']['ddms-internal-flag-pwd'] = [DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR];
        }
        return $flagsAndOptions;
    }


    /**
     * @param array<int, string> $argv
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function distiguishFlagsAndOptions(array $argv): array
    {
        $args = ['flags' => [], 'options' => []];
        foreach($argv as $position => $arg) {
            if(substr($arg, 0, 2) === '--') {
                $args['flags'][str_replace('--' , '', $arg)] = [];
                $nextItemKey = $position + 1;
                while(isset($argv[$nextItemKey]) && substr($argv[$nextItemKey], 0, 2) !== '--') {
                    $args['flags'][str_replace('--' , '', $arg)][] = $argv[$nextItemKey];
                    $nextItemKey++;
                }
                continue;
            }
            if (!$this->in_array_recursive($arg, $args['flags'])) {
                $args['options'][$position] = $arg;
            }
        }
        return $args;
    }

    /**
     * @param mixed $haystack
     * @param array<mixed> $haystack
     * @return bool
     */
    private function in_array_recursive(string $needle, array $haystack): bool
    {
        foreach($haystack as $value) {
            if($value === $needle) {
                return true;
            }
            if (is_array($value) && $this->in_array_recursive($needle, $value)) {
                return true;
            }
        }
        return false;
    }

    abstract public function run(UserInterface $ddmsUI, array $preparedArguments = ['flags' => [], 'options' => []]): bool;

    /**
     * Flatten a multi-dimensional array. Keys are not preserved, unless
     * key of type string, in which case it will become a value in the
     * new array.
     * @param array<mixed> $array
     *
     * @return array<int, mixed>
     */
    private function flattenArray(array $array): array {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        $flatArr = [];
        foreach($iterator as $key => $value) {
            if(is_string($key)) {
                array_push($flatArr, $key);
            }
            array_push($flatArr, $value);
        }
        return $flatArr;
    }

    /**
     * @param array<mixed> $array
     *
     * @return array<array|string>
     */
    private function convertValuesToStrings(array $array): array {
        $convertedValues = [];
        foreach($array as $key => $value) {
            if(is_array($value) && !empty($value)) {
                $convertedValues[$key] = $this->convertValuesToStrings($value);
                continue;
            }
            if(is_null($value)) {
                $value = 'null';
            }
            if($value === true) {
                $value = 'true';
            }
            if($value === false) {
                $value = 'false';
            }
            if(is_object($value)) {
                $value = json_encode($value);
            }
            $convertedValues[$key] = ($value === []) ? '[]' : strval($value);
        }
        return $convertedValues;
    }

}
