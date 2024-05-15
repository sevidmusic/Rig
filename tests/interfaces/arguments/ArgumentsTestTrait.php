<?php

namespace Darling\Rig\tests\interfaces\arguments;

use \Darling\Rig\interfaces\arguments\Arguments;
use \PHPUnit\Framework\Attributes\CoversClass;
use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;

/**
 * The ArgumentsTestTrait defines common tests for
 * implementations of the Arguments interface.
 *
 * @see Arguments
 *
 */
#[CoversClass(Arguments::class)]
trait ArgumentsTestTrait
{

    private string $testArgumentDefaultValue = '';

    /** @var array<mixed> $expectedSpecifiedArgumentData */
    private array $expectedSpecifiedArgumentData = [];

    /**
     * @var Arguments $arguments An instance of a Arguments
     *                           implementation to test.
     */
    protected Arguments $arguments;

    /**
     * Set up an instance of a Arguments implementation to test.
     *
     * This method must set the Arguments implementation instance
     * to be tested via the setArgumentsTestInstance() method.
     *
     * This method may also be used to perform any additional setup
     * required by the implementation being tested.
     *
     * @return void
     *
     * @example
     *
     * ```
     * protected function setUp(): void
     * {
     *     $testArgumentData = [
     *         // Valid argument data
     *         '--new-module',
     *         '--module-name' => 'hello-wolrd',
     *         '--path-to-roady-project' => strval(
     *             realpath(
     *                 str_replace(
     *                     'tests' .
     *                     DIRECTORY_SEPARATOR .
     *                     'classes' .
     *                     DIRECTORY_SEPARATOR .
     *                     'arguments',
     *                     '',
     *                     __DIR__
     *                 )
     *             )
     *         ),
     *         // Invalid argument data
     *         $this->randomString(),
     *         $this->randomObjectInstance(),
     *         $this->randomFloat(),
     *         $this->randomClassStringOrObjectInstance(),
     *     ];
     *     $this->setExpectedSpecifiedArgumentData($testArgumentData);
     *     $this->setArgumentsTestInstance(
     *         new Arguments($testArgumentData)
     *     );
     * }
     * ```
     *
     */
    abstract protected function setUp(): void;

    /**
     * Return the Arguments implementation instance to test.
     *
     * @return Arguments
     *
     */
    protected function argumentsTestInstance(): Arguments
    {
        return $this->arguments;
    }

    /**
     * Set the Arguments implementation instance to test.
     *
     * @param Arguments $argumentsTestInstance
     *                              An instance of an
     *                              implementation of
     *                              the Arguments
     *                              interface to test.
     *
     * @return void
     *
     */
    protected function setArgumentsTestInstance(
        Arguments $argumentsTestInstance
    ): void
    {
        $this->arguments = $argumentsTestInstance;
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
            $rigCommands[$case->value] = $this->testArgumentDefaultValue;
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
            $rigCommandArguments[$case->value] = $this->testArgumentDefaultValue;
        }
        return $rigCommandArguments;
    }

    /** @return array<string, string> */
    private function arrayThatDefinesExpectedKeysAndValues(): array
    {
        $arguments = [];
        foreach(
            $this->arrayThatDefinesRigCommandKeys()
            as
            $rigCommandName => $rigCommandDefaultValue
        ) {
            $arguments[$rigCommandName] = $this->argumentValueIfSpecified($rigCommandName);
        }
        foreach(
            $this->arrayThatDefinesRigCommandArgumentKeys()
            as
            $rigCommandArgumentName => $testArgumentDefaultValue
        ) {
            $arguments[$rigCommandArgumentName] = $this->argumentValueIfSpecified($rigCommandArgumentName);
        }
        return $arguments;
    }

    /**
     * Set the array of $specifiedArgumentData that is expected to
     * be returned by the Arguments instance being tested's
     * specifiedArgumentData() method.
     *
     * @param array<mixed> $specifiedArgumentData
     *                         The array of $specifiedArgumentData
     *                         that is expected to be returned by
     *                         the Arguments instance being tested's
     *                         specifiedArgumentData() method.
     *
     * @return void
     *
     */
    private function setExpectedSpecifiedArgumentData(array $specifiedArgumentData): void
    {
        $this->expectedSpecifiedArgumentData = $specifiedArgumentData;
    }

    /**
     * Return the array of $specifiedArgumentData that is expected to
     * be returned by the Arguments instance being tested's
     * specifiedArgumentData() method.
     *
     * @return array<mixed>
     *
     */
    private function expectedSpecifiedArgumentData(): array
    {
        return $this->expectedSpecifiedArgumentData;
    }

    /**
     * Return the value of the item in the array returned by the
     * specifiedArgumentData() method whose key matches the
     * specified $key, if it exists.
     *
     * If the item does not exist in the array returned by the
     * specifiedArgumentData() method, return an empty string.
     *
     * @param string $key The key used to index the item
     *                    in the array returned by the
     *                    specifiedArgumentData() method.
     *
     */
    private function argumentValueIfSpecified(string $key): string
    {
        $specifiedArgumentData = $this->expectedSpecifiedArgumentData();
        return match(in_array($key, $specifiedArgumentData, true)) {
            true => $key,
            false => match(
                key_exists($key, $specifiedArgumentData)
                &&
                !empty($specifiedArgumentData[$key])
                &&
                is_string($specifiedArgumentData[$key])
            ) {
                true => $specifiedArgumentData[$key],
                false => $this->testArgumentDefaultValue,
            },
        };
    }


    /**
     * Test asArray() returns array with expected values defined.
     *
     * @return void
     *
     */
    public function test_asArray_returns_array_with_expected_values_defined(): void
    {
        $this->assertEquals(
            $this->arrayThatDefinesExpectedKeysAndValues(),
            $this->argumentsTestInstance()->asArray(),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'asArray',
                'returns array with expected keys defined',
            ),
        );
    }


    /**
     * Test asArray() returns array with expected keys defined.
     *
     * @return void
     *
     */
    public function test_asArray_returns_array_with_expected_keys_defined(): void
    {
        $this->assertEquals(
            array_keys($this->arrayThatDefinesExpectedKeysAndValues()),
            array_keys($this->argumentsTestInstance()->asArray()),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'asArray',
                'returns array with expected keys defined',
            ),
        );
    }


    /**
     * Test specifiedArgumentData() returns the specified argument data.
     *
     * @return void
     *
     */
    public function test_specifiedArgumentData_returns_the_specified_argument_data(): void
    {
        $this->assertEquals(
            $this->expectedSpecifiedArgumentData(),
            $this->argumentsTestInstance()->specifiedArgumentData(),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'specifiedArgumentData',
                'returns specified array of argument data',
            ),
        );
    }

    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;
    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;

}

