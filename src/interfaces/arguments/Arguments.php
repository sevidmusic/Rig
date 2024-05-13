<?php

namespace Darling\Rig\interfaces\arguments;

/**
 * Description of this interface.
 *
 */
interface Arguments
{

    /** @return array<string, string> */
    public function asArray(): array;

    /** @return array<mixed> */
    public function specifiedArgumentData(): array;
}

