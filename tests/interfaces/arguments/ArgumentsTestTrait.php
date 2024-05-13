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
    private function arrayOfExpectedRigCommands(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = $this->testArgumentDefaultValue;
        }
        return $rigCommands;
    }

    /** @return array<string, string> */
    private function arrayOfExpectedRigCommandArguments(): array
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
            $this->arrayOfExpectedRigCommands()
            as
            $rigCommandName => $rigCommandDefaultValue
        ) {
            $arguments[$rigCommandName] = $rigCommandDefaultValue;
        }
        foreach(
            $this->arrayOfExpectedRigCommandArguments()
            as
            $rigCommandArgumentName => $testArgumentDefaultValue
        ) {
            $arguments[$rigCommandArgumentName] = $testArgumentDefaultValue;
        }
        return $arguments;
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

    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;
}

