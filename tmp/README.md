The tmp/ directory is used by ddms in circumstances when an appropriate directory
path cannot be determined.

For example, if `ddms --new-app` is run and the path to the DarlingDataManagmentSystem's
Apps directory cannot be determined, the new App will be created in the tmp/ directory.

If you find this directory is populated by ddms, it means that one of the following
internal ddms flags was not passed to bin/ddms.php by bin/ddms:

--ddms-internal-flag-pwd "$(pwd)"

To fix this, make sure bon/ddms's call to /usr/bin/php ddms.php sets the appropriate
internal flags after the $@ parameter:

bin/ddms.php
...

/usr/bin/php ddms.php $@ --ddms-internal-flag-pwd "$(pwd)" ...

...
