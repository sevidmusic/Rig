<?php

namespace ddms\interfaces\command;

interface Command {

    /**
     * @param array<int, string> $argv An array of values to be interpreted as arguments.
     *                                 This will typically be the $argv array populated by
     *                                 PHP.
     *                                 This array should be a single-dimensional array of strings
     *                                 with numeric indexes.
     *                                 https://www.php.net/manual/en/reserved.variables.argv.php
     */
    public function run(array $argv): bool;

    /**
     * @param array<int, string> $argv An array of values to be interpreted as arguments.
     *                                 This will typically be the $argv array populated by
     *                                 PHP.
     *                                 https://www.php.net/manual/en/reserved.variables.argv.php
     *                                 This method is intended to provide implementations a way
     *                                 to prepare the $argv array appropriately for their use case.
     *                                 If no preparation is required, this method MUST return the
     *                                 $argv array unmodified.
     *
     * @return array<int|string,string> Either an array modified appropriately for the implementation,
     *                                  or, if no preparation is required by the implementation, the
     *                                  $argv array.
     *                                  This can be a numerically or associatively indexed single or
     *                                  multi-dimensional array so long as all values are strings.
     */
    public function prepareArguments(array $argv): array;

}
