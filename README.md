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
- [--view-log](#rig---view-log)

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
section should only be taken if `rig` is installed
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

To force `setup.sh` to overwrite an existing `rig` symlink,
use the `--force` flag:

```sh
./setup.sh --force
```

# Commands

### `rig --help`

Get information about rig or one of it's commands.

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

rig --help view-log

```

### `rig --list-routes`

List the Routes configured by existing modules.

If no arguments are specified, all of the Routes
defined by all existing modules will be included
in the list.

Arguments:

```sh
--defined-for-modules         If specified, only list Routes that are
                              defined for one of the specified modules.

--defined-for-requests        If specified, only list the Routes that
                              respond to one of the specified Requests.

--defined-for-named-positions If specified, only list Routes that
                              are defined for one of the specified
                              Named Positions.

--defined-for-position        If specified, only list Routes that are
                              defined for one of the specified Positions.

--defined-for-file            If specified, only list Routes that are
                              defined for the specified file.

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

### `rig --new-module`

Create a new module in the current Roady project's `modules`
directory.

By default, the new module will only contain the following
files and directories:

```sh
css
js
localhost.8080.json
output
```

The `--generate-boilerplate` flag can be used to generate some
initial files, and configure some initial Routes for the new
module.

If the `--generate-boilerplate` flag is specified, then the
new module will contain the following files and directories.

Note: NEW_MODULE_NAME will be replaced by the new module's
actual name.
```sh
css/NEW_MODULE_NAME.css
js/NEW_MODULE_NAME.js
localhost.8080.json
output/NEW_MODULE_NAME.html
```

If the `--generate-boilerplate` flag is specified, then the
content of the initial files will be:

- css/NEW_MODULE_NAME.css

```css

html {
    color-scheme: dark light;
}

@media (prefers-color-scheme: dark) {

    body {
        background: black;
        color: white;
    }

}

@media (prefers-color-scheme: light) {

    body {
        background: white;
        color: black;
    }

}

```

- js/NEW_MODULE_NAME.js

```js
console.log("Javascript was loaded.");
```

- output/NEW_MODULE_NAME.html

```html
<p>Hello world</p>
```

Arguments:

```sh
--for-authority             If specified, generate a Route
                            configuration file for the specified
                            domain authority.

                            Note: If the --for-authority flag is not
                            specified then a Route configuration
                            file will be generated for the authority:

                            localhost:8000

--generate-boilerplate      If specified, generate some initial files
                            and directories with boilerplate content.

--name                      The name to assign to the new module.

--path-to-roady-project     The path to the Roady project to create
                            the new module for.

```

Examples:

```sh
rig --new-module \
    --for-authority "www.example.com:8080" \
    --generate-boilerplate \
    --name hello-world \
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

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --view-log`

Description...

Arguments:

```sh

```

Examples:

```sh
```

