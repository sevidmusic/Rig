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
     *     $this->setArgumentsTestInstance(
     *         new \Darling\Rig\classes\arguments\Arguments()
     *     );
     * }
     *
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

    /** @return array<string, string> */
    private function arrayThatDefinesRigCommandKeys(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = $this->testArgumentDefaultValue;
        }
        return $rigCommands;
    }

    /** @return array<string, string> */
    private function arrayThatDefinesRigCommandArgumentKeys(): array
    {
        $rigCommandArguments = [];
        foreach(RigCommandArgument::cases() as $case) {
            $rigCommandArguments[$case->value] = $this->testArgumentDefaultValue;
        }
        return $rigCommandArguments;
    }

    /** @return array<string, string> */
    private function arrayThatDefinesExpectedKeys(): array
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
     * @param array<mixed> $specifiedArgumentData
     *
     * @return void
     *
     */
    private function setExpectedSpecifiedArgumentData(array $specifiedArgumentData): void
    {
        $this->expectedSpecifiedArgumentData = $specifiedArgumentData;
    }

    /**
     * @return array<mixed>
     */
    private function expectedSpecifiedArgumentData(): array
    {
        return $this->expectedSpecifiedArgumentData;
    }

    private function argumentNameIfSpecified(string $name): string
    {
        return (
            isset($this->expectedSpecifiedArgumentData()[$name])
            ? $name
            : ''
        );
    }

    private function argumentValueIfSpecified(string $name): string
    {
        $specifiedArgumentData = $this->expectedSpecifiedArgumentData();
        return match(in_array($name, $specifiedArgumentData, true)) {
            true => $name,
            false => match(
                key_exists($name, $specifiedArgumentData)
                &&
                !empty($specifiedArgumentData[$name])
                &&
                is_string($specifiedArgumentData[$name])
            ) {
                true => $specifiedArgumentData[$name],
                false => $this->testArgumentDefaultValue,
            },
        };
    }

    public function test_asArray_returns_array_with_expected_values_defined(): void
    {
        $this->assertEquals(
            $this->arrayThatDefinesExpectedKeys(),
            $this->argumentsTestInstance()->asArray(),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'asArray',
                'returns array with expected keys defined',
            ),
        );
    }

    public function test_asArray_returns_array_with_expected_keys_defined(): void
    {
        $this->assertEquals(
            array_keys($this->arrayThatDefinesExpectedKeys()),
            array_keys($this->argumentsTestInstance()->asArray()),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'asArray',
                'returns array with expected keys defined',
            ),
        );
    }

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

    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;

}

