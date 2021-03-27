<?php

function my_other_function($value) {
    echo 'hello ' . $value;
}

function my_function($value) {
    $what = 'my_other_function';

    if (function_exists($what)) {
        my_other_function($value);
    }
}