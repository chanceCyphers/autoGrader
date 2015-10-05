drop table if exists categories;
create table categories (count int not null auto_increment primary key, id varchar(100), description varchar(50), owner varchar(50), datetime timestamp);
insert into categories set id='1', description='All Categories', owner='-root-';

drop table if exists users;
create table users(username varchar(50) not null primary key, pwd text, email varchar(50));
insert into users values('aikeji', password('ikeji'), 'aikeji@emich.edu');

drop table if exists groups;
create table groups(id int auto_increment not null primary key, name varchar(50), categoryId varchar(50));
insert into groups values (1,'-root-', '1'), (2, 'Everyone', '1');

drop table if exists groupMembers;
create table groupMembers(groupId int, username varchar(50), primary key (groupId, username));
insert into groupMembers values (1, 'aikeji');

drop table if exists itemPrivileges;
create table itemPrivileges(itemId varchar(100), groupId int, privilege int, primary key (itemId, groupId));
insert into itemPrivileges values (1, 1, 3), (1,2,2);


drop table if exists notes;
create table notes(id varchar(100) not null primary key, note longText);

drop table if exists questions;
create table questions (id int auto_increment not null primary key, title text, question longText, owner varchar(100), subcategory varchar(100), privileges varchar(100));
