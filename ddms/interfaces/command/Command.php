<?php

namespace ddms\interfaces\command;

use ddms\interfaces\ui\UserInterface;

interface Command {

    /**
     * @param UserInterface $userInterface
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool;

    /**
     * @param array<mixed> $argv
     * @see https://www.php.net/manual/en/reserved.variables.argv.php
     *
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    public function prepareArguments(array $argv): array;

}
