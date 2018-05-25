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
    else {
        $sql = "SELECT * FROM tasks WHERE p_id = ? AND u_id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $p_id, $u_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_num_rows($result);
    return $count;
};
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
    mysqli_stmt_bind_param($stmt, "i", $u_id);
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
        $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %h:%m') AS deadline FROM tasks WHERE p_id IS NULL AND u_id = ? ORDER BY create_date DESC";
        $stmt = db_get_prepare_stmt($link, $sql, [$u_id]);
    }
    else {
        $sql = "SELECT *, DATE_FORMAT(deadline, '%Y-%m-%d %h:%m') AS deadline FROM tasks WHERE p_id = ? AND u_id = ? ORDER BY create_date DESC";
        $stmt = db_get_prepare_stmt($link, $sql, [$p_id, $u_id]);
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
    move_uploaded_file($file['preview']['tmp_name'], "uploads/" . $fileName );
    return array($fileUrl, $fileName);
}

/**
 * Проверка не занят ли email
 *
 * @param mysqli $link Ресурс соединения
 * @param string $email email пользователя
 *
 * @return int $result количество email
 */
 function checkEmailTaken($link, $email) {
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
function addNewUser($link, $name, $email, $pw){
    $hash_pw = password_hash($pw, PASSWORD_DEFAULT);
	$sql = "INSERT INTO users (name, email, pw) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash_pw);
    $res = mysqli_stmt_execute($stmt);
    return $res;
}
?>