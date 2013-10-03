DROP SCHEMA IF EXISTS GeoChat CASCADE;

CREATE SCHEMA GeoChat;

DROP TABLE IF EXISTS Authentication CASCADE;
DROP TABLE IF EXISTS Users CASCADE;
DROP TABLE IF EXISTS Messages CASCADE;

CREATE TABLE Authentication(
	userName 	varchar(15) NOT NULL PRIMARY KEY,
	passwordHash 	CHAR(40) NOT NULL,
	salt 		CHAR(40) NOT NULL,
	UNIQUE(userName)
);

CREATE TABLE Users(
	userId 		SERIAL NOT NULL PRIMARY KEY,
	userName 	varchar(15) REFERENCES Authentication(userName),
	location 	GEOMETRY(point)
);

CREATE TABLE Messages(
	messageId 	SERIAL NOT NULL PRIMARY KEY,
	senderId 	numeric(5,0) REFERENCES Users(userId), 
	message 	varchar(300) NOT NULL,
	location 	GEOMETRY(point),
	radius 		numeric(2,0),
	time 		TIMESTAMP NOT NULL
);

