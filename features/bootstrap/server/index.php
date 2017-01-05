<?php

$verified = '0';
$content = 'INVALID';

if (array_key_exists('__verified', $_POST)) {
    $verified = $_POST['__verified'];
}

if ($verified === '1') {
    $content = 'VERIFIED';
}

echo $content;
