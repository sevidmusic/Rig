<?php

namespace Darling\Rig\classes\utilities\cli;

use \Darling\Rig\interfaces\utilities\cli\CLIColorizer as CLIColorizerInterface;

class CLIColorizer implements CLIColorizerInterface
{

    public static function applyANSIColor(string $string, int $backgroundColorCode): string
    {
        return "\033[0m" .      // reset color
            "\033[48;5;" .      // set background color to specified color
            strval($backgroundColorCode) . "m" .
            "\033[38;5;0m " .   // set foreground color to black
            $string .
            " \033[0m";
    }

    public static function applySUCCEEDEDColor(string $string): string
    {
        return '';
    }

    public static function applyFAILEDColor(string $string): string
    {
        return '';
    }

    public static function applyNOT_PROCESSEDColor(string $string): string
    {
        return '';
    }

    public static function applyHighlightColor(string $string): string
    {
        return '';
    }

}

