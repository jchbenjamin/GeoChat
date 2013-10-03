INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ('mad', '9b3c6623dc00e4acc2caf60083edbff59a56362a', 'iz1IO9D8AX0a2wrbeb3h');
INSERT INTO GeoChat.Users(userName, location) VALUES ('mad', ST_SetSRID((ST_MakePoint(38.235643,82.1253)), 4326));

INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ('hey', '4eeaaf7b1d04f66d33cb36061c9605e5d34104d4', 'obiVrtMBtZgipTAQBqSG');
INSERT INTO GeoChat.Users(userName, location) VALUES ('hey', ST_SetSRID((ST_MakePoint(38.23464, 82.15324)), 4326));

INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES('paige', '6d2519ee12108f0a463a6e26052498cbe0dab9d5', 'HFE0pZa5nIGRpGriq3Hg');
INSERT INTO GeoChat.Users(userName, location) VALUES ('paige', ST_SetSRID((ST_MakePoint(38.1243,82.12453)), 4326));

INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ('becca', '4f9b567b2cc2026f73b795629482c344964032e1', 'q2Ka0DEkOv2anKbqwbck');
INSERT INTO GeoChat.Users(userName, location) VALUES ('becca', ST_SetSRID((ST_MakePoint(38.916682,82.318115)), 4326));

INSERT INTO GeoChat.Authentication(userName, passwordHash, salt) VALUES ('hi', 'de41451fce279f44fba7888e1fb1d071127a5487', 'FMAve0ufsRXhYaxUnAax');
INSERT INTO GeoChat.Users(userName, location) VALUES ('hi', ST_SetSRID((ST_MakePoint(38.5264,82.73253)), 4326));


INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='mad'), 'Hello all <3 mad', (SELECT location FROM GeoChat.Users WHERE username='mad'), 3, '2013-04-22 10:26:41');

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='mad'), 'HI again im just making some test data. nbd.', (SELECT location FROM GeoChat.Users WHERE username='mad'), 3, '2013-04-22 10:34:44');

UPDATE GeoChat.Users SET location = ST_SetSRID((ST_MakePoint(38.9873, 82.16492)), 4326) WHERE username='mad';

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='mad'), 'just hanging out', (SELECT location FROM GeoChat.Users WHERE username='mad'), 3, '2013-04-22 10:35:23');

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='hey'), 'heyheyheyheyheyheyheyehyheyasldkhgoblasdfkjaksdljflienialdkfh', (SELECT location FROM GeoChat.Users WHERE username='hey'), 10, '2013-04-22 10:38:52');

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='paige'), 'hey guys paige is awesome and is taking notes for me while i make this stupid test file', (SELECT location FROM GeoChat.Users WHERE username='paige'), 3, '2013-04-22 10:43:23');

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='becca'), 'hi my name is beeca and i am sitting in front of madison right now.', (SELECT location FROM GeoChat.Users WHERE username='becca'), 10, '2013-04-22 10:44:12');

INSERT INTO GeoChat.Messages(senderid, message, location, radius, time) VALUES ((SELECT userId FROM GeoChat.Users WHERE username='hi'), 'hi hi hi hi hi hi hi hi hi', (SELECT location FROM GeoChat.Users WHERE username='hi'), 6, '2013-04-22 10:47:25');

