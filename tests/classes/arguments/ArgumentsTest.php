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
     * The ArgumentsTestTrait defines
     * common tests for implementations of the
     * Darling\Rig\interfaces\arguments\Arguments
     * interface.
     *
     * @see ArgumentsTestTrait
     *
     */
    use ArgumentsTestTrait;

    public function setUp(): void
    {
        $testArgumentData = [
            // Valid argument data
            '--new-module',
            '--module-name' => 'hello-wolrd',
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
            $this->randomObjectInstance(),
            $this->randomFloat(),
            $this->randomClassStringOrObjectInstance(),
        ];
        $this->setExpectedSpecifiedArgumentData($testArgumentData);
        $this->setArgumentsTestInstance(
            new Arguments($testArgumentData)
        );
    }
}

