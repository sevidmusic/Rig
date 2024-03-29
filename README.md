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

- [--help](#rig---help)
- [--list-routes](#rig---list-routes)
- [--new-module](#rig---new-module)
- [--new-route](#rig---new-route)
- [--remove-route](#rig---remove-route)
- [--update-route](#rig---update-route)
- [--version](#rig---version)
- [--view-action-log](#rig---view-action-log)

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
that was installed with the version
of `roady`
being used.

For niche use cases that require
`rig`
be installed independently one of the
following installation methods may be used:

Via `composer`:

```
composer require darling/rig
```

Via `git`:

```
git clone https://github.com/sevidmusic/rig
```

# Post Manual Installation Steps

Note: The post installation steps described in this
section should only be taken if `rig` was installed
manually via `composer require darling/rig` or
`git clone https://github.com/sevidmusic/rig`.

To make it easier to use a manually installed version
of `rig`, it's good to create a symlink to `rig` in
`~/.local/bin`.

The `setup.sh` script will do just that.

After manual installation, run:

```sh
./setup.sh
```

Note:

`setup,sh` will not overwrite an existing `rig` symlink
by default.

To force `setup.sh` to overwrite an existing `rig` symlink
use the `--force` flag:

```sh
./setup.sh --force
```

# Commands

### `rig --help`

Display documentation for rig or one of `rig`'s commands.

Arguments:

```sh
[COMMAND_NAME]
```

Examples:

```sh
rig --help

rig --help list-routes

rig --help new-module

rig --help new-route

rig --help remove-route

rig --help update-route

rig --help version

rig --help view-action-log

```

### `rig --list-routes`

List the Routes configured by existing modules.

Note: If no arguments are specified, all of the
Routes defined by all existing modules will be
included in the list.

Arguments:

```
--defined-for-modules         If specified, only list Routes
                              that are defined for one of the
                              specified modules.

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

--path-to-roady-project      The path to the relevant Roady project's
                             root directory.

```

Examples:

```sh
rig --list-routes

rig --list-routes --defined-for-modules "hello-world" "hello-universe"

rig --list-routes --defined-for-requests "homepage" "global"

rig --list-routes --defined-for-named-positions "roady-ui-header"

rig --list-routes --defined-for-positionss "0" "3"

rig --list-routes --defined-for-files "homepage.html" "global.css"

```
It is also possible to specify multiple arguments to further
filter the results.

```sh
rig --list-routes \
    --defined-for-modules "hello-world" "hello-universe" \
    --defined-for-requests "homepage" "global" \
    --defined-for-named-positions "roady-ui-header" \
    --defined-for-positionss "0" "3" \
    --defined-for-files "homepage.html" "global.css"
```

### `rig --new-module`

Create a new module in the current Roady project's `modules`
directory.

If the `--no-boilerplate` flag is not specified,
the following initial files and directories
will be created for the new module.

Note: The name `NEW_MODULE_NAME` will be replaced
by the new module's actual name.

```
localhost.8080.json
output/NEW_MODULE_NAME.html
```

The content of the initial files created for the new module will be:

- output/NEW_MODULE_NAME.html

```html
<p>Hello world</p>
```

- localhost.8080.json

  Note: The string `NEW_MODULE_NAME` in the example `json`
  will be replaced by the new module's actual name.

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
--for-authority             If specified, generate a Route
                            configuration file for the specified
                            domain authority.

                            Note: If the --for-authority flag is not
                            specified then a Route configuration
                            file will be generated for the authority:

                            localhost:8000

--no-boilerplate            If specified, do not generate any
                            initial files and directories for
                            the new module.

--name                      The name to assign to the new module.

--path-to-roady-project     The path to the relevant Roady project's
                            root directory.

```

Examples:

```sh
rig --new-module \
    --for-authority "www.example.com:8080" \
    --name hello-world \
    --path-to-roady-project "./"

rig --new-module \
    --for-authority "localhost:8080" \
    --name hello-universe \
    --no-boilerplate \
    --path-to-roady-project "./"
```

### `rig --new-route`

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --remove-route`

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --update-route`

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --version`

Display rig's version.

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
