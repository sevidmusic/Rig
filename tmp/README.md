The tmp/ directory is used by rig in circumstances when an appropriate directory
path cannot be determined.

For example, if `rig --new-app` is run and the path to roady's
Apps directory cannot be determined, the new App will be created in the tmp/ directory.

If you find this directory is populated by rig, it means that one of the following
internal rig flags was not passed to bin/rig.php by bin/rig:

--rig-internal-flag-pwd "$(pwd)"

To fix this, make sure bon/rig's call to /usr/bin/php rig.php sets the appropriate
internal flags after the $@ parameter:

bin/rig.php
...

/usr/bin/php rig.php $@ --rig-internal-flag-pwd "$(pwd)" ...

...
