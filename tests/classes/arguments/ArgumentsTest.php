<?php

namespace Darling\Rig\tests\classes\arguments;

use \Darling\Rig\classes\arguments\Arguments;
use \Darling\Rig\tests\RigTest;
use \Darling\Rig\tests\interfaces\arguments\ArgumentsTestTrait;
use \PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Arguments::class)]
class ArgumentsTest extends RigTest
{

    /**
     * The ArgumentsTestTrait defines common tests for implementations
     * of the Darling\Rig\interfaces\arguments\Arguments interface.
     *
     * @see ArgumentsTestTrait
     *
     */
    use ArgumentsTestTrait;

    private function randomRigCommand(): string
    {
        $rigCommands = array_keys($this->arrayThatDefinesRigCommandKeys());
        return $rigCommands[array_rand($rigCommands)];
    }

    private function randomRigCommandArgument(): string
    {
        $rigCommandArguments = array_keys($this->arrayThatDefinesRigCommandArgumentKeys());
        return $rigCommandArguments[array_rand($rigCommandArguments)];
    }

    public function setUp(): void
    {
        $testArgumentDataArrays = [
            // empty array
            [],
            // associative array
            [
                // Valid argument data
                $this->randomRigCommand(),
                $this->randomRigCommandArgument() => $this->randomChars(),
                '--path-to-roady-project' => strval(
                    realpath(
                        str_replace(
                            'tests' .
                            DIRECTORY_SEPARATOR .
                            'classes' .
                            DIRECTORY_SEPARATOR .
                            'arguments',
                            '',
                            __DIR__
                        )
                    )
                ),
                // Invalid argument data
                $this->randomString(),
                $this->randomFloat(),
                #$this->randomObjectInstance(),
                #$this->randomClassStringOrObjectInstance(),
            ],
            // numerically indxed array
            [
                // Valid argument data
                $this->randomRigCommand(),
                $this->randomRigCommandArgument(),
                $this->randomString(),
                $this->randomString(),
                $this->randomString(),
                $this->randomRigCommandArgument(),
                $this->randomFloat(),
                '--path-to-roady-project' => strval(
                    realpath(
                        str_replace(
                            'tests' .
                            DIRECTORY_SEPARATOR .
                            'classes' .
                            DIRECTORY_SEPARATOR .
                            'arguments',
                            '',
                            __DIR__
                        )
                    )
                ),
                $this->randomRigCommandArgument(),
                // Invalid argument data
                #$this->randomObjectInstance(),
                #$this->randomClassStringOrObjectInstance(),
            ]

        ];
        $testArgumentData = $testArgumentDataArrays[array_rand($testArgumentDataArrays)];
        $this->setExpectedSpecifiedArgumentData($testArgumentData);
        $this->setArgumentsTestInstance(
            new Arguments($testArgumentData)
        );
    }
}

