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

    private string $expectedDefaultRigCommandArgumentValue = '';

    /**
     * @var Arguments $arguments
     *                              An instance of a
     *                              Arguments
     *                              implementation to test.
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
    private function expectedRigCommandsArray(): array
    {
        $rigCommands = [];
        foreach(RigCommand::cases() as $case) {
            $rigCommands[$case->value] = $this->expectedDefaultRigCommandArgumentValue;
        }
        return $rigCommands;
    }

    /** @return array<string, string> */
    private function expectedRigCommandArgumentsArray(): array
    {
        $rigCommandArguments = [];
        foreach(RigCommandArgument::cases() as $case) {
            $rigCommandArguments[$case->value] = '';
        }
        return $rigCommandArguments;
    }

    /** @return array<string, string> */
    private function expectedArgumentsArray(): array
    {
        $arguments = [];
        foreach(
            $this->expectedRigCommandsArray()
            as
            $rigCommandName => $rigCommandDefaultValue
        ) {
            $arguments[$rigCommandName] = $rigCommandDefaultValue;
        }
        foreach(
            $this->expectedRigCommandArgumentsArray()
            as
            $rigCommandArgumentName => $rigCommandArgumentDefaultValue
        ) {
            $arguments[$rigCommandArgumentName] = $rigCommandArgumentDefaultValue;
        }
        return $arguments;
    }

    public function test_asArray_returns_array_of_expected_argument_key_value_pairs(): void
    {
        $this->assertEquals(
            $this->expectedArgumentsArray(),
            $this->argumentsTestInstance()->asArray(),
            $this->testFailedMessage(
                $this->argumentsTestInstance(),
                'asArray',
                'returns expected array of Argument key value pairs'
            ),
        );
    }

    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;
}

