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

- [--version](#--version)

- [--install-roady](#--install-roady)

- [--new-module](#--new-module)

# About

[rig](https://github.com/sevidmusic/rig) is a command line utiltiy
designed to aide in development with the
[Roady](https://github.com/sevidmusic/Roady) php framework.

Note: [rig](https://github.com/sevidmusic/rig) is not required by
[Roady](https://github.com/sevidmusic/Roady), nor is
[Roady](https://github.com/sevidmusic/Roady) required by
[rig](https://github.com/sevidmusic/rig)

# Installation

Via `composer`:

```
composer require darling/rig
```

Via `git`:

```
git clone https://github.com/sevidmusic/rig
```

# Post Installation

To make it easier to use [rig](https://github.com/sevidmusic/rig),
it's good to create a symlink to [rig](https://github.com/sevidmusic/rig)
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

`rig --version` will display [rig](https://github.com/sevidmusic/rig)'s
version number.

It will also display a warning if `rig` is out of date.

Examples:

```sh
rig --version
```

### --install-roady

`rig --install-roady` can be used to install
[Roady](https://github.com/sevidmusic/roady)
at a specified path.

Arguments:

```sh
--installation-path   The path to the directory where Roady should
                      be installed.

--run-composer-update If specified, composer update will be run
                      for the new Roady installation.
```

Examples:

```sh
rig --install-roady --installation-path ./

rig --install-roady --installation-path ~/ --run-composer-update
```

### --new-module

`rig --new-module` will create a new module in the
specified [Roady](https://github.com/sevidmusic/Roady) project's
`modules` directory.

Arguments:
```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

--name                  The name to assign to the new module.

--authority             The domain authority that the new modules
                        Routes will be initially configured for, for
                        example:

                        - localhost:8080
                        - www.example.com

--generate-boilerplate  If specified, generate the following initial
                        directories and files for the module:

                        - css/
                        - js/
                        - output/
                        - assets/
                        - css/NEW_MODULES_NAME.css
                        - output/NEW_MODULES_NAME.html
                        - AUTHORITY.json
```

Examples:

```sh
rig --new-module \
    --path-to-roady-project ./ \
    --name Foo \
    --authority localhost:8080 \
    --generate-boilerplate

```

### HERE


### --configure-route

`rig --configure-route` will configure a new Route for a module in
the specified [Roady](https://github.com/sevidmusic/Roady) project's
`modules` directory.

Arguments:

```sh
--path-to-roady-project The path to the root directory of the Roady
                        project to create the new module for.

                        For example:

                        --path-to-roady-project ./

--module-name           The name of the module to configure the
                        Route for.

                        For example:

                        --module-name hello-world

--authority             The domain authority that the new Route
                        will be configured for.

                        For example:

                        --authority localhost:8080

--named-positions      The named-positions to assign the Route to.
                       named-positions should be specified as json
                       arrays of "name position" pairs.

                       For example:

                       --named-positions '[roady-ui-header, 1]'


--responds-to-requests The names of the Request the Route should
                       be served in Response to.

                       For example:

                       --responds-to-requests Foo Bar Baz

--relative-path-to-output-file The path to the file served by the
                               Route, relative to the relevant
                               modules root directory.

                       For example:

                       --relative-path-to-output-file output

Examples:

```sh
rig --configure-route \
    --path-to-roady-project ./ \
    --module-name hello-world \
    --authority localhost:8080 \
    --named-positions "[roady-ui-header, 0]" "[roady-ui-footer, 7]" \
    --responds-to-requests Foo Bar Baz \
    --relative-path-to-output-file output
```
