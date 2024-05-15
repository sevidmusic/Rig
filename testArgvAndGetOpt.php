<?php

var_dump($argv ?? null);
var_dump(getopt('', ['foo:', 'baz', 'bin::']));
