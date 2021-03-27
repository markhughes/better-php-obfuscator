#!/usr/bin/env php
<?php

try {
    Phar::mapPhar('bphpo.phar');
    putenv("BPHPO_HOME=phar://" . __FILE__);
    include 'phar://bphpo.phar/src/bphpo.php';
} catch (PharException $e) {
    echo $e->getMessage();
    echo 'Cannot initialize Phar';
    exit(1);
}

__HALT_COMPILER();
