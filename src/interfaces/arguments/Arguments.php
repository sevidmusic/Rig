<?php

namespace Darling\Rig\interfaces\arguments;

/**
 * Description of this interface.
 *
 */
interface Arguments
{

    /**
     * Return an array whose keys are defined by the
     * RigCommand enum, and RigCommandArgument enum's
     * cases.
     *
     * The values of the array will be derived from the
     * array returned by the specifiedArgumentData() method,
     * and will be indexed by the RigCommand or RigCommandArgument
     * they are associated with.
     *
     * Only items whose value or key match a RigCommand
     * case or RigCommandArgument case will be included
     * in the returned array.
     *
     * Note: All RigCommand and RigCommandArgument cases will
     * be represented in the returned array. Any cases that do
     * not have a corresponding value in the array returned by
     * the specifiedArgumentData() method will be assigned an
     * empty string.
     *
     * For example, if the array of $specifiedArgumentData was:
     *
     * ```
     * array(3) {
     *   [0]=>
     *   string(12) "--new-module"
     *   ["--module-name"]=>
     *   string(11) "hello-wolrd"
     *   ["--path-to-roady-project"]=>
     *   string(21) "/home/darling/Git/Rig"
     * }
     * ```
     *
     * Then the returned array will be:
     *
     * ```
     * {
     *   ["--delete-route"]=>
     *   string(0) ""
     *   ["--help"]=>
     *   string(0) ""
     *   ["--list-routes"]=>
     *   string(0) ""
     *   ["--new-module"]=>
     *   string(12) "--new-module"
     *   ["--new-route"]=>
     *   string(0) ""
     *   ["--start-servers"]=>
     *   string(0) ""
     *   ["--update-route"]=>
     *   string(0) ""
     *   ["--version"]=>
     *   string(0) ""
     *   ["--view-action-log"]=>
     *   string(0) ""
     *   ["--view-readme"]=>
     *   string(0) ""
     *   ["--authority"]=>
     *   string(0) ""
     *   ["--defined-for-authorities"]=>
     *   string(0) ""
     *   ["--defined-for-files"]=>
     *   string(0) ""
     *   ["--defined-for-modules"]=>
     *   string(0) ""
     *   ["--defined-for-named-positions"]=>
     *   string(0) ""
     *   ["--defined-for-positions"]=>
     *   string(0) ""
     *   ["--defined-for-requests"]=>
     *   string(0) ""
     *   ["--for-authority"]=>
     *   string(0) ""
     *   ["--module-name"]=>
     *   string(11) "hello-wolrd"
     *   ["--named-positions"]=>
     *   string(0) ""
     *   ["--no-boilerplate"]=>
     *   string(0) ""
     *   ["--open-in-browser"]=>
     *   string(0) ""
     *   ["--path-to-roady-project"]=>
     *   string(21) "/home/darling/Git/Rig"
     *   ["--ports"]=>
     *   string(0) ""
     *   ["--relative-path"]=>
     *   string(0) ""
     *   ["--responds-to"]=>
     *   string(0) ""
     *   ["--route-hash"]=>
     *   string(0) ""
     * }
     * ```
     *
     * @return array<string, string>
     *
     */
    public function asArray(): array;

    /**
     * Return the specified argument data unmodified.
     *
     * @return array<mixed>
     */
    public function specifiedArgumentData(): array;
}

