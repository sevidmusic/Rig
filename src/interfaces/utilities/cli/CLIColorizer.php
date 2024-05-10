<?php

namespace Darling\Rig\interfaces\utilities\cli;

/**
 * A CLIColorizer can be used to format strings for a Terminal
 * using various colors for highlighting.
 *
 * This class is designed to format strings to be output
 * to a terminal, using this class in any other context
 * is harmless, though probably not appropriate.
 *
 */
interface CLIColorizer
{
    /**
     * Apply the specified ANSI $backgroundColorCode to the specified
     * $string as the background color:
     *
     *     `\033[48;5;{$backgroundColorCode}m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * @param string $string The string to apply color to.
     *
     * @param int $backgroundColorCode The color code to apply as the
     *                                 background color.
     *
     *                                 Color code range: 0 - 255
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applyANSIColor("Foo", rand(1, 255));
     *
     */
    public static function applyANSIColor(
        string $string,
        int $backgroundColorCode
    ): string;


    /**
     * Apply the ANSI color 83 to the specified
     * $string as the background color:
     *
     *     `\033[48;5;83m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * @param string $string The string to apply color to.
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applySUCCEEDEDColor("Foo");
     *
     */
    public static function applySUCCEEDEDColor(string $string): string;

    /**
     * Apply the ANSI color 160 to the specified $string as the
     * background color:
     *
     *     `\033[48;5;160m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * @param string $string The string to apply color to.
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applyFAILEDColor("Foo");
     *
     */
    public static function applyFAILEDColor(string $string): string;

    /**
     * Apply the ANSI color 250 to the specified $string as the
     * background color:
     *
     *     `\033[48;5;250m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * @param string $string The string to apply color to.
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applyNOT_PROCESSEDColor("Foo");
     *
     */
    public static function applyNOT_PROCESSEDColor(string $string): string;

    /**
     * Apply the ANSI color 67 to the specified $string as the
     * background color:
     *
     *     `\033[48;5;67m`
     *
     * Foreground color will be black:
     *
     *     `\033[38;5;0m`
     *
     * @param string $string The string to apply color to.
     *
     * @return string
     *
     * @example
     *
     * $cLIColorizer->applyHighlightColor("Foo");
     *
     */
    public static function applyHighlightColor(string $string): string;

}


