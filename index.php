<!-- Главный сценарий -->

<?php
 date_default_timezone_set('Europe/Moscow');
 require_once 'functions.php';

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// простой массив проектов
$project = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

// ассоциативные массивы задач
$task_1 = ['task' => 'Собеседование в IT компании', 'end_date' => '2018-06-01', 'category' => $project[3], 'complete' => false ];
$task_2 = ['task' => 'Выполнить тестовое задание', 'end_date' => '2018-05-17', 'category' => $project[3], 'complete' => false ];
$task_3 = ['task' => 'Сделать задание первого раздела', 'end_date' => '2018-04-21', 'category' => $project[2], 'complete' => true ];
$task_4 = ['task' => 'Встреча с другом', 'end_date' => '2018-04-22', 'category' => $project[1], 'complete' => false ];
$task_5 = ['task' => 'Купить корм для кота', 'end_date' => 'Нет', 'category' => $project[4], 'complete' => false ];
$task_6 = ['task' => 'Заказать пиццу', 'end_date' => 'Нет', 'category' => $project[4], 'complete' => false ];

// двумерный массив из всех задач
$tasks = array($task_1, $task_2, $task_3, $task_4, $task_5, $task_6);

//HTML код главной страницы
$main_content = include_template('templates/index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);

//окончательный HTML код
$layout_content = include_template('templates/layout.php', ['content' => $main_content, 'title' => 'Дела в порядке', 'tasks' => $tasks, 'project' => $project]);

print($layout_content);
?>

