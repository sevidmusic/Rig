```sh
 ____  _
|  _ \(_) ____
| |_) | |/ _` |
|  _ <| | (_| |
|_| \_\_|\__, |
         |___/
```

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

Via git:

```
git clone https://github.com/sevidmusic/rig
```

# Commands

### --version

`rig --version` will display [rig](https://github.com/sevidmusic/rig)'s
version number.

It can also be used to check if [rig](https://github.com/sevidmusic/rig)
is up to date.

Arguments:
```sh
--is-up-to-date If specified, display whether or not the currently
                installed version of rig is up to date.
```

Examples:

```sh
rig --version

rig --version --is-up-to-date
```

### --install-roady

`rig --install-roady` can be used to install
[Roady](https://github.com/sevidmusic/roady)
at a specified path.

Arguments:

```sh
--installation-path The path to install Roady at.
```

Examples:

```sh
rig --install-roady --installation-path ./
```

### --new-module

`rig --new-module` will create a new module in the
specified [Roady](https://github.com/sevidmusic/Roady) project's
`modules` directory.

Arguments:
```sh
--path-to-roady-project The path to the Roady project to create the
                        new module for.

--name                  The name to assign to the new module.

--domain                The initial domain that will be used to
                        configure the new modules Routes.

--generate-boilerplate  If specified, generate the following initial
                        directories and files for the module:

                        - css/
                        - js/
                        - output/
                        - assets/
                        - css/NEW_MODULES_NAME.css
                        - output/NEW_MODULES_NAME.html
```

Examples:

```sh
rig --new-module \
    --path-to-roady-project ./ \
    --name Foo \
    --domain localhost:8080 \
    --generate-boilerplate

```
