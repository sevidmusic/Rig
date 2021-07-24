<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use rig\abstractions\command\AbstractCommand;

final class AbstractCommandTest extends TestCase
{

    public function testPrepareArgumentsReturnsArrayWith_rig_apps_directory_path_FlagPresent(): void
    {
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']['path-to-apps-directory']));
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagIsAssignedAtLeastOneArgument(): void
    {
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']['path-to-apps-directory'][0]));
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagsFirstAssignedArgumentIsAPathToAnExistingDirectoryEvenIfFirstSpecifiedArgumentIsNotAPathToAnExistingDirectory(): void
    {
        $this->assertTrue(file_exists($this->getMockCommand()->prepareArguments(['--path-to-apps-directory', 'FooBar' . strval(rand(1000,999))])['flags']['path-to-apps-directory'][0]));
        $this->assertTrue(is_dir($this->getMockCommand()->prepareArguments(['--path-to-apps-directory', 'FooBar' . strval(rand(1000,999))])['flags']['path-to-apps-directory'][0]));
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagsFirstAssignedArgumentMatchesSpecifiedArgumentIfSpecifiedArgumentIsAPathToAnExistingDirectory(): void
    {
        $this->assertEquals(
            __DIR__,
            $this->getMockCommand()->prepareArguments(['--path-to-apps-directory', __DIR__])['flags']['path-to-apps-directory'][0]
        );
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagsFirstArgumentIsAssignedPathTo_rig_tmp_DirectoryIfFirstSpecifiedArgumentIsNotAPathToAnExistingDirectory(): void
    {
        $this->assertEquals(
            realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__)),
            $this->getMockCommand()->prepareArguments(['--path-to-apps-directory', 'FooBar' . strval(rand(1000, 9999))])['flags']['path-to-apps-directory'][0]
        );
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagsFirstArgumentIsAssignedPathTo_rig_tmp_DirectoryIf_rig_apps_directory_path_FlagIsSpecifiedWithNoArguments(): void
    {
        $this->assertEquals(
            realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__)),
            $this->getMockCommand()->prepareArguments(['--path-to-apps-directory'])['flags']['path-to-apps-directory'][0]
        );
    }

    private function determineExpectedRoadyAppsDirectory(): string
    {
        // This path is used if rig is istalled  inside roady's vendor directory, or mock vendor directory.
        $expectedRoadyAppsDirectory = strval(realpath(str_replace('vendor' . DIRECTORY_SEPARATOR . 'darling' . DIRECTORY_SEPARATOR . 'rig' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR .  'command', 'Apps', __DIR__)));
        if(substr($expectedRoadyAppsDirectory, -4, 4) === 'Apps' && file_exists($expectedRoadyAppsDirectory) && is_dir($expectedRoadyAppsDirectory)) {
            return $expectedRoadyAppsDirectory;
        }
        // This path is used if rig is not installed inside roady's vendor directory, or mock vendor directory.
        return strval(realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__)));
    }

    public function testPrepareArgumentsReturnsArrayWhose_rig_apps_directory_path_FlagsFirstArgumentIsAssignedPathToExpectedroadyAppsDirectoryOr_rig_tmp_DirectoryIf_rig_apps_directory_path_FlagIsNotSpecified(): void
    {
        $expectedRoadyAppsDirectory = $this->determineExpectedRoadyAppsDirectory();
        $this->assertEquals(
            $expectedRoadyAppsDirectory,
            $this->getMockCommand()->prepareArguments([])['flags']['path-to-apps-directory'][0]
        );
    }

    public function testPrepareArgumentsReturnsAnArrayWhoseNonRecursiveCountIsTwo(): void
    {
        $this->assertEquals(2, count($this->getMockCommand()->prepareArguments($this->getMockArray())));
    }

    public function testPrepareArgumentsReturnsAnArrayWhoseTopLevelIndexesAre_flags__And__options(): void
    {
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']));
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['options']));
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayAtIndex_flags(): void
    {
        $this->assertTrue(is_array($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']));
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayWhoseKeysAreStringsAtIndex_flags():void
    {
        $this->assertTrue($this->keysAreStrings($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']));
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayWhoseValuesAreArraysAtIndex_flags():void
    {
        $flagsArray = $this->getMockCommand()->prepareArguments($this->getMockArray())['flags'];
        foreach($flagsArray as $flagArray) {
            $this->assertTrue(is_array($flagArray));
        }
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayOfArraysWhoseKeysAreIntsAtIndex_flags():void
    {

        $flagsArray = $this->getMockCommand()->prepareArguments($this->getMockArray())['flags'];
        foreach($flagsArray as $flagArray) {
            foreach($flagArray as $key => $value) {
                $this->assertTrue(is_int($key));
            }
        }
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayOfArraysWhoseValuesAreStringsAtIndex_flags():void
    {
        $flagsArray = $this->getMockCommand()->prepareArguments($this->getMockArray())['flags'];
        foreach($flagsArray as $flagArray) {
            foreach($flagArray as $value) {
                $this->assertTrue(is_string($value));
            }
        }
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayAtIndex_options(): void
    {
        $this->assertTrue(is_array($this->getMockCommand()->prepareArguments($this->getMockArray())['options']));
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayWhoseKeysAreIntsAtIndex_options():void
    {
        $this->assertTrue($this->keysAreInts($this->getMockCommand()->prepareArguments($this->getMockArray())['options']));
    }

    public function testPrepareArgumentsReturnsAnArrayThatIsAssignedAnArrayWhoseValuesAreStringsAtIndex_options():void
    {
        $flagsArray = $this->getMockCommand()->prepareArguments($this->getMockArray())['options'];
        foreach($flagsArray as $flagArray) {
            $this->assertTrue(is_string($flagArray));
        }
    }

    /**
     * @param array<mixed> $array
     *
     * @return bool True if all keys in array are ints, or if array is empty, false otherwise.
     */
    private function keysAreInts(array $array, bool $recurse = false): bool
    {
        if(empty($array)) { return true; }
        $status = [];
        foreach($array as $key => $value) {
            array_push($status, is_int($key));
            if(is_array($value) && !empty($value)) {
                if($recurse === true) {
                    array_push($status, $this->keysAreInts($value, true));
                }
                continue;
            }
        }
        return !in_array(false, $status);
    }


    /**
     * @param array<mixed> $array
     *
     * @return bool True if all keys in array are strings, or if array is empty, false otherwise.
     */
    private function keysAreStrings(array $array, bool $recurse = false): bool
    {
        if(empty($array)) { return true; }
        $status = [];
        foreach($array as $key => $value) {
            array_push($status, is_string($key));
            if(is_array($value) && !empty($value)) {
                if($recurse === true) {
                    array_push($status, $this->keysAreStrings($value, true));
                }
                continue;
            }
        }
        return !in_array(false, $status);
    }

    /**
     * @param mixed $needle A values to search for.
     * @param array<mixed> $haystack An array of values to be interpreted as arguments.
     */
    private function in_array_recursive(mixed $needle, array $haystack): bool
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

    /**
     * Verify that all keys in a given array are type int or string.
     *
     * @param array<mixed> $array An array of values to be interpreted as arguments.
     *
     * @return bool True if all keys are type int or string, false otherwise.
     */
    private function valuesAreStringsOrArrays(array $array): bool
    {
        $status = [];
        foreach($array as $key => $value) {
            if(!is_string($value) && !is_int($value)) {
                array_push($status, false);
            }
            if(is_array($value)) {
                array_push($status, $this->valuesAreStringsOrArrays($value));
            }
        }
        return !in_array(false, $status);
    }

    private function getMockCommand(): AbstractCommand
    {
        return $this->getMockBuilder(AbstractCommand::class)->getMockForAbstractClass();
    }

    /**
     * @return array<mixed> $array An array of values to be interpreted as arguments.
     */
    private function getMockArray(): array
    {
        $arrays = [
            ['OPTION', '--flag', ['arg1', 'arg2'], null, '--flag' => 'arg1'],
            ['OPTION', rand(0, PHP_INT_MAX), '--flag', [[],'arg1' => ['bazzer'], 'arg2'], false, 'stringkey' => 'value'],
            ['OPTIONA', 'OPTIONB', 'OPTIONC', '--flag' => ['arg1', 'arg2'], '--flag2', null, false, true]
        ];
        return $arrays[array_rand($arrays)];
    }
}

