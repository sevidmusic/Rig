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
        if(!in_array('ddms-apps-directory-path', array_keys($flags)) || !isset($flags['ddms-apps-directory-path'][0]) || !file_exists($flags['ddms-apps-directory-path'][0]) || !is_dir($flags['ddms-apps-directory-path'][0])) {
            $ddmsTmpDirPath = $this->determineDefaultAppsDirectoryPath($flags);
            $flagsAndOptions['flags']['ddms-apps-directory-path'] = [$ddmsTmpDirPath];
        }
        return $flagsAndOptions;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineDefaultAppsDirectoryPath(array $flags): string
    {
        $expectedDarlingDMSAppsDirectory = strval(
            realpath(
                str_replace(
                    'vendor' . DIRECTORY_SEPARATOR . 'darling' . DIRECTORY_SEPARATOR . 'ddms' . DIRECTORY_SEPARATOR . 'ddms' . DIRECTORY_SEPARATOR . 'abstractions' . DIRECTORY_SEPARATOR .  'command',
                    'Apps',
                    __DIR__)
            )
        );
        $ddmsTmpDirectoryPath = strval(realpath(str_replace('ddms' . DIRECTORY_SEPARATOR . 'abstractions' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__)));
        if(!in_array('ddms-apps-directory-path', array_keys($flags)) && substr($expectedDarlingDMSAppsDirectory, -4, 4) === 'Apps' && file_exists($expectedDarlingDMSAppsDirectory) && is_dir($expectedDarlingDMSAppsDirectory)) {
            return $expectedDarlingDMSAppsDirectory;
        }
        return $ddmsTmpDirectoryPath;
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

    /**
     * @param array <string, array<int, string>> $flags
     */
    protected function determineAppDirectoryPath(array $flags): string
    {
        return  $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0];
    }

}
