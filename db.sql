/* Create database */
CREATE DATABASE test_db CHARACTER SET latin1 COLLATE latin1_swedish_ci;

/* Create tables */
CREATE TABLE test_db.languages(
	`Language` VARCHAR (30) NOT NULL PRIMARY KEY
);

CREATE TABLE test_db.users(
	`Id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Name` VARCHAR (30) NOT NULL,
	`Birthdate` DATE,
	`ID code / SSN` VARCHAR(11),
	`Is a current employee` TINYINT(1),
	`Email` VARCHAR(100),
	`Phone` VARCHAR(20),
	`Address` VARCHAR(100)
);

CREATE TABLE test_db.users_log(
	`Id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Entry` INTEGER,
	`Entity` VARCHAR(100),
	`Who` VARCHAR(100),
	`Action` VARCHAR(20),
	`Time` DATETIME
);

CREATE TABLE test_db.user_personal_info(
	`Id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Introduction` TEXT NOT NULL DEFAULT "",
	`Previous work experience` TEXT NOT NULL DEFAULT "",
	`Education information` TEXT NOT NULL DEFAULT "",
	`Language` VARCHAR (30),
	`User_id` INTEGER
);

/* Add foreign keys */
ALTER TABLE test_db.user_personal_info
ADD FOREIGN KEY (`Language`) REFERENCES languages(`Language`),
ADD FOREIGN KEY (`User_id`) REFERENCES users(`Id`);

/* Create log triggers */
CREATE TRIGGER test_db.after_users_insert
    AFTER INSERT ON test_db.users
    FOR EACH ROW
INSERT INTO test_db.users_log
SET Action = 'insert',
    Entry = New.Id,
    Entity = 'users',
    Who = USER(),
    Time = NOW();

CREATE TRIGGER test_db.after_users_update
    AFTER UPDATE ON test_db.users
    FOR EACH ROW
INSERT INTO test_db.users_log
SET Action = 'update',
    Entry = New.Id,
    Entity = 'users',
    Who = USER(),
    Time = NOW();

CREATE TRIGGER test_db.after_user_personal_info_insert
    AFTER INSERT ON test_db.user_personal_info
    FOR EACH ROW
INSERT INTO test_db.users_log
SET Action = 'insert',
    Entry = New.Id,
    Entity = 'user_personal_info',
    Who = USER(),
    Time = NOW();

CREATE TRIGGER test_db.after_user_personal_info_update
    AFTER UPDATE ON test_db.user_personal_info
    FOR EACH ROW
INSERT INTO test_db.users_log
SET Action = 'update',
    Entry = New.Id,
    Entity = 'user_personal_info',
    Who = USER(),
    Time = NOW();

/* Insert custom data */
INSERT INTO test_db.languages (`Language`) VALUES ("English"),("Spanish"),("French");

INSERT INTO test_db.users (`Name`,`Birthdate`,`ID code / SSN`,`Is a current employee`,`Email`,`Phone`,`Address`) VALUES
("Test","1994-09-20","12345678900",true,"test@test.test","+37211111111","A. H. Tammsaare tee 104a, 12918 Tallinn");

SET @user_id = LAST_INSERT_ID();

INSERT INTO test_db.user_personal_info (`Introduction`,`Previous work experience`,`Education information`,`Language`,`User_id`) VALUES
("Introduction data","Previous work experience data","Education information data","English", @user_id),
("Datos de introducción","Datos de experiencia laboral previa","Datos de información sobre educación","Spanish", @user_id),
("Données d'introduction","Données d'expérience de travail antérieures","Information sur l'éducation","French", @user_id);

/* Select data for last created user (please take care of user_id variable if you going to use it separately) */
SELECT test_db.users.*, test_db.user_personal_info.Introduction, test_db.user_personal_info.`Previous work experience`, test_db.user_personal_info.`Education information`
FROM test_db.users
INNER JOIN test_db.user_personal_info ON test_db.users.id=test_db.user_personal_info.User_id WHERE test_db.users.Id=@user_id;
