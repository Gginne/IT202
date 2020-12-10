CREATE TABLE IF NOT EXISTS `OrderItems`
(
    id int auto_increment,
	product_id int,
	user_id int,
	quantity int,
	unit_price decimal(12, 2),
	orderRef int, -- this will be a manually handled id to group order items together
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	primary key (id),
	foreign key (user_id) references Users(id),
	foreign key (product_id) references Products(id)
)