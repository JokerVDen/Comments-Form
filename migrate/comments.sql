create table comments
(
    id    int auto_increment
        primary key,
    name  varchar(100) not null,
    email varchar(100) not null,
    text  text         not null
);
