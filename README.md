```sh
 ____  _
|  _ \(_) ____
| |_) | |/ _` |
|  _ <| | (_| |
|_| \_\_|\__, |
         |___/
```

- [About](#about)

- [Installation](#installation)

- [Commands](#commands)

- [--help](#--help)
- [--list-routes](#--list-routes)
- [--new-module](#--new-module)
- [--new-route](#--new-route)
- [--remove-route](#--remove-route)
- [--update-route](#--update-route)
- [--version](#--version)

# About

[rig](https://github.com/sevidmusic/rig) is a command line utiltiy
designed to aide in development with the
[Roady](https://github.com/sevidmusic/Roady) php framework.

# Installation

It is not necessary to manually install
`rig` if `roady` is installed.

`rig` is a dependency of `roady`
and will be installed via `composer` when `roady`
is installed via `composer require darling/roady`.

It is best to use the version of
`rig`
that is packaged with the version
of `roady`
being used.

For niche use cases that require
`rig`
be installed on independently, one of the
following methods may be used:

Via `composer`:

```
composer require darling/rig
```

Via `git`:

```
git clone https://github.com/sevidmusic/rig
```

# Post Manual Installation

Note: The post installation steps described in this
section should only be taken if `rig` is installed
manually via `composer require darling/rig` or
`git clone https://github.com/sevidmusic/rig`.

To make it easier to use `rig`,
it's good to create a symlink to `rig`
in `~/.local/bin`.

The `setup.sh` script will do just that.

After installation, run:

```sh
./setup.sh
```

Note:

`setup,sh` will not overwrite an existing `rig` symlink
by default.

To force `setup.sh` to overwrite an existing `rig` symlink,
use the `--force` flag:

```sh
./setup.sh --force
```

# Commands

### --version

`rig --version` will display `rig`'s
version number.

It will also display a warning if `rig` is out of date.

Examples:

```sh
rig --version
```

### --new-module

`rig --new-module` will create a new module in the
specified `roady`g project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

                        Defaults to current directory.

                        For example:

                        --path-to-roady-project "./"

--module-name           The name to assign to the new module.

                        For example:

                        --module-name "hello-world"

                        Note:

                        This name must be unique, it is not possible
                        to use the name of an existing module.

--authority             The domain authority that the new Route
                        will be configured for.

                        For example:

                        --authority "localhost:8080"

--generate-boilerplate  If specified, generate the following initial
                        directories and files for the module:

                        - AUTHORITY.json
                        - README.md
                        - assets/
                        - css/
                        - css/NEW_MODULES_NAME.css
                        - js/
                        - js/NEW_MODULES_NAME.js
                        - output/
                        - output/NEW_MODULES_NAME.html
                        - output/NEW_MODULES_NAME.php
```

Examples:

```sh

rig --new-module \
    --path-to-roady-project ./ \
    --module-name Foo \
    --authority localhost:8080

rig --new-module \
    --path-to-roady-project ./ \
    --module-name Bar \
    --authority localhost:8888 \
    --generate-boilerplate

```

### --new-route

`rig --new-route` will configure a new Route for a module in
the specified `roady`g project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

                        Defaults to current directory.

                        For example:

                        --path-to-roady-project "./"

--for-module            The name of the module to configure the
                        Route for.

                        For example:

                        --for-module "hello-world"

--authority             The domain authority that the new Route
                        will be configured for.

                        For example:

                        --authority "localhost:8080"

--named-positions      The named-positions to assign the Route to.
                       named-positions should be specified as json
                       arrays of "name position" pairs.

                       For example:

                       --named-positions '["roady-ui-header", 1]' \
                                         '["roady-ui-footer", 7]'

--responds-to-requests The names of the Request the Route should
                       be served in Response to.

                       For example:

                       --responds-to-requests "Foo" "Bar" "Baz"

--relative-path-to-output-file The path to the file that is served
                               when the Route is requested,
                               The path should be relative to the
                               relevant module's root directory.

                       For example:

                       --relative-path-to-output-file "output"
```

Examples:

```sh
rig --new-route \
    --path-to-roady-project "./" \
    --for-module "hello-world" \
    --authority "localhost:8080" \
    --named-positions "[roady-ui-header, 0]" "[roady-ui-footer, 7]" \
    --responds-to-requests "Foo" "Bar" "Baz" \
    --relative-path-to-output-file "output/hello-world.php"
```

### --update-route

`rig --update-route` will update an existing Route for a module in
the specified `roady`g project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

                        Defaults to current directory.

                        For example:

                        --path-to-roady-project "./"

--for-module            The name of the module to update the
                        Route for.

                        For example:

                        --for-module "hello-world"

--authority             The domain authority that the new Route
                        will be configured for.

                        For example:

                        --authority "localhost:8080"

--add-named-positions  The named-positions to assign the Route to the
                       Route in addition to the already assigned
                       named positions.

                       named positions should be specified as json
                       arrays of "name position" pairs.

                       For example:

                       --add-named-positions '["foo", 1]' \
                                             '["bar", 7]' \


--remove-named-positions The names of the named-positions to remove
                         from the Route.

                         For example:

                         --remove-named-positions "roady-ui-header" "roady-ui-footer"

--add-requests         The names of the additional Requests the Route
                       should be served in Response to.

                       For example:

                       --add-requests "Foo" "Bar" "Baz"

--remove-requests      The names of the Requests to remove from the
                       Routes definition.

                       For example:

                       --remove-requests "Foo" "Bar" "Baz"

--relative-path-to-output-file The path to the file served by the
                               Route, relative to the relevant
                               modules root directory.

                       For example:

                       --relative-path-to-output-file "output"
```

Examples:

```sh
rig --update-route \
    --path-to-roady-project "./" \
    --for-module "hello-world" \
    --authority "localhost:8080" \
    --add-named-positions "[roady-ui-main-content, 0]"
    --remove-named-positions "roady-ui-header" "roady-ui-footer" \
    --responds-to-requests "Foo" "Bar" "Baz" \
    --relative-path-to-output-file "output/hello-world.html"
```

### --remove-route

`rig --remove-route` will remove an existing Route from a module in
the specified `roady`g project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

                        Defaults to current directory.

                        For example:

                        --path-to-roady-project "./"

--for-module            The name of the module to update the
                        Route for.

                        For example:

                        --for-module "hello-world"

--authority             The domain authority that the Route to be
                        removed was configured for.

                        For example:

                        --authority "localhost:8080"

--relative-path-to-output-file The path to the file served by the
                               Route, relative to the relevant
                               modules root directory.

                       For example:

                       --relative-path-to-output-file "output/hello-world.html"
```

Examples:

```sh
rig --remove-route \
    --path-to-roady-project "./" \
    --for-module "hello-world" \
    --authority "localhost:8080" \
    --relative-path-to-output-file "output/hello-world.html"
```

### --list-routes

`rig --list-routes` will list the Routes defined by one or more module
in the specified `roady`g project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project   The path to the root directory of the Roady
                          project to create the new module for.

                          Defaults to current directory.

                          For example:

                          --path-to-roady-project "./"

--defined-by-modules      If specified, only include Routes defined by
                          the specified modules.

                          For example:

                          --defined-by-modules "hello-world"

--defined-for-authorities If specified, only include Routes defined
                          for the specified domain authorites.

                          For example:

                          --defined-for-authorities "localhost:8080"

--responds-to-requests    If specified, only include Routes that respond
                          to one of the specified Requests.

                          For example:

                          --responds-to-requests "Foo" "Bar" "Baz"
```

Examples:

```sh
rig --list-routes \
    --path-to-roady-project "./" \
    --defined-by-modules "hello-world" \
    --defined-for-authorities "localhost:8080" \
    --responds-to-requests "homepage"
```

