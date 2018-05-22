<!-- Главный сценарий -->

<?php
 date_default_timezone_set('Europe/Moscow');
 require_once 'functions.php';
 require_once 'init.php';
 
 
 // показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
 
if (!$link){
	$error = mysqli_connect_error();
	$content = include_template('templates/error.php', ['error' => $error]);
}
else {
	$sql = 'SELECT `id`,`project` FROM projects WHERE u_id = 4';
	$result = mysqli_query($link, $sql);
	
	if ($result){
		$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
	}
	else {
		$error = mysqli_connect_error($link);
		$content = include_template('templates/error.php', ['error' => $error]);
	}
	$sql = 'SELECT  task, end_date, file_name, file_path, deadline, tasks.u_id, tasks.p_id, projects.project FROM tasks JOIN projects ON tasks.p_id = projects.id WHERE tasks.u_id = 4';
	
	if($res = mysqli_query($link, $sql)){
		$tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
		//HTML код главной страницы
		$content = include_template('templates/index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);
	}


//окончательный HTML код
$content = include_template('templates/layout.php', ['content' => $content, 'title' => 'Дела в порядке', 'tasks' => $tasks, 'projects' => $projects]);
}
print($content);

?>

