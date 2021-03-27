<?php

function hello_world() {
    $current = basename($PHP_SELF);
    $logo_guid = php_logo_guid();

    echo '<img src="'.$current .'?=' . $logo_guid . '" alt="PHP Logo" border="0" />';

    echo '<h1>Hello world!</h1>';
    echo 'This is php version: ' . phpversion();
}