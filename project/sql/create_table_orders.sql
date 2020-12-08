CREATE TABLE IF NOT EXISTS `Orders`
(
    id int auto_increment,
	user_id int,
	total_price int,
	payment_method varchar(20) NOT NULL DEFAULT "Cash",
	address 	TEXT,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	primary key (id),
	foreign key (user_id) references Users(id)
)