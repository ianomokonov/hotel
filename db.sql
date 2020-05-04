CREATE TABLE IF NOT EXISTS user(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    isAdmin bit DEFAULT 0,
    name varchar(255) NOT NULL,
    surname varchar(255) NOT NULL,
    middlename varchar(255),
    email varchar(255) NOT NULL,
    password varchar(255) NOT NUll,
    points int(10) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS course(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    description text NOT NULL,
    link varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS question(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    courseId int(10) NOT NULL,
    title varchar(255) NOT NULL,
    FOREIGN KEY (courseId) REFERENCES course(id)
);

CREATE TABLE IF NOT EXISTS answer(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    questionId int(10) NOT NULL,
    title varchar(255) NOT NULL,
    isRight bit DEFAULT 0,
    FOREIGN KEY (questionId) REFERENCES question(id)
);

CREATE TABLE IF NOT EXISTS userAnswer(
    id int(10) PRIMARY KEY AUTO_INCREMENT,
    userId int(10) NOT NULL,
    questionId int(10) NOT NULL,
    answerId int(10) NOT NULL,
    FOREIGN KEY (userId) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (questionId) REFERENCES question(id) ON DELETE CASCADE,
    FOREIGN KEY (answerId) REFERENCES answer(id) ON DELETE CASCADE
);