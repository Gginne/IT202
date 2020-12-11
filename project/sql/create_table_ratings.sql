CREATE TABLE IF NOT EXISTS `Ratings`
(
    id         int auto_increment,
    product_id int,
    rating     int,
    comment   TEXT,
    user_id    int,
    created    datetime       default current_timestamp,
    primary key (id),
    foreign key (product_id) references Products (id),
    foreign key (user_id) references Users (id),
    UNIQUE KEY (product_id, user_id)
)