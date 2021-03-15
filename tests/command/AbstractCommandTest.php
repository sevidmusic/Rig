<?php

namespace tests\command\AbstractCommand;

use PHPUnit\Framework\TestCase;
use ddms\abstractions\command\AbstractCommand;

final class AbstractCommandTest extends TestCase
{

    public function testPrepareArgumentsReturnsArrayWith_ddms_internal_flag_pwd_FlagPresent(): void
    {
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']['ddms-internal-flag-pwd']));
    }

    public function testPrepareArgumentsReturnsArrayWhose_ddms_internal_flag_pwd_FlagIsAssignedAtLeastOneArgument(): void
    {
        $this->assertTrue(isset($this->getMockCommand()->prepareArguments($this->getMockArray())['flags']['ddms-internal-flag-pwd'][0]));
    }

    public function testPrepareArgumentsReturnsArrayWhose_ddms_internal_flag_pwd_FlagsFirstAssignedArgumentIsAPathToAnExistingDirectory(): void
    {
        $this->assertTrue(file_exists($this->getMockCommand()->prepareArguments(['--ddms-internal-flag-pwd', 'FooBar' . strval(rand(1000,999))])['flags']['ddms-internal-flag-pwd'][0]));
    }

    public function testPrepareArgumentsReturnsArrayWhose_ddms_internal_flag_pwd_FlagsFirstAssignedArgumentMatchesSpecifiedArgumentIfFirstArgumentIsAPathToAnExistingDirectory(): void
    {
        $this->assertEquals(
            __DIR__,
            $this->getMockCommand()->prepareArguments(['--ddms-internal-flag-pwd', __DIR__])['flags']['ddms-internal-flag-pwd'][0]
        );
    }

    public function testPrepareArgumentsReturnsArrayWhose_ddms_internal_flag_pwd_FlagsFirstArgumentIsAssignedPathTo_ddms_tmp_DirectoryIf_ddms_internal_flag_pwd_FlagsFirstSpecifiedArgumentIsNotAPathToAnExistingDirectory(): void
    {
        $this->assertEquals(
            realpath(str_replace('tests' . DIRECTORY_SEPARATOR . 'command', 'tmp', __DIR__)),
            $this->getMockCommand()->prepareArguments(['--ddms-internal-flag-pwd', 'FooBar' . strval(rand(1000, 9999))])['flags']['ddms-internal-flag-pwd'][0]
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

