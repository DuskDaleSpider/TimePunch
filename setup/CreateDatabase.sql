create database time_punch;

use time_punch;

create table Users(
	id int unique auto_increment primary key,
    username varchar(255) unique not null,
    password varchar(255) not null,
    first_name varchar(255) not null,
    last_name varchar(255) not null,
    phone_number varchar(255)
);

create table TimePunches(
	id int unique auto_increment primary key not null,
    user_id int not null,
    date date not null,
    punch_in timestamp,
    lunch_start timestamp,
    lunch_end timestamp,
    punch_out timestamp,
    foreign key user_key(user_id) references Users(id)
);

create table OpenPunches(
	time_punch_id int unique not null,
    user_id int unique not null,
    foreign key user_key(user_id) references Users(id),
    foreign key time_punch_key(time_punch_id) references TimePunches(id),
    constraint PK_key primary key (time_punch_id, user_id)
);


