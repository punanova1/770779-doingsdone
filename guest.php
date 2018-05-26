<?php
require_once "functions.php";

$content = include_template('templates/guest.php', [
    'login' => $login
]);

print $content;

