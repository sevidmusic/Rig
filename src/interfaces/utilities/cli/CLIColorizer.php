<?php

namespace Darling\Rig\interfaces\utilities\cli;

/**
 * Description of this interface.
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
     * Note: This function is designed to format strings to be output
     *       to a terminal, using this function in any other context
     *       is harmless, though probably not appropriate.
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


    public static function applySUCCEEDEDColor(string $string): string;

    public static function applyFAILEDColor(string $string): string;

    public static function applyNOT_PROCESSEDColor(string $string): string;

    public static function applyHighlightColor(string $string): string;
}


