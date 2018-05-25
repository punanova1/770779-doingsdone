<?php
 date_default_timezone_set('Europe/Moscow');
 require_once 'functions.php';
 require_once 'init.php';
$u_id = 0;
$p_id = 0;
$posted_name = "";
$errors =
    [
        "titleError" => false,
        "dateError" => false,
        "errors" => false
    ];
$registrationErrors =
    [
        "emailEmptyError" => false,
        "emailTakenError" => false,
        "emailValidityError" => false,
        "emailError" => false,
        "passwordError" => false,
        "nameError" => false,
        "errors" => false
    ];
if (!$link){
    $error = mysqli_connect_error();
    $content = include_template('templates/error.php', ['error' => $error]);
}
else {
    // показывать или нет выполненные задачи
    $show_complete_tasks = 1;
    // выбираем список проектов
    $projects = users_projects($link, $u_id);
    array_unshift($projects, ["id" => 0, "project" => "Входящие"]);
    if (isset($_GET["id"])) {
        $p_id = (int)$_GET["id"];
    }
    $tasks = users_tasks($link, $p_id, $u_id);
}
// регистрация пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["register"])) {
    $userName = $_POST["name"];
    $userEmail = $_POST["email"];
    $userPassword = $_POST["password"];

    if(empty($userName)) {
        $registrationErrors["nameError"] = true;
        $registrationErrors["errors"] = true;
    }
    if(empty($userPassword)){
        $registrationErrors["passwordError"] = true;
        $registrationErrors["errors"] = true;
    }
    if(empty($userEmail)){
        $registrationErrors["emailEmptyError"] = true;
        $registrationErrors["emailError"] = true;
        $registrationErrors["errors"] = true;
    }
    if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $registrationErrors["emailValidityError"] = true;
        $registrationErrors["emailError"] = true;
        $registrationErrors["errors"] = true;
    }
    if($registrationErrors["emailEmptyError"] OR $registrationErrors["emailValidityError"]) {
        $registrationErrors["emailError"] = true;
    }
    if(checkEmailTaken($link, $userEmail)){
        $registrationErrors["emailTakenError"] = true;
        $registrationErrors["emailError"] = true;
        $registrationErrors["errors"] = true;
    }
    if(!$registrationErrors["errors"]) {
        if(addNewUser($link, $userName, $userEmail, $userPassword)) {
            print "Пользователь добавлен";
        }
    }
}
// создание задачи + валидация
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["tasks_add"])) {
    $posted_name = $_POST["name"];
    $posted_date = $_POST["date"];
    $posted_file = $_FILES["preview"];
    $posted_project = $_POST["project"];
    if (empty($posted_name)) {
        $errors["titleError"] = true;
        $errors["errors"] = true;
    }
    if (empty($posted_project)) {
        $posted_project = NULL;
    }
    if (!validity_date($posted_date)) {
        $errors["dateError"] = true;
        $errors["errors"] = true;
    }
    if (empty($posted_date)) {
        $posted_date = NULL;
    }
    if (!$errors["errors"]) {
        if (!empty($_FILES["preview"]["name"])) {
            $file_info = uploadFile($_FILES);
            $file_name = $file_info[1];
            $file_url = $file_info[0];
        }
        $sql = "INSERT INTO tasks (task, file_name, file_path, deadline, u_id, p_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssii', $posted_name, $file_name, $file_url, $posted_date, $u_id, $posted_project);
        $res = mysqli_stmt_execute($stmt);
        if ($res) {
            if ($posted_project == NULL) {
                header("Location: " . "index.php");
            } else {
                header("Location: " . "index.php?id=" . $posted_project);
            }
        }
    }
}

// cодержимое тега main
$content = include_template('templates/index.php', [
    'tasks'               => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
]);
// форма добавления задачи
$addtask = include_template('templates/addtask.php', [
    'projects'    => $projects,
    'errors'      => $errors,
    'posted_name' => $posted_name
]);

$content = include_template('templates/layout.php', [
    'content'  => $content,
    'title'    => 'Дела в порядке',
    'tasks'    => $tasks,
    'projects' => $projects,
    'u_id'     => $u_id,
    'p_id'     => $p_id,
    'link'     => $link,
    'addtask'  => $addtask,
    'errors'   => $errors
]);
// Страница регистрации
if ($u_id == 0) {
    $content = include_template('templates/registration.php', [
        'registrationErrors' => $registrationErrors
    ]);
}
print($content);
?>

