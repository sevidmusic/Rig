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

### `rig --version`

`rig --version` will display `rig`'s version number.

Example:

```
rig --version
```

### `rig --new-roady-project --path-to-roady-project`

`rig --new-roady-project` will clone [Roady](https://github.com/sevidmusic/Roady)
to the specified `--path-to-roady-project`, and setup the new Roady
project.

Example:

```
rig --new-roady-project --path-to-roady-project ./
```


### `rig --new-roady-module --path-to-roady-project [--generate-boilerplate]`

`rig --new-roady-module` will create a new module for the specified
[Roady](https://github.com/sevidmusic/Roady) project.

If the `--generate-boilerplate` flag is not specified,
then only the modules directory structure, a `README.md`,
and a `localhost.8080.json` will be created for the
new module.

Example

```
--new-roady-module
--path-to-roady-project ./ \
--module-name new-module-with-boilerplate \
```
```sh
Roady/modules/new-module-with-no-boilerplate/:
assets  css  js  localhost.8080.json  output  README.md
```

If the `--generate-boilerplate` flag is specified, then some initial
files will be created to make it easier to get started developing
the module.

Example

```
--new-roady-module
--path-to-roady-project ./ \
--module-name new-module-with-boilerplate \
--generate-boilerplate
```

Would generate a module with a directory the following structure:


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

```
