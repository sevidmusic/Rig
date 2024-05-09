<?php

namespace Darling\Rig\interfaces\messages;

use \Darling\PHPTextTypes\interfaces\strings\Text;

/**
 * A Message is a string that is intended to be added to a MessageLog.
 */
interface Message extends Text
{

    /**
     * Return the message as a string.
     *
     * @return string
     *
     */
    public function __toString(): string;

}

