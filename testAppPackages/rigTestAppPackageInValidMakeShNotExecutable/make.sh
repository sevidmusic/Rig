#!/bin/bash
# make.sh
# NOTE: This App Package is specifically designed for use by the tests defined for
#       `rig --make-app-package`. DO NOT USE THIS APP PACKAGE FOR ANYTHING ELSE!

set -o posix

setupPaths() {
    local path_to_this_file search replace
    # Determine real path to dsh directory
    SOURCE="${BASH_SOURCE[0]}"
    while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
      DIR="$(cd -P "$(dirname "$SOURCE")" >/dev/null 2>&1 && pwd)"
      SOURCE="$(readlink "$SOURCE")"
      [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
    done
    path_to_this_file="$(cd -P "$(dirname "$SOURCE")" >/dev/null 2>&1 && pwd)"
    search='testAppPackages/rigTestAppPackageValidMakeSh'
    replace='bin/rig'
    PATH_TO_Rig_EXECUTABLE="${path_to_this_file/$search/$replace}"
}

# The path to the rig executable must be determined dynamically based on the location of this make.sh file to accomodate different installation contexts.
setupPaths

# even though rig executable path is determined dynamically for tests, in
# order for this make.sh to be valid, we still need to at least define the following
# rig new app command call as a comment or rig will complain and phpunit tests will fail.
# rig --new-app --name rigTestAppPackageValidMakeSh --domain "http://localhost:8080/" --debug flags options
"${PATH_TO_Rig_EXECUTABLE}" --new-app --name rigTestAppPackageValidMakeSh --domain "http://localhost:8080/" --debug flags options

