<?php

foreach (glob(__DIR__ . '/*.php') as $filename) {
    if (basename($filename) !== 'helpers.php') {
        require_once $filename;
    }
}
