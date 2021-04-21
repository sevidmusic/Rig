#!/bin/bash
# make.sh
# NOTE: This App Package is specifically designed for use by the tests defined for
#       `ddms --make-app-package`. DO NOT USE THIS APP PACKAGE FOR ANYTHING ELSE!

set -o posix

/home/darling/Downloads/vendor/darling/ddms/bin/ddms --new-app --name ddmsTestAppPackageValidMakeSh --domain "http://localhost:8080/" --debug flags options

/home/darling/Downloads/vendor/darling/ddms/bin/ddms --new-dynamic-output-component --name DynamicOutputComponent --for-app ddmsTestAppPackageValidMakeSh

