<?php
require_once "functions.php";
$login = false;
$content = include_template('templates/guest.php', [
    'login' => $login
]);

print $content;

