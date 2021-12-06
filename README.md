  ```
  rig  [--assign-to-response] [--configure-app-output]
       [--path-to-apps-directory] [--debug] [--help]
       [--make-app-package] [--new-app-package] [--new-app]
       [--new-dynamic-output-component] [--new-global-response]
       [--new-output-component] [--new-request] [--new-response]
       [--start-server] [--view-active-servers]
  ```

  ### Description:

  rig is a command line utility designed to aid in the development
  of [roady](https://roady.tech) Apps.

  rig's executable will be located at the following path relative to
  the relevant [roady](https://roady.tech) installation's root directory:

  ```
  ./vendor/darling/rig/bin/rig
  ```

  To use rig, rig must be in your `$PATH`.

  For example, if [roady](https://roady.tech) is installed at the path `~/roady`, then
  rig can be added to your `$PATH` via:

  ```
  export PATH="${PATH}:${HOME}/roady/vendor/darling/rig/bin"
  ```

  Note: This will only add rig to your `$PATH` temporarily, to
  add it permanently you will need to set your `$PATH` in the
  appropriate configuration file for the shell you are using.
  For information on setting your `$PATH` permanently, please
  consult the documentation for the shell you are using.

  WARNING: If you have multiple installations of [roady](https://roady.tech) on your
  system, make sure the path to the rig executable defined in
  your `$PATH` matches the path to the rig executable located
  in the vendor directory of the [roady](https://roady.tech) installation you are
  currently working with, or else rig may perform actions on
  the wrong [roady](https://roady.tech) installation.

  ### Flags:

  ### `[--assign-to-response]`

                                      Assign one or more Requests,
                                      OutputComponents, or
                                      DynamicOutputComponents to an
                                      existing Response or
                                      GlobalResponse.

                                      Note: For more information use:

                                      ```
                                      rig --help assign-to-response
                                      ```

  ### `[--configure-app-output]`

                                      Configure output for an App
                                      to be served in response to
                                      appropriate requests to any
                                      domains the App is built for.

                                      Note: For more information use:

                                      ```
                                      rig --help configure-app-output
                                      ```

  ### `[--path-to-apps-directory]`

                                      This flag can be used in
                                      conjunction with other rig
                                      flags to manually set the
                                      [roady](https://roady.tech) Apps directory path
                                      used by rig to an
                                      alternative Apps directory
                                      path.

                                      Note: For more information use:

                                      ```
                                      rig --help \
                                          path-to-apps-directory
                                      ```

  ### `[--debug]`

                                      Show debug information for the
                                      current call to rig.

                                      Note: For more information use:

                                      ```
                                      rig --help debug
                                      ```

  ### `[--help] [FLAG]`

                                      Show this help message, or
                                      the appropriate help
                                      message for the specified
                                      `[FLAG]`.

                                      For example:

                                      ```
                                      rig --help

                                      rig --help --new-app
                                      ```

                                      The `[FLAG]` may be
                                      specified with or
                                      without the `--` prefix.

                                      For example, the following
                                      calls to rig are equivalent:

                                      ```
                                      rig --help --new-app

                                      rig --help new-app
                                      ```

                                      WARNING: The
                                      `--path-to-apps-directory`
                                      flag is an exception, to get
                                      help information for the
                                      `--path-to-apps-directory` the
                                      `--` prefix must be excluded.

                                      For example:

                                      The following example won't
                                      work, rig will think you
                                      are trying to set an
                                      alternative Apps directory
                                      path.

                                      ```
                                      rig --help \
                                          --path-to-apps-directory
                                      ```

                                      To get help information
                                      about the
                                      `--path-to-apps-directory`
                                      flag the `--`
                                      prefix must be excluded:

                                      For example:

                                      ```
                                      rig --help \
                                          path-to-apps-directory
                                      ```

                                      Note: It is also possible to
                                      pass a documentation topic to
                                      `rig --help`. The following
                                      documentation topics can be
                                      passed to `rig --help`:

                                      ```
                                      rig --help AppPackages

                                      rig --help Apps

                                      rig --help Components.php

                                      rig --help DynamicOutput

                                      rig --help DynamicOutputComponents

                                      rig --help GlobalResponses

                                      rig --help OutputComponents

                                      rig --help Requests

                                      rig --help Responses

                                      rig --help css
                                      
                                      rig --help getting-started

                                      rig --help js

                                      rig --help resources

                                      rig --help roady
                                      ```

  ### `[--make-app-package]`

                                      Make an App Package into
                                      an App.

                                      Note: For more information use:

                                      ```
                                      rig --help make-app-package
                                      ```

  ### `[--new-app-package]`

                                      Creates a new App Package.

                                      Note: For more information use:

                                      ```
                                      rig --help new-app-package
                                      ```

  ### `[--new-app]`

                                      Creates a new [roady](https://roady.tech) App.
                                      The new App will be created
                                      at the path that corresponds
                                      to the value assigned to the
                                      `--path-to-apps-directory`
                                      flag.

                                      Note: For more information use:

                                      ```
                                      rig --help new-app
                                      ```

  ### `[--new-dynamic-output-component]`

                                      Configure a new
                                      DynamicOutputComponent
                                      for an App.

                                      Note: For more information use:

                                      ```
                                      rig --help \
                                      --new-dynamic-output-component
                                      ```

  ### `[--new-global-response]`

                                      Configure a new GlobalResponse
                                      for the specified App.

                                      Note: For more information use:

                                      ```
                                      rig --help new-global-response
                                      ```

  ### `[--new-output-component]`

                                      Configure a new OutputComponent
                                      for the specified App.

                                      Note: For more information use:

                                      ```
                                      rig --help new-output-component
                                      ```

  ### `[--new-request]`

                                      Configure a new Request for the
                                      specified App.

                                      Note: For more information use:

                                      ```
                                      rig --help new-request
                                      ```

  ### `[--new-response]`

                                      Configure a new Response for
                                      the specified App.

                                      Note: For more information use:

                                      ```
                                      rig --help new-response
                                      ```

  ### `[--start-server]`

                                      Start a php built in server
                                      instance that can be used to
                                      run one or more [roady](https://roady.tech) Apps
                                      locally.

                                      Note: For more information use:

                                      ```
                                      rig --help start-server
                                      ```

  ### `[--view-active-servers]`

                                      List the domains associated
                                      with the currently active
                                      php built in server instances
                                      that were started via
                                      `rig --start-server`.

  ### Documenation

  The documentation can be accessed locally via `rig --help`.

  The following arguments can be passed to `rig --help`

  ```
  rig --help assign-to-response

  rig --help configure-app-output

  rig --help debug

  rig --help help

  rig --help make-app-package

  rig --help new-app

  rig --help new-app-package

  rig --help new-dynamic-output-component

  rig --help new-global-response

  rig --help new-output-component

  rig --help new-request

  rig --help new-response

  rig --help path-to-apps-directory

  rig --help start-server

  rig --help view-active-servers

  rig --help AppPackages

  rig --help Apps

  rig --help Components.php

  rig --help DynamicOutput

  rig --help DynamicOutputComponents

  rig --help GlobalResponses

  rig --help OutputComponents

  rig --help Requests

  rig --help Responses

  rig --help css

  rig --help getting-started

  rig --help js

  rig --help resources

  rig --help roady
  ```

  Documentation for rig and [roady](https://roady.tech) can also be found online at:

  ```
  https://roady.tech
  ```

  rig is available on GitHub:

  https://github.com/sevidmusic/rig

  [roady](https://roady.tech) is available on GitHub:

  https://github.com/sevidmusic/roady

  A collection of supported App Packages that can be made into roady
  Apps via rig --make-app-package is available on GitHub:

  https://github.com/sevidmusic/roadyAppPackages
