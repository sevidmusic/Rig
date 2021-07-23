<?php

namespace rig\interfaces\ui;

interface UserInterface {

    public function showMessage(string $message): void;

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showOptions(array $arguments): void;

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showFlags(array $arguments): void;

}
