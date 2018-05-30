<?php
date_default_timezone_set('Europe/Moscow');
require_once "functions.php";
require_once "init.php";

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: guest.php");
    exit;
}

$u_id = $_SESSION['user']['id'];
$p_id = 0;
$posted_name = "";
$postedTitle = "";
$errors =
    [
        'titleError' => false,
        'dateError' => false,
        'errors' => false
    ];
$addProjectErrors =
    [
        'emptyTitle' => false,
        'projectExists' => false,
        'errors' => false
    ];
if(isset($_SESSION['user'])) {
    $usersName = getUsersNameById($link, $_SESSION['user']['id']);
}
if(isset($_SESSION['user'])) {
    if (!$link) {
        $error = mysqli_connect_error();
        $content = include_template('templates/error.php', ['error' => $error]);
    }
    else {
        // выбираем список проектов
        $projects = users_projects($link, $u_id);
        array_unshift($projects, ['id' => 0, 'project' => "Входящие"]);
        if (isset($_GET['id'])) {
            $p_id = (int)$_GET['id'];
        }
        $tasks = users_tasks($link, $p_id, $u_id);
    }
    // создание задачи + валидация
    if ($_SERVER['REQUEST_METHOD'] == "POST" AND isset($_POST["task_add"])) {
        if(isset($_POST['name'])) {
            $posted_name = (string)$_POST['name'];
        }
        if(isset($_POST['date'])) {
            $posted_date = $_POST['date'];
        }
        if(isset($_FILES['preview'])) {
            $posted_file = $_FILES['preview'];
        }
        if(isset($_POST['project'])) {
            $posted_project = $_POST['project'];
        }
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
                    header("Location: index.php");
                    exit;
                } else {
                    header("Location: index.php?id=$posted_project");
                    exit;
                }
            }
        }
    }
    // добавление нового проекта
    if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["project_add"])) {
        if(isset($_POST['name'])) {
			$projectName = (string)$_POST["name"];
		}
        if(empty($projectName)) {
            $addProjectErrors["emptyTitle"] = true;
            $addProjectErrors["errors"] = true;
        }
        else {
            if(addNewProject($link, $u_id, $projectName)){
                header("Location: " . "index.php");
            }
            else {
                $postedTitle = $projectName;
                $addProjectErrors["projectExists"] = true;
                $addProjectErrors["errors"] = true;
            }
        }
    }
    // показывать или нет выполненые задачи
    $show_complete_tasks = false;
    if (array_key_exists("show_completed", $_GET)) {
        $show_complete_tasks = $_GET["show_completed"] === "1";
    }
    // завершение/возобновление задачи
    if(isset($_GET["task_id"])) {
        $taskId = $_GET["task_id"];
        task_enddate($link, $taskId);
    }
    // фильтры для задач
    if(isset($_GET["today"])) {
        $tasks = tasks_filter($link, $u_id, "today");
    }
    if(isset($_GET["tomorrow"])) {
        $tasks = tasks_filter($link, $u_id, "tomorrow");
    }
    if(isset($_GET["failed"])) {
        $tasks = tasks_filter($link, $u_id, "failed");
    }
}
// cодержимое тега main
$content = include_template('templates/index.php', [
    'tasks'               => $tasks,
    'show_complete_tasks'=> $show_complete_tasks
]);
// форма добавления задачи
$addtask = include_template('templates/addtask.php', [
    'projects'    => $projects,
    'errors'      => $errors,
    'posted_name' => $posted_name
]);
// форма добавления проекта
$addproject  = include_template('templates/addproject.php', [
    'addProjectErrors'    => $addProjectErrors,
    'postedTitle'         => $postedTitle
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
    'addproject'  => $addproject,
    'addProjectErrors'  => $addProjectErrors,
    'usersName'=> $usersName
]);
print($content);
