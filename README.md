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

- [--help](#rig--help)
- [--list-routes](#rig--list-routes)
- [--new-module](#rig--new-module)
- [--new-route](#rig--new-route)
- [--remove-route](#rig--remove-route)
- [--update-route](#rig--update-route)
- [--version](#rig--version)
- [--view-command-log](#rig--view-command-log)

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

### `rig --help`

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --list-routes`

Description...

Arguments:

```sh

```

Examples:

```sh
```

### `rig --new-module`

Description...

Arguments:

```sh

```

Examples:

```sh
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

### `rig --view-command-log`

Description...

Arguments:

```sh

```

Examples:

```sh
```

