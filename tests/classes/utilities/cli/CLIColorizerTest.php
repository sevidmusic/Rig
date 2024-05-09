<?php

namespace Darling\Rig\tests\classes\utilities\cli;

use \Darling\Rig\classes\utilities\cli\CLIColorizer;
use \Darling\Rig\tests\RigTest;
use \Darling\Rig\tests\interfaces\utilities\cli\CLIColorizerTestTrait;
use \PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CLIColorizer::class)]
class CLIColorizerTest extends RigTest
{

    /**
     * The CLIColorizerTestTrait defines
     * common tests for implementations of the
     * Darling\Rig\interfaces\utilities\cli\CLIColorizer
     * interface.
     *
     * @see CLIColorizerTestTrait
     *
     */
    use CLIColorizerTestTrait;

    public function setUp(): void
    {
        $this->setCLIColorizerTestInstance(
            new CLIColorizer()
        );
    }
}

