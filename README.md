```sh
 ____  _
|  _ \(_) ____
| |_) | |/ _` |
|  _ <| | (_| |
|_| \_\_|\__, |
         |___/
```

# About

`rig` is a command line utiltiy designed to aide in development with
the [Roady](https://github.com/sevidmusic/Roady) php framework.

Note: `rig` is not required by
[Roady](https://github.com/sevidmusic/Roady), nor is
[Roady](https://github.com/sevidmusic/Roady) required by `rig`.

# Installation

Via `composer`:

```
composer require darling/rig
```

# Commands

### --version

`rig --version` will display `rig`'s version number, and to check
if rig is up to date.

Arguments:
```sh
--is-up-to-date If specified, display whether or not the currently
                installed version of rig is up to date.
```

Examples:

```
rig --version

rig --version --is-up-to-date
```

### --install-roady

`rig --install-roady` can be used to install Roady at a specified path.

Arguments:

```sh
--installation-path The path to install Roady at.

--purge-no-world If specified, do not include the `hello-world`
                 module that typically comes with Roady.
```

Examples:

```sh
rig --install-roady --installation-path ./
```

