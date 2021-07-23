#!/bin/bash
# make.sh
# NOTE: This App Package is specifically designed for use by the tests defined for
#       `rig --make-app-package`. DO NOT USE THIS APP PACKAGE FOR ANYTHING ELSE!

set -o posix

rig --new-app --name WrongName --domain "http://localhost:8080/" --debug flags options

