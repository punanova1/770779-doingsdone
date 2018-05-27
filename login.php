<?php
require_once "functions.php";
require_once "init.php";

$login = true;
$loginErrors =
    [
        'emptyEmail' => false,
        'emptyPassword' => false,
        'emailNotFound' => false,
        'incorrectPassword' => false,
        'errors' => false
    ];
$userEmail = "";
if($_SERVER['REQUEST_METHOD'] == "POST" AND isset($_POST['login'])) {
    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];
    if (empty($userEmail)) {
        $loginErrors['emptyEmail'] = true;
        $loginErrors['errors'] = true;
    }
    if (empty($userPassword)) {
        $loginErrors['emptyPassword'] = true;
        $loginErrors['errors'] = true;
    }
    if (!checkEmail($link, $userEmail)) {
        $loginErrors['emailNotFound'] = true;
        $loginErrors['errors'] = true;
    }
    if (!verify_password($link, $userEmail, $userPassword)) {
        $loginErrors['incorrectPassword'] = true;
        $loginErrors['errors'] = true;
    }
    if ($loginErrors['errors'] == false) {
        $u_id = getUsersIdByEmail($link, $userEmail);
        session_start();
        $_SESSION['user'] = $u_id;
        header("Location: index.php");
    }
};
$login = include_template('templates/login.php', [
        'login' => $login,
        'loginErrors' => $loginErrors,
        'userEmail' => $userEmail
]);
$content = include_template('templates/guest.php', [
        'login' => $login
]);
print($content);
