create database test;
use test;

create table subscribers(
    id int not null primary key auto_increment,
    fname varchar(50) not null,
    email varchar(50) not null
);

create table audit_subscribers(
    id int not null primary key auto_increment,
    subscriber_name varchar(50) not null,
    action_performed varchar(50) not null,
    date_added datetime not null default current_timestamp
);

delimiter $$

create trigger before_subscriber_insert before insert on subscribers for each row
begin
    insert into audit_subscribers
    set action_performed = "Insert a new subscriber",
    subscriber_name = new.fname;
end
$$

create trigger after_subscriber_delete after delete on subscribers for each row
begin
    insert into audit_subscribers
    set action_performed = "Deleted a subscriber",
    subscriber_name = old.fname;
end
$$

create trigger after_subscriber_edit after update on subscribers for each row
begin
    insert into audit_subscribers
    set action_performed = "Updated a subscriber",
    subscriber_name = old.fname;
end
$$

delimiter ;
