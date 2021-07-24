<?php

/**
 * Components.php
 *
 * This file is used to configure an AppBuilder for the App.
 *
 * The AppBuilder is responsible for building the App for a domian when the this
 * file is executed via php.
 *
 *     For example:
 *
 *     php Apps/APPNAME/Components.php
 *
 * Unless you modify this file's logic, the domain the App is built for will be
 * determined as follows:
 *
 *     1. If a domain is specified via $argv[1] the App will be built
 *        for that domain.
 *
 *        For example:
 *
 *            php Apps/APPNAME/Components.php 'https://specified.domain'
 *
 *        Would build the App for the domain:
 *
 *            https://specified.domain
 *
 *     2. If a domain is not specified via $argv[1] then the App will be built
 *        for the hard coded domain passed as the $domain parameter to this
 *        files call to the AppBuilder::getAppsAppComponentsFactory() method:
 *
 *            AppBuilder::buildApp(
 *                AppBuilder::getAppsAppComponentsFactory($appName, $domain)
 *                                                                     ^
 *            );
 *
 *     3. If a domain is not specified via $argv[1], and the hard-coded default
 *        domain is either not defined, or defined as an empty string, then the
 *        App will be built for the domain:
 *
 *            http://localhost:8080
 */

use roady\classes\utility\AppBuilder;

ini_set('display_errors', 'true');

require(
    strval(
        realpath(
            str_replace(
                'Apps' . DIRECTORY_SEPARATOR . strval(basename(__DIR__)),
                'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
                __DIR__
            )
        )
    )
);

AppBuilder::buildApp(
    AppBuilder::getAppsAppComponentsFactory(
        /**
         * @param string $appName
         * Configure the App's name. The App's name should match the App's
         * directory's name.
         */
        strval(basename(__DIR__)),
        (
            /**
             * @param string $domain
             * Configure the domain to build the App for.
             * App will be built for Domain specified via $argv[1] if provided.
             *
             * If $argv[1] is not provided, and the
             * AppBuilder::getAppsAppComponentsFactory() method's $domain
             * parameter is set, the App will be built for the domain passed
             * to the $domain parameter.
             *
             * Note: If $argv[1] is not specified, and the $domain parameter
             * is an empty string, then the AppBuilder will build the App for the
             * domain:
             *
             *     http://localhost:8080
             *
             * WARNING: If you modify this file, it is recomeneded that you
             *          still pass the value you use for the $domain parameter to
             *          escapeshellarg().
             */
            escapeshellarg($argv[1] ?? '')
        )
    )
);
