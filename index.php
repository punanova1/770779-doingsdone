<?php
date_default_timezone_set('Europe/Moscow');
require_once "functions.php";
require_once "init.php";

session_start();

if($_GET['page'] == "logout") {
    if(isset($_SESSION['user'])) {
        $_SESSION = [];
    }
}
$u_id = $_SESSION['user']['id'];
$p_id = 0;
$posted_name = "";
$errors =
    [
        'titleError' => false,
        'dateError' => false,
        'errors' => false
    ];

if($_GET['page'] == "registration") {
    header("Location: registration.php");
}
if($_GET['page'] == "login") {
    header("Location: login.php");
}
if(!isset($_SESSION['user']) AND $_GET['page'] != "registration") {
    header("Location: guest.php");
}
if(isset($_SESSION['user'])) {
    $usersName = getUsersNameById($link, $_SESSION['user']['id']);
}
if(isset($_SESSION['user'])) {
    if (!$link) {
        $error = mysqli_connect_error();
        $content = include_template('templates/error.php', ['error' => $error]);
    }
    else {
        // показывать или нет выполненные задачи
        $show_complete_tasks = 0;
        // выбираем список проектов
        $projects = users_projects($link, $u_id);
        array_unshift($projects, ['id' => 0, 'project' => "Входящие"]);
        if (isset($_GET['id'])) {
            $p_id = (int)$_GET['id'];
        }
        $tasks = users_tasks($link, $p_id, $u_id);
    }
    // создание задачи + валидация
    if ($_SERVER['REQUEST_METHOD'] == "POST" AND isset($_POST["tasks_add"])) {
        $posted_name = $_POST['name'];
        $posted_date = $_POST['date'];
        $posted_file = $_FILES['preview'];
        $posted_project = $_POST['project'];
        if (empty($posted_name)) {
            $errors['titleError'] = true;
            $errors['errors'] = true;
        }
        if (empty($posted_project)) {
            $posted_project = NULL;
        }
        if (!validity_date($posted_date)) {
            $errors['dateError'] = true;
            $errors['errors'] = true;
        }
        if (empty($posted_date)) {
            $posted_date = NULL;
        }
        if (!$errors['errors']) {
            if (!empty($_FILES['preview']['name'])) {
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
                    header("Location: ' . 'index.php");
                } else {
                    header("Location: ' . 'index.php?id=" . $posted_project);
                }
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
    'errors'   => $errors,
    'usersName'=> $usersName
]);
print($content);
