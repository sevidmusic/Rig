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
- [Getting Started](#getting-started)

- [Commands](#commands)

- [--help](#rig---help)
- [--delete-route](#rig---delete-route)
- [--list-routes](#rig---list-routes)
- [--new-module](#rig---new-module)
- [--new-route](#rig---new-route)
- [--start-servers](#rig---start-servers)
- [--update-route](#rig---update-route)
- [--version](#rig---version)
- [--view-action-log](#rig---view-action-log)

# About

[rig](https://github.com/sevidmusic/rig) is a command line utility
designed to aide in development with the
[Roady](https://github.com/sevidmusic/roady) php framework.

# Installation

`rig` can be installed via one of the following installation methods:

Via `composer`:

```sh
composer require darling/rig
```

Note: If `rig` is installed via `composer`, then the `rig` and
`rig.php` scripts will be placed in the root directory
of the project that `composer require` was run for.

For example, if the project's directory contained the
following files and directories before installing `rig`:

```
composer.json
```

Then after running `composer require darling/rig` the project's
directory will contain the following files and directories:

```
composer.json
rig
rig.php

```

Via `git`:

```sh
git clone https://github.com/sevidmusic/rig
```

### Post `git clone` Installation Steps

Note: The post installation steps described in this
section should only be taken if `rig` was installed
manually via `git clone https://github.com/sevidmusic/rig`.

Creating a symlink to `rig` in `~/.local/bin` will make it easier to
use a version of `rig` that was installed via `git clone`.

`rig` provides a `setup.sh` script that will do just that.

After installing with `git clone`, move into `rig`'s root directory
and 'run:

```sh
./setup.sh
```

Note:

`setup.sh` will not overwrite an existing `rig` symlink by default.

To force `setup.sh` to overwrite an existing `rig` symlink use
the `--force` flag:

```sh
./setup.sh --force
```

# Getting Started

To make sure rig is is installed and callable, run the
following command:

```sh
rig --version
```

If that worked, then `rig` is installed properly.

### Creating a Module

`rig` can be used to create new Module for a `Roady` project.

For example, to create a Module named `hello-world` run the
following command:

```sh
rig --new-module \
    --module-name "hello-world"
```

This will create a Module named `hello-world` in the current
`Roady` project's `modules` directory.

```
modules/hello-world
```

It will also create the following files:

```
modules/hello-world/output/hello-world.html

modules/hello-world/localhost.8080.json
```

To use this Module, start a development server on `localhost:8080`
via `rig --start-servers --open-in-browser`.

Note: If [localhost:8080](http://localhost:8080) does not open
in a browser automatically, then manually open a web browser
and navigate to [localhost:8080](http://localhost:8080).

If everything is working then the new module's output,
`Hello hello-world`, should be displayed in the browser.

# Additional Documentation

Documentation relevant to `rig`'s  individual commands can be
found below.

More thorough documentation about using `rig` with `Roady` to build
websites can be found in [Roady's](https://github.com/sevidmusic/roady)
`README.md`.

# Commands

### `rig --help`

Display documentation for `rig`, or one of `rig`'s commands.

Arguments:

```
[COMMAND_NAME]
```

Examples:

```sh
rig --help

rig --help delete-route

rig --help list-routes

rig --help new-module

rig --help new-route

rig --help start-servers

rig --help update-route

rig --help version

rig --help view-action-log
```

### `rig --delete-route`

Delete the Route that is assigned the specified Route hash.

Note: Route hashes are displayed in the output of `rig --list-routes`.

Arguments:

```
--path-to-roady-project     The path to the relevant Roady project's
                            root directory.

                            Defaults to current directory: ./

--route-hash                The hash of the Route to delete.
```

Examples:

```sh
rig --delete-route \
--route-hash "016bbd46d3a3fc25c"

rig --delete-route \
--path-to-roady-project "./" \
--route-hash "6591c3e1ed38ed5eb"
```

### `rig --list-routes`

List the Routes configured by existing Modules.

Note: If no arguments are specified, all of the
Routes defined by all existing Modules will be
included in the list.

Arguments:

```
--defined-for-authorities     If specified, only list Routes
                              that are defined for one of the
                              specified Authorities.

--defined-for-modules         If specified, only list Routes
                              that are defined by one of the
                              specified Modules.

--defined-for-requests        If specified, only list the
                              Routes that respond to one of
                              the specified Requests.

--defined-for-named-positions If specified, only list Routes that
                              are defined for one of the specified
                              Named Positions.

--defined-for-positions       If specified, only list Routes
                              that are defined for one of the
                              specified Positions.

--defined-for-files           If specified, only list Routes that
                              are defined for one of the specified
                              files.

--path-to-roady-project       The path to the relevant Roady project's
                              root directory.

                              Defaults to current directory: ./

```

Examples:

```sh
rig --list-routes

rig --list-routes --defined-for-modules "hello-world" "hello-universe"

rig --list-routes --defined-for-requests "homepage" "global"

rig --list-routes --defined-for-named-positions "roady-ui-header"

rig --list-routes --defined-for-positions "0" "3"

rig --list-routes --defined-for-files "homepage.html" "global.css"

```
It is also possible to specify multiple arguments to further
filter the results.

```sh
rig --list-routes \
    --defined-for-modules "hello-world" "hello-universe" \
    --defined-for-requests "homepage" "global" \
    --defined-for-named-positions "roady-ui-header" \
    --defined-for-positions "0" "3" \
    --defined-for-files "homepage.html" "global.css"
```

The output of `rig --list-routes` will look something like:

```sh
  # Routes

 ┌───────────────────┬─────────────────────────────────────────────────────┐
 │ route-hash:       │ 016bbd46d3a3fc25c                                   │
 ├───────────────────┼─────────────────────────────────────────────────────┤
 │ defined-by-module │ hello-world                                         │
 │ responds-to       │ homepage                                            │
 │ named-positions   │ [{"position-name":"roady-ui-footer","position":10}] │
 │ relative-path     │ output/hello-world.html                             │
 └───────────────────┴─────────────────────────────────────────────────────┘


 ┌───────────────────┬────────────────────────────────────────────────────┐
 │ route-hash:       │ 6591c3e1ed38ed5eb                                  │
 ├───────────────────┼────────────────────────────────────────────────────┤
 │ defined-by-module │ hello-world                                        │
 │ responds-to       │ hello-universe, hello-world, homepage              │
 │ named-positions   │ [{"position-name":"roady-ui-header","position":3}] │
 │ relative-path     │ output/header.html                                 │
 └───────────────────┴────────────────────────────────────────────────────┘
```

### `rig --new-module`

Create a new Module in the current Roady project's `modules`
directory.

If the `--no-boilerplate` flag is not specified,
the following initial files and directories
will be created for the new Module.

Note: The name `NEW_MODULE_NAME` will be replaced
by the new Module's actual name.

```
localhost.8080.json
output/NEW_MODULE_NAME.html
```

The content of the initial files created for the new Module will be:

- `output/NEW_MODULE_NAME.html`

```html
<p>Hello NEW_MODULE_NAME</p>
```

- `localhost.8080.json`

  Note: The string `NEW_MODULE_NAME` in the example `json`
  will be replaced by the new Module's actual name.

```json
[
    {
        "module-name": "NEW_MODULE_NAME",
        "responds-to": [
            "homepage"
        ],
        "named-positions": [
            {
                "position-name": "roady-ui-main-content",
                "position": 0
            }
        ],
        "relative-path": "output\/NEW_MODULE_NAME.html"
    }
]
```

Arguments:

```
--for-authority             If specified, create an initial Route
                            configuration file for the specified
                            domain authority.

                            Note: If the --for-authority flag is
                            not specified then an initial Route
                            configuration file will be created
                            for the authority:

                            localhost:8000

                            Note: If the --no-boilerplate flag is
                            specified and the --for-authority flag
                            is not specified then an initial Route
                            configuration file will not be created.

--no-boilerplate            If specified, do not create any
                            initial files and directories for
                            the new Module.

--module-name               The name to assign to the new Module.

--path-to-roady-project     The path to the relevant Roady project's
                            root directory.

                            Defaults to current directory: ./
```

Examples:

```sh

rig --new-module \
    --module-name hello-world

rig --new-module \
    --module-name hello-universe \
    --for-authority "www.example.com"

rig --new-module \
    --module-name hello-multiverse \
    --for-authority "localhost:8888" \
    --no-boilerplate \
    --path-to-roady-project "./"
```

### `rig --new-route`

Define a new Route for an existing Module.

Arguments:

```
--module-name               The name of the Module to define the new
                            Route for.

--named-positions           A json string that represents an array
                            of arrays of named positons.

                            For example:

                            [{"position-name":"roady-ui-footer","position":10}]

--path-to-roady-project     The path to the relevant Roady project's
                            root directory.

                            Defaults to current directory: ./

--relative-path             The path to the file served by the Route,
                            relative to the Module's root directory.

--responds-to               The names of the Requests the Route will
                            respond to.
```

Examples:

```sh
rig --new-route \
--module-name "hello-world" \
--named-positions '[{"position-name":"roady-ui-footer","position":10}]' \
--relative-path "output/hello-world.html" \
--responds-to "homepage" "hello-world"
```

### `rig --start-servers`

Start up one or more local servers on the specified ports.

If no ports are specified then start a local server on port `8080`.

The servers will be available at `localhost:PORT`, for example,
`rig --start-server 8888` would start a local server that would be
accsessible at [localhost:8888](http://localhost:8888).

Arguments:

```
--ports           The ports to start servers on.

--open-in-browser If specified, attempt to automatically open
                  the running servers in a browser.
```

Examples:

```sh
# start server on localhost:8080
rig --start-servers

# start server on localhost:8888 and localhost:8017
rig --start-servers --ports 8888 8017

# start server on localhost:8420 and open it in a browser
rig --start-servers --ports 8420 --open-in-browser
```

### `rig --update-route`

Update the Route that is assigned the specified Route hash using
the specified criteria.

Note: Route hashes are displayed in the table produced by
`rig --list-routes`.

Arguments:

```
--module-name               The name of the Module the Route is
                            defined for.

--named-positions           A json string that represents an array
                            of arrays of named positons.

                            For example:

                            [{"position-name":"roady-ui-footer","position":10}]

--path-to-roady-project     The path to the relevant Roady project's
                            root directory.

                            Defaults to current directory: ./

--relative-path             The path to the file served by the Route,
                            relative to the Module's root directory.

--responds-to               The names of the additional Requests
                            the Route will respond to.

--route-hash                The hash of the Route to update.
```

Examples:

```sh
rig --update-route \
--module-name "hello-world" \
--named-positions '[{"position-name":"roady-ui-footer","position":10}]' \
--relative-path "output/hello-world.html" \
--responds-to "homepage" "hello-world"
```

### `rig --version`

Display rig's version.

Note: If `rig` is not up to date, a warning message will be shown.

Examples:

```sh
rig --version
```

### `rig --view-action-log`

View the log of Actions that have been taken by the commands run
by rig.

Examples:

```sh
rig --view-action-log
```

