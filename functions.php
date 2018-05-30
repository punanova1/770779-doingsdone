<?php

require_once "mysql_helper.php";

/**
 * Считает количество проектов по категориям
 *
 * @param mysqli $link Ресурс соединения
 * @param int $p_id ID проекта
 * @param int $u_id ID пользователя
 *
 * @return int $count количество проектов
 */
function calculate_project($link, $p_id, $u_id) {
    if (!$p_id) {
        $sql = "SELECT * FROM tasks WHERE p_id IS NULL AND u_id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $u_id);
    }
    elseif ($p_id == -1) {
        $sql = "SELECT * FROM tasks WHERE u_id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $u_id);
    }
    else {
        $sql = "SELECT * FROM tasks WHERE p_id = ? AND u_id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $p_id, $u_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_num_rows($result);
    return $count;
}
/**
 * Добавляет шаблон
 *
 * @param string $file Ссылка на шаблон
 * @param array $data Данные для вставки в шаблон
 *
 * @return string Подготовленный шаблон
 */
function include_template($file, $data){
    if (!file_exists($file)) {
        return "";
    }
    ob_start();
        extract($data);
    require_once($file);
        $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}
/**
 * Сроки выполнения задачи (до дедлайналайна)
 *
 * @param string $data дедлайн задачи
 *
 * @return string $deadline количество часов до дедлайна
 */
function deadline ($data){
    $end_date = strtotime($data);
    if ($end_date == "") {
        return "";
    }
    $curdate = time();
    $deadline = floor(($end_date - $curdate)/60/60);

    return $deadline;
}
/**
 * Получает проекты для конкретного пользователя
 *
 * @param mysqli $link Ресурс соединения
 * @param int $u_id ID пользователя
 *
 * @return array проекты для конкретного пользователя
 */
function users_projects($link, $u_id) {
    $sql = "SELECT * FROM projects WHERE u_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $u_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $projects;
    }
    else {
        return [];
    }
}
/**
 * Получает данные задач для конкретного пользователя и конкретного проекта
 *
 * @param mysqli $link Ресурс соединения
 * @param int $p_id ID проекта
 * @param int $u_id ID пользователя
 *
 * @return array данные задач для конкретного пользователя и конкретного проекта
 */
function users_tasks($link, $p_id, $u_id) {
    if (!$p_id) {
        $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') AS deadline FROM tasks WHERE p_id IS NULL AND u_id = ? ORDER BY create_date DESC";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $u_id);
    }
    else {
        $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') AS deadline FROM tasks WHERE p_id = ? AND u_id = ? ORDER BY create_date DESC";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $p_id, $u_id);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if(count($result) != 0 ) {
        $tasks = $result;
        return $tasks;
    }
    else {
        return [];
    }
}
/**
 * Валидация даты (из формы)
 *
 * @param string $date ID проекта
 *
 * @return boolean $valid верна дата или нет
 */
function validity_date($date) {
    $valid = false;
    $d = DateTime::createFromFormat('Y-m-d H:i', $date);
    if (($d && $d->format('Y-m-d H:i') == $date) OR empty($date)) {
        $valid = true;
    }
    return $valid;
}
/**
 * Загрузка файла
 */
function uploadFile($file) {
    if(!file_exists("uploads")) {
        mkdir("uploads", 0777, true);
    }
    $fileName = str_replace(' ', '-', $file['preview']['name']);
    $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $file['preview']['name']);
    $fileUrl = '/uploads/' . $fileName;
    move_uploaded_file($file['preview']['tmp_name'], "uploads/" . $fileName);
    return array($fileUrl, $fileName);
}
/**
 * Проверка email
 *
 * @param mysqli $link Ресурс соединения
 * @param string $email email пользователя
 *
 * @return int $result количество email
 */
function checkEmail($link, $email) {
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's',$email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result);
}
/**
 * Добавление нового пользователя
 *
 * @param mysqli $link Ресурс соединения
 * @param string $name name пользователя
 * @param string $email email пользователя
 * @param string $pw пароль пользователя
 */
function addNewUser($link, $name, $email, $pw) {
    $hash_pw = password_hash($pw, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, pw) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash_pw);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}
/**
 * Проверка подлинности пароля
 *
 * @param mysqli $link Ресурс соединения
 * @param string $email email пользователя
 * @param string $pw пароль пользователя
 */
function verify_password($link, $email, $pw) {
    $sql = "SELECT pw FROM users WHERE email = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $DBpw = mysqli_fetch_assoc($result);
    return password_verify($pw, $DBpw['pw']);
}
/**
 * Возвращает ID пользователя по эл.почте
 *
 * @param mysqli $link Ресурс соединения
 * @param string $email email пользователя
 */
function getUsersIdByEmail($link, $email) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
/**
 * Возвращает имя пользователя по ID
 *
 * @param mysqli $link Ресурс соединения
 * @param int $u_id ID пользователя
 */
function getUsersNameById($link, $u_id) {
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $u_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
/**
 * Добавление нового проекта
 *
 * @param mysqli $link Ресурс соединения
 * @param int $u_id ID пользователя
 * @param string $u_id название проекта
 */
function addNewProject($link, $u_id, $projectName)
{
    $sql = "SELECT project FROM projects WHERE u_id = ? AND project = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $u_id, $projectName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        $sql = "INSERT INTO projects (project, u_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $projectName, $u_id);
        return mysqli_stmt_execute($stmt);
    }
    return false;
}
/**
 * Завершение/возобновление задачи
 *
 * @param mysqli $link   Ресурс соединения
 * @param int    $taskId ID задачи
 */
function task_enddate($link, $taskId)
{
    $sql  = "SELECT * FROM tasks WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $taskId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $task   = mysqli_fetch_assoc($result);

    if ($task['end_date'] == null) {
        $sql  = "UPDATE tasks SET end_date = NOW() WHERE id = ?";
    } else {
        $sql  = "UPDATE tasks SET end_date = NULL WHERE id = ?";
    }

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $taskId);

    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $taskId);

    return mysqli_stmt_execute($stmt);
}
/**
 * Выборка задач
 *
 * @param mysqli $link Ресурс соединения
 * @param int $u_id ID пользователя
 * @param $date параметр выборки
 */
 function tasks_filter($link, $u_id, $date)
{
    switch ($date) {
        case "today":
            $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') AS deadline FROM tasks WHERE u_id = ? AND STR_TO_DATE(CURDATE(), \"%Y-%m-%d\") = STR_TO_DATE(deadline, \"%Y-%m-%d\")";
            break;
        case "tomorrow":
            $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') AS deadline FROM tasks WHERE u_id = ? AND STR_TO_DATE(CURDATE() + INTERVAL 1 DAY, \"%Y-%m-%d\") = STR_TO_DATE(deadline, \"%Y-%m-%d\")";
            break;
        case "failed":
            $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %H:%i') AS deadline FROM tasks WHERE u_id = ? AND STR_TO_DATE(CURDATE(), \"%Y-%m-%d\") > STR_TO_DATE(deadline, \"%Y-%m-%d\") AND end_date is NULL";
            break;
    }
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $u_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
   return $result;
}
