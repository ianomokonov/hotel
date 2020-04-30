CREATE TABLE IF NOT EXISTS user(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    isAdmin bit DEFAULT 0,
    name varchar(255) NOT NULL,
    surname varchar(255) NOT NULL,
    middlename varchar(255),
    email varchar(255) NOT NULL,
    phone varchar (20),
    password varchar(255) NOT NUll
);

CREATE TABLE IF NOT EXISTS room(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    img varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    description text NOT NULL,
    price int(10) NOT NULL
);

CREATE TABLE IF NOT EXISTS roomOrder(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    userId int(10) NOT NULL,
    roomId int(10) NOT NULL,
    placeId int(10) NULL,
    dateFrom DATE NOT NULL,
    dateTo DATE NULL,
    orderSum int(10) NOT NULL,
    FOREIGN KEY (userId) REFERENCES user(id),
    FOREIGN KEY (roomId) REFERENCES room(id),
);