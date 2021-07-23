<?php

namespace rig\abstractions\ui;

use rig\interfaces\ui\UserInterface;

abstract class AbstractUserInterface implements UserInterface
{

    public function showMessage(string $message): void
    {
        echo $message;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    abstract public function showOptions(array $arguments): void;

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    abstract public function showFlags(array $arguments): void;

}
