create database doingsdone
default character set utf8
default collate utf8_general_ci;

use doingsdone;

create table users (
 id int auto_increment primary key,
 name varchar(128) NOT NULL,
 email varchar(128) NOT NULL,
 reg_date datetime DEFAULT CURRENT_TIMESTAMP,
 contacts varchar(128),
 pw varchar(64) NOT NULL
  );

create table projects (
 id int auto_increment primary key,
 project varchar(128) NOT NULL,
 u_id int(10),
 FOREIGN KEY (u_id) REFERENCES users(id)
  );  

create table tasks (
 id int auto_increment primary key,
 task varchar(128) NOT NULL,
 create_date datetime  DEFAULT CURRENT_TIMESTAMP,
 end_date datetime,
 file_name varchar (128),
 file_path varchar (128),
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


