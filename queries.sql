insert into users
set name = 'Игнат', email = 'ignat.v@gmail.com', contacts = '89217659986', pw = '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka';
insert into users
set name = 'Леночка', email = 'kitty_93@li.ru', contacts = '89115236974', pw = '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa';
insert into users
set name = 'Руслан', email = 'warrior07@mail.ru', contacts = '89065548792', pw = '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW';
insert into users
set name = 'Ксения', email = 'ksundik92@mail.ru', pw = '$2y$10$6OxpEH8nyrYpkOT8H5cApezuzh15tZEEQ2axgFOH5cApezuzh15tx';


insert into projects
set project = 'Входящие', u_id = '2';
insert into projects
set project = 'Учеба', u_id = '4';
insert into projects
set project = 'Работа', u_id = '4';
insert into projects
set project = 'Дом', u_id = '4';
insert into projects
set project = 'Работа', u_id = '2';
insert into projects
set project = 'Домашние дела', u_id = '3';
insert into projects
set project = 'Авто', u_id = '1';


insert into tasks
set task = 'Собеседование в IT компании', deadline = '2018-06-01 00:00' , u_id = 2, p_id = '4';
insert into tasks
set task = 'Выполнить тестовое задание', deadline = '2018-05-17 00:00' , u_id = 2, p_id = '4';
insert into tasks
set task = 'Сделать задание первого раздела', deadline = '2018-04-21 00:00' , u_id = 4, p_id = '3';
insert into tasks
set task = 'Встреча с другом', deadline = '2018-04-22 00:00' , u_id = 1, p_id = '2';
insert into tasks
set task = 'Купить корм для кота', u_id = 3, p_id = '5';
insert into tasks
set task = 'Заказать пиццу', u_id = 3, p_id = '5';


/* получить список из всех проектов для одного пользователя */
select project from projects
where u_id = 4;

/* получить список из всех задач для одного проекта */
select task from tasks
where p_id = 5;

/* пометить задачу как выполненную */
update tasks set end_date = '2018-05-17 17:05'
where task = 'Сделать задание первого раздела';

/* получить все задачи для завтрашнего дня */
select task from tasks
where deadline = '2018-05-17 00:00';

/* обновить название задачи по её идентификатору */
update tasks set task = 'Встреча с друзьями'
where id = '4';