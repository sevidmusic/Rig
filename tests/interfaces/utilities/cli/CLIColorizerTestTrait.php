<?php

namespace Darling\Rig\tests\interfaces\utilities\cli;

use \Darling\Rig\interfaces\utilities\cli\CLIColorizer;
use \PHPUnit\Framework\Attributes\CoversClass;

/**
 * The CLIColorizerTestTrait defines common tests for
 * implementations of the CLIColorizer interface.
 *
 * @see CLIColorizer
 *
 */
#[CoversClass(CLIColorizer::class)]
trait CLIColorizerTestTrait
{

    /**
     * @var CLIColorizer $cLIColorizer
     *                              An instance of a
     *                              CLIColorizer
     *                              implementation to test.
     */
    protected CLIColorizer $cLIColorizer;

    /**
     * Set up an instance of a CLIColorizer implementation to test.
     *
     * This method must set the CLIColorizer implementation instance
     * to be tested via the setCLIColorizerTestInstance() method.
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
     *     $this->setCLIColorizerTestInstance(
     *         new \Darling\Rig\classes\utilities\cli\CLIColorizer()
     *     );
     * }
     *
     * ```
     *
     */
    abstract protected function setUp(): void;

    /**
     * Return the CLIColorizer implementation instance to test.
     *
     * @return CLIColorizer
     *
     */
    protected function cLIColorizerTestInstance(): CLIColorizer
    {
        return $this->cLIColorizer;
    }

    /**
     * Set the CLIColorizer implementation instance to test.
     *
     * @param CLIColorizer $cLIColorizerTestInstance
     *                              An instance of an
     *                              implementation of
     *                              the CLIColorizer
     *                              interface to test.
     *
     * @return void
     *
     */
    protected function setCLIColorizerTestInstance(
        CLIColorizer $cLIColorizerTestInstance
    ): void
    {
        $this->cLIColorizer = $cLIColorizerTestInstance;
    }
    private static function applyExpectedANSIColor(
        string $string,
        int $backgroundColorCode
    ): string {
        return "\033[0m" .      // reset color
            "\033[48;5;" .      // set background color to specified color
            strval($backgroundColorCode) . "m" .
            "\033[38;5;0m " .   // set foreground color to black
            $string .
            " \033[0m";         // reset color
    }

    public function test_applyANSIColor_applies_formatting_for_specified_color(): void
    {
        $string = $this->randomString();
        $colorCode = rand(0, 128);
        $expectedString = $this->applyExpectedANSIColor($string, $colorCode);
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()->applyANSIColor(
                $string,
                $colorCode,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyANSIColor',
                'applies formatting for the specified ANSI color'
            ),
        );
    }

    public function test_static_call_to_applyANSIColor_applies_formatting_for_specified_color(): void
    {
        $string = $this->randomString();
        $colorCode = rand(0, 128);
        $expectedString = $this->applyExpectedANSIColor($string, $colorCode);
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::applyANSIColor(
                $string,
                $colorCode,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyANSIColor',
                'applies formatting for the specified ANSI color' .
                'when called statically'
            ),
        );
    }

    protected abstract function randomString(): string;
    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;

}

