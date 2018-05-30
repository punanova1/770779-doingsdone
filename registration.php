<?php
require_once "functions.php";
require_once "init.php";

$registrationErrors =
    [
        'emailEmptyError' => false,
        'emailTakenError' => false,
        'emailValidityError' => false,
        'emailError' => false,
        'passwordError' => false,
        'nameError' => false,
        'errors' => false
    ];
$userName = "";
$userEmail = "";
// регистрация пользователя
if ($_SERVER['REQUEST_METHOD'] == "POST" AND isset($_POST['register'])) {
    $userName = (string)$_POST['name'];
    $userEmail = (string)$_POST['email'];
    $userPassword = (string)$_POST['password'];

    if(empty($userName)) {
        $registrationErrors['nameError'] = true;
        $registrationErrors['errors'] = true;
    }
    if(empty($userPassword)){
        $registrationErrors['passwordError'] = true;
        $registrationErrors['errors'] = true;
    }
    if(empty($userEmail)){
        $registrationErrors['emailEmptyError'] = true;
        $registrationErrors['emailError'] = true;
        $registrationErrors['errors'] = true;
    }
    if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $registrationErrors['emailValidityError'] = true;
        $registrationErrors['emailError'] = true;
        $registrationErrors['errors'] = true;
    }
    if($registrationErrors['emailEmptyError'] OR $registrationErrors['emailValidityError']) {
        $registrationErrors['emailError'] = true;
    }
    if(checkEmail($link, $userEmail)){
        $registrationErrors['emailTakenError'] = true;
        $registrationErrors['emailError'] = true;
        $registrationErrors['errors'] = true;
    }
    if(!$registrationErrors['errors']) {
        if(addNewUser($link, $userName, $userEmail, $userPassword)) {
            header("Location: login.php");
            exit;
        }
    }
}
$content = include_template('templates/registration.php', [
    'registrationErrors' => $registrationErrors,
    'userName' => $userName,
    'userEmail' => $userEmail
]);
print($content);
