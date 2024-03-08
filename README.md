```sh
 ____  _
|  _ \(_) ____
| |_) | |/ _` |
|  _ <| | (_| |
|_| \_\_|\__, |
         |___/
```

# About

`rig` is a command line utiltiy designed to aide in development
with the [Roady](https://github.com/sevidmusic/Roady) php
framework.

### Note:

`rig` is being re-written from scratch for [Roady](https://github.com/sevidmusic/Roady)
version `2.0`.

This `README.md` will continue to evolve as `rig` is developed.

# Installation

Via compoer:

```
composer require darling/rig
```

# Commands

```
rig --version
```

`rig --version` will display `rig`'s version number.

# Actions taken by this command:

1. Display `rig`'s version number.

```
rig --new-roady-project --path-to-roady-project ./
```

`rig --new-roady-project` will create a new Roady project at the
specified `--path-to-roady-project`.

# Actions taken by this command:

1. Clone [Roady](https://github.com/sevidmusic/Roady) into specified `--path-to-roady-project`.
2. Update composer for new [Roady](https://github.com/sevidmusic/Roady) project.
3. Start a web server on `localhost:8080` for new [Roady](https://github.com/sevidmusic/Roady) project.
   This will will run the `Hello World` module included with [Roady](https://github.com/sevidmusic/Roady).

### `--new-roady-module --path-to-roady-project --module-name --generate-boilerplate`

```sh
rig --new-roady-module \
    --name NameOfNewModule \
    --path-to-roady-project path/to/exisitng/directory
```

```sh
rig --new-roady-module \
    --name NameOfNewModule \
    --path-to-roady-project path/to/exisitng/directory \
    --generate-boilerplate
```

`rig --new-roady-module` will create a new module for the specified
[Roady](https://github.com/sevidmusic/Roady) project.

If the `--generate-boilerplate` flag is specified, then some initial
files will be created to make it easier to get started developing
the module:

```sh
Roady/modules/new-module-with-boilerplate/:
assets  css  js  localhost.8080.json  output  README.md

Roady/modules/new-module-with-boilerplate/assets:
roadyLogo.png

Roady/modules/new-module-with-boilerplate/css:
global_styles.css

Roady/modules/new-module-with-boilerplate/js:
global_scripts.js

Roady/modules/new-module-with-boilerplate/output:
global_roady-ui-pre-header.html
global_roady-ui-header.html
global_roady-ui-main-content.html
global_roady-ui-footer.html
```

If the `--generate-boilerplate` flag is not specified,
then the modules directory structure will be created,
a `READM`E.md will be created, and a `localhost.8080.json`
will be creaed.

```sh
Roady/modules/new-module-with-boilerplate/:
assets  css  js  localhost.8080.json  output  README.md
```
