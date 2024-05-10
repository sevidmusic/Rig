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

    private int $expectedSucceededColor = 83;
    private int $expectedFailedColor = 160;
    private int $expectedNotProcessedColor = 250;
    private int $expectedHighlightColor = 67;

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

    /**
     * Test applyANSIColor applies formatting for the specified color.
     *
     * @return void
     *
     */
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

    /**
     * Test static call to applyANSIColor applies formatting for
     * specified color.
     *
     * @return void
     *
     */
    public function test_static_call_to_applyANSIColor_applies_formatting_for_specified_color(): void
    {
        $string = $this->randomString();
        $colorCode = rand(0, 128);
        $expectedString = $this->applyExpectedANSIColor(
            $string, $colorCode
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::class::applyANSIColor(
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

    /**
     * Test applySUCCEEDEDColor applies formatting for success
     * color.
     *
     * @return void
     *
     */
    public function test_applySUCCEEDEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedSucceededColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedSucceededColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()->applySUCCEEDEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applySUCCEEDEDColor',
                'applies ANSI color ' . $this->expectedSucceededColor
            ),
        );
    }

    /**
     * Test static call to applySUCCEEDEDColor applies formatting
     * for success color.
     *
     * @return void
     *
     */
    public function test_static_call_to_applySUCCEEDEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedSucceededColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedSucceededColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::class::applySUCCEEDEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applySUCCEEDEDColor',
                'applies ANSI color ' . $this->expectedSucceededColor
            ),
        );
    }

    /**
     * Test applyFAILEDColor applies formatting for success
     * color.
     *
     * @return void
     *
     */
    public function test_applyFAILEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedFailedColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedFailedColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()->applyFAILEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyFAILEDColor',
                'applies ANSI color ' . $this->expectedFailedColor
            ),
        );
    }

    /**
     * Test static call to applyFAILEDColor applies formatting for
     * success color.
     *
     * @return void
     *
     */
    public function test_static_call_to_applyFAILEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedFailedColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedFailedColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::class::applyFAILEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyFAILEDColor',
                'applies ANSI color ' . $this->expectedFailedColor
            ),
        );
    }


    /**
     * Test applyNOR_PROCESSEDColor applies formatting for success
     * color.
     *
     * @return void
     *
     */
    public function test_applyNOT_PROCESSEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedNotProcessedColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedNotProcessedColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()->applyNOT_PROCESSEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyNOT_PROCESSEDColor',
                'applies ANSI color ' . $this->expectedNotProcessedColor
            ),
        );
    }

    /**
     * Test static call to applyNOR_PROCESSEDColor applies
     * formatting for success color.
     *
     * @return void
     *
     */
    public function test_static_call_to_applyNOT_PROCESSEDColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedNotProcessedColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedNotProcessedColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::class::applyNOT_PROCESSEDColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyNOT_PROCESSEDColor',
                'applies ANSI color ' . $this->expectedNotProcessedColor
            ),
        );
    }

    /**
     * Test applyHighlightColor applies formatting for success
     * color.
     *
     * @return void
     *
     */
    public function test_applyHighlightColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedHighlightColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedHighlightColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()->applyHighlightColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyHighlightColor',
                'applies ANSI color ' . $this->expectedHighlightColor
            ),
        );
    }


    /**
     * Test static call to applyHighlightColor applies formatting
     * for success color.
     *
     * @return void
     *
     */
    public function test_static_call_to_applyHighlightColor_applies_formatting_for_success_color(): void
    {
        $string = $this->randomString();
        $colorCode = $this->expectedHighlightColor;
        $expectedString = $this->applyExpectedANSIColor(
            $string,
            $this->expectedHighlightColor
        );
        $this->assertEquals(
            $expectedString,
            $this->cLIColorizerTestInstance()::class::applyHighlightColor(
                $string,
            ),
            $this->testFailedMessage(
                $this->cLIColorizerTestInstance(),
                'applyHighlightColor',
                'applies ANSI color ' . $this->expectedHighlightColor
            ),
        );
    }
    protected abstract function randomString(): string;
    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;

}

