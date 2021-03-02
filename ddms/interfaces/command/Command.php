<?php

namespace ddms\interfaces\command;

interface Command {

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    public function run(array $preparedArguments): bool;

    /**
     * @param array<mixed> $argv
     * @see https://www.php.net/manual/en/reserved.variables.argv.php
     *
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    public function prepareArguments(array $argv): array;

}
