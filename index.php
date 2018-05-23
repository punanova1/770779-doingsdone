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
	$sql = 'SELECT `id`,`project` FROM projects';
	$result = mysqli_query($link, $sql);
	
			$sql = 'SELECT  task, tasks.p_id, projects.project FROM tasks JOIN projects ON tasks.p_id = projects.id';
			$res = mysqli_query($link, $sql);
			if ($res){
				$all_tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
	}
		
	if ($result){
		$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
	}
	else {
		$error = mysqli_connect_error($link);
		$content = include_template('templates/error.php', ['error' => $error]);
	}
	
	$show_project = '';
		if(isset($_GET['id'])) {
			$show_project ='WHERE tasks.p_id = ' . $_GET['id'];
		}
		$sql = 'SELECT  task, end_date, file_name, file_path, deadline, tasks.u_id, tasks.p_id, projects.project FROM tasks JOIN projects ON tasks.p_id = projects.id '.$show_project;
		
		if($res = mysqli_query($link, $sql)){
			$tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
			if(count($tasks) == 0){
				http_response_code(404);
				$content = 'Not Found '. http_response_code();
			}
			else{
				//HTML код главной страницы
			$content = include_template('templates/index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);
			}
			
		}
	
	//окончательный HTML код
	$content = include_template('templates/layout.php', ['content' => $content, 'title' => 'Дела в порядке', 'tasks' => $tasks, 'all_tasks' => $all_tasks, 'projects' => $projects]);
}
print($content);

?>

