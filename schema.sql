create database doingsdone
default character set utf8
default collate utf8_general_ci;

use doingsdone;

create table users (
 id int auto_increment primary key,
 name char(128) NOT NULL,
 email char(128) NOT NULL,
 reg_data datetime,
 contacts char(128),
 pw char(64) NOT NULL
  );

create table projects (
 id int auto_increment primary key,
 project char(128) NOT NULL,
 u_id int(10),
 FOREIGN KEY (u_id) REFERENCES users(id)
  );  

create table tasks (
 id int auto_increment primary key,
 task char(128) NOT NULL,
 create_data datetime,
 end_data datetime,
 t_file blob,
 deadline datetime,
 u_id int(10),
 p_id int(10),
 FOREIGN KEY (u_id) REFERENCES users(id),
 FOREIGN KEY (p_id) REFERENCES projects(id)
  );
   
create unique index email on users(email);
create index name on users(name);
create index task on tasks(task);
create index project on projects(project);


