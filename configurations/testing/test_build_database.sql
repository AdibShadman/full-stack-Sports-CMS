CREATE DATABASE sports_cms;
USE sports_cms;

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL UNIQUE,
  PRIMARY KEY (`country_id`)
);

CREATE TABLE IF NOT EXISTS `state` (
  `state_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `country_id` INT NOT NULL,
  PRIMARY KEY (`state_id`),
  FOREIGN KEY (`country_id`) REFERENCES country(country_id)
);

CREATE TABLE IF NOT EXISTS `sport` (
  `sport_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`sport_id`)
);

CREATE TABLE IF NOT EXISTS `player` (
  `player_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `given_name` VARCHAR(45) NOT NULL,
  `family_name` VARCHAR(45) NOT NULL,
  `gender` VARCHAR(1) NOT NULL CHECK (gender in ('M', 'F')),
  `date_of_birth` DATE NOT NULL,
  `email` VARCHAR(75) NOT NULL UNIQUE,
  `last_played` DATETIME,
  `receive_emails` VARCHAR(1) NOT NULL DEFAULT 'Y'  CHECK (receive_emails IN ('Y', 'N')),
  `country_id` INT NOT NULL,
  `state_id` INT NOT NULL,
  PRIMARY KEY (`player_id`),
  FOREIGN KEY (`country_id`) REFERENCES country(country_id),
  FOREIGN KEY (`state_id`) REFERENCES state(state_id)
);

CREATE TABLE IF NOT EXISTS `club` (
  `club_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(90) NOT NULL UNIQUE,
  `country_id` INT NOT NULL,
  `state_id` INT NOT NULL,
  `sport_id` INT NOT NULL,
  `club_exp` date NOT NULL,
  PRIMARY KEY (`club_id`),
  FOREIGN KEY (`country_id`) REFERENCES country(country_id),
  FOREIGN KEY (`state_id`) REFERENCES state(state_id),
  FOREIGN KEY (`sport_id`) REFERENCES sport(sport_id)
);

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(90) NOT NULL,
  `type` VARCHAR(10) NOT NULL CHECK (type in ('Single', 'Double')),
  `start_date` DATE NOT NULL,
  `country_id` INT NOT NULL,
  `state_id` INT NOT NULL,
  `sport_id` INT NOT NULL,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`country_id`) REFERENCES country(country_id),
  FOREIGN KEY (`state_id`) REFERENCES state(state_id),
  FOREIGN KEY (`sport_id`) REFERENCES sport(sport_id)
);

CREATE TABLE IF NOT EXISTS `plays_at` (
  `club_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  PRIMARY KEY (`club_id`, `event_id`),
  FOREIGN KEY (`club_id`) REFERENCES club(club_id),
  FOREIGN KEY (`event_id`) REFERENCES event(event_id)
);

CREATE TABLE IF NOT EXISTS `team` (
  `team_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `player_one_id` INT NOT NULL,
  `player_two_id` INT NOT NULL,
  PRIMARY KEY (`team_id`, `player_one_id`, `player_two_id`),
  FOREIGN KEY (`player_one_id`) REFERENCES player(player_id),
  FOREIGN KEY (`player_two_id`) REFERENCES player(player_id)
);

CREATE TABLE IF NOT EXISTS `rating` (
  `rating_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `mean` DOUBLE NOT NULL,
  `standard_deviation` DOUBLE NOT NULL,
  `last_calculated` DATETIME NOT NULL,
  `sport_id` INT NOT NULL,
  `player_id` INT DEFAULT NULL,
  `team_id` INT DEFAULT NULL,
  PRIMARY KEY (`rating_id`, `sport_id`),
  FOREIGN KEY (`sport_id`) REFERENCES sport(sport_id),
  FOREIGN KEY (`player_id`) REFERENCES player(player_id),
  FOREIGN KEY (`team_id`) REFERENCES team(team_id)
);

CREATE TABLE IF NOT EXISTS `game` (
  `game_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `mean_before_winning` DOUBLE NOT NULL,
  `mean_after_winning` DOUBLE,
  `standard_deviation_before_winning` DOUBLE NOT NULL,
  `standard_deviation_after_winning` DOUBLE,
  `mean_before_losing` DOUBLE NOT NULL,
  `mean_after_losing` DOUBLE,
  `standard_deviation_before_losing` DOUBLE NOT NULL,
  `standard_deviation_after_losing` DOUBLE,
  `winner_score` INT,
  `loser_score` INT,
  `event_id` INT NOT NULL,
  PRIMARY KEY (`game_id`),
  FOREIGN KEY (`event_id`) REFERENCES event(event_id)
);

CREATE TABLE IF NOT EXISTS `game_result` (
  `game_result_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `won` VARCHAR(1) NOT NULL CHECK (won IN ('Y', 'N')),
  `player_id` INT,
  `team_id` INT,
  `game_id` INT NOT NULL,
  PRIMARY KEY (`game_result_id`),
  FOREIGN KEY (`player_id`) REFERENCES player(player_id),
  FOREIGN KEY (`team_id`) REFERENCES team(team_id),
  FOREIGN KEY (`game_id`) REFERENCES game(game_id)
);

CREATE TABLE IF NOT EXISTS `account` (
  `account_id` INT NOT NULL UNIQUE AUTO_INCREMENT,
  `given_name` VARCHAR(45) NOT NULL,
  `family_name` VARCHAR(45) NOT NULL,
  `organisation` VARCHAR(90) NOT NULL,
  `email` VARCHAR(75) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `token` VARCHAR(45),
  `token_expiration_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `access_level` TINYINT NOT NULL DEFAULT '2' CHECK (access_level IN ('0', '1', '2')),
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` VARCHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`account_id`)
);

CREATE TABLE IF NOT EXISTS `membership` (
  `club_id` INT NOT NULL,
  `player_id` INT NOT NULL,
  PRIMARY KEY (`club_id`, `player_id`),
  FOREIGN KEY (`club_id`) REFERENCES club(club_id),
  FOREIGN KEY (`player_id`) REFERENCES player(player_id)
);

CREATE TABLE IF NOT EXISTS `director_of` (
  `account_id` INT NOT NULL,
  `club_id` INT NOT NULL,
  PRIMARY KEY (`account_id`, `club_id`),
  FOREIGN KEY (`account_id`) REFERENCES account(account_id),
  FOREIGN KEY (`club_id`) REFERENCES club(club_id)
);


insert into `country`(name) VALUES ('Australia');
insert into `country`(name) VALUES ('New Zealand');

insert into `state`(name, country_id) VALUES ('Tasmania', '1');
insert into `state`(name, country_id) VALUES ('Western Australia', '1');
insert into `state`(name, country_id) VALUES ('Auckland', '2');
insert into `state`(name, country_id) VALUES ('Otago', '2');

insert into `sport` (name) VALUES ('Badminton');
insert into `sport` (name) VALUES ('Squash');
insert into `sport` (name) VALUES ('Tennis');

insert into `club` (name, country_id, state_id,sport_id) VALUES ('Launceston Badminton Club', 1, 1,1);
insert into `club` (name, country_id, state_id, sport_id) VALUES ('Otago Squash Club', 2, 4, 2);

insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('John', 'Smith', 'M', '1993-03-17', 'Sean.Allen@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 1, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 1, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 1, null);
insert into `membership` (club_id, player_id) VALUES (1, 1);

insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Earl', 'Taylor', 'M', NOW(), 'Earl.Taylor@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 2, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 2, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 2, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Jeremy', 'Perez', 'M', NOW(), 'Jeremy.Perez@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 3, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 3, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 3, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Raymond', 'Gonzales', 'M', NOW(), 'Raymond.Gonzales@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 4, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 4, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 4, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Todd', 'Flores', 'M', NOW(), 'Todd.Flores@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 5, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 5, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 5, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Philip', 'Thompson', 'M', NOW(), 'Philip.Thompson@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 6, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 6, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 6, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kevin', 'Young', 'M', NOW(), 'Kevin.Young@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 7, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 7, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 7, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kenneth', 'Evans', 'M', NOW(), 'Kenneth.Evans@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 8, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 8, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 8, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Benjamin', 'Miller', 'M', NOW(), 'Benjamin.Miller@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 9, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 9, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 9, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Fred', 'Bailey', 'M', NOW(), 'Fred.Bailey@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 10, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 10, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 10, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Louis', 'Rivera', 'M', NOW(), 'Louis.Rivera@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 11, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 11, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 11, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Christopher', 'Jenkins', 'M', NOW(), 'Christopher.Jenkins@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 12, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 12, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 12, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Eugene', 'Lewis', 'M', NOW(), 'Eugene.Lewis@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 13, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 13, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 13, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Robert', 'Williams', 'M', NOW(), 'Robert.Williams@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 14, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 14, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 14, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Douglas', 'Johnson', 'M', NOW(), 'Douglas.Johnson@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 15, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 15, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 15, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Larry', 'Martin', 'M', NOW(), 'Larry.Martin@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 16, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 16, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 16, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('David', 'Barnes', 'M', NOW(), 'David.Barnes@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 17, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 17, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 17, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Carl', 'Perry', 'M', NOW(), 'Carl.Perry@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 18, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 18, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 18, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Johnny', 'Sanders', 'M', NOW(), 'Johnny.Sanders@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 19, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 19, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 19, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('William', 'Hughes', 'M', NOW(), 'William.Hughes@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 20, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 20, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 20, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Harry', 'Price', 'M', NOW(), 'Harry.Price@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 21, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 21, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 21, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Victor', 'Campbell', 'M', NOW(), 'Victor.Campbell@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 22, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 22, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 22, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Richard', 'Morgan', 'M', NOW(), 'Richard.Morgan@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 23, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 23, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 23, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Gerald', 'Ramirez', 'M', NOW(), 'Gerald.Ramirez@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 24, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 24, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 24, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Ernest', 'Clark', 'M', NOW(), 'Ernest.Clark@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 25, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 25, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 25, null);

insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Craig', 'Walker', 'M', NOW(), 'Craig.Walker@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 26, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 26, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 26, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Peter', 'Baker', 'M', NOW(), 'Peter.Baker@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 27, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 27, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 27, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Edward', 'Martin', 'M', NOW(), 'Edward.Martin@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 28, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 28, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 28, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Nicholas', 'Carter', 'M', NOW(), 'Nicholas.Carter@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 29, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 29, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 29, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Keith', 'Campbell', 'M', NOW(), 'Keith.Campbell@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 30, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 30, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 30, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Justin', 'Flores', 'M', NOW(), 'Justin.Flores@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 31, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 31, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 31, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Carlos', 'Price', 'M', NOW(), 'Carlos.Price@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 32, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 32, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 32, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Mark', 'Roberts', 'M', NOW(), 'Mark.Roberts@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 33, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 33, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 33, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Juan', 'Murphy', 'M', NOW(), 'Juan.Murphy@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 34, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 34, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 34, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Adam', 'Rodriguez', 'M', NOW(), 'Adam.Rodriguez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 35, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 35, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 35, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Andrew', 'Hill', 'M', NOW(), 'Andrew.Hill@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 36, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 36, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 36, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Alan', 'Butler', 'M', NOW(), 'Alan.Butler@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 37, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 37, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 37, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Larry', 'Bennett', 'M', NOW(), 'Larry.Bennett@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 38, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 38, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 38, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Jerry', 'Brown', 'M', NOW(), 'Jerry.Brown@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 39, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 39, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 39, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Earl', 'Young', 'M', NOW(), 'Earl.Young@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 40, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 40, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 40, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Gary', 'Torres', 'M', NOW(), 'Gary.Torres@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 41, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 41, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 41, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Dennis', 'James', 'M', NOW(), 'Dennis.James@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 42, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 42, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 42, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Samuel', 'Harris', 'M', NOW(), 'Samuel.Harris@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 43, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 43, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 43, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Charles', 'Simmons', 'M', NOW(), 'Charles.Simmons@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 44, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 44, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 44, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Terry', 'Edwards', 'M', NOW(), 'Terry.Edwards@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 45, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 45, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 45, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('George', 'Collins', 'M', NOW(), 'George.Collins@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 46, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 46, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 46, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Jeffrey', 'Bell', 'M', NOW(), 'Jeffrey.Bell@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 47, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 47, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 47, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Raymond', 'Johnson', 'M', NOW(), 'Raymond.Johnson@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 48, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 48, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 48, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Ryan', 'Smith', 'M', NOW(), 'Ryan.Smith@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 49, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 49, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 49, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kenneth', 'Phillips', 'M', NOW(), 'Kenneth.Phillips@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 50, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 50, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 50, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Andrea', 'Patterson', 'F', NOW(), 'Andrea.Patterson@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 51, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 51, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 51, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Jean', 'Sanchez', 'F', NOW(), 'Jean.Sanchez@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 52, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 52, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 52, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Julie', 'Reed', 'F', NOW(), 'Julie.Reed@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 53, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 53, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 53, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Karen', 'Russell', 'F', NOW(), 'Karen.Russell@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 54, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 54, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 54, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Nicole', 'Barnes', 'F', NOW(), 'Nicole.Barnes@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 55, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 55, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 55, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Julia', 'Bell', 'F', NOW(), 'Julia.Bell@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 56, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 56, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 56, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Heather', 'Flores', 'F', NOW(), 'Heather.Flores@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 57, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 57, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 57, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Sarah', 'Gonzalez', 'F', NOW(), 'Sarah.Gonzalez@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 58, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 58, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 58, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Judith', 'Bailey', 'F', NOW(), 'Judith.Bailey@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 59, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 59, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 59, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Amanda', 'James', 'F', NOW(), 'Amanda.James@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 60, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 60, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 60, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Christine', 'Long', 'F', NOW(), 'Christine.Long@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 61, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 61, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 61, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Ashley', 'Wood', 'F', NOW(), 'Ashley.Wood@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 62, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 62, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 62, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Teresa', 'Diaz', 'F', NOW(), 'Teresa.Diaz@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 63, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 63, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 63, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Martha', 'Green', 'F', NOW(), 'Martha.Green@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 64, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 64, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 64, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Rebecca', 'Watson', 'F', NOW(), 'Rebecca.Watson@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 65, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 65, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 65, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Carol', 'Ramirez', 'F', NOW(), 'Carol.Ramirez@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 66, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 66, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 66, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Irene', 'Harris', 'F', NOW(), 'Irene.Harris@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 67, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 67, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 67, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Lisa', 'Martin', 'F', NOW(), 'Lisa.Martin@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 68, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 68, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 68, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Louise', 'Cooper', 'F', NOW(), 'Louise.Cooper@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 69, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 69, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 69, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Rachel', 'King', 'F', NOW(), 'Rachel.King@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 70, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 70, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 70, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Cynthia', 'Miller', 'F', NOW(), 'Cynthia.Miller@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 71, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 71, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 71, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Janet', 'Roberts', 'F', NOW(), 'Janet.Roberts@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 72, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 72, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 72, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kathryn', 'Stewart', 'F', NOW(), 'Kathryn.Stewart@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 73, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 73, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 73, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Elizabeth', 'Simmons', 'F', NOW(), 'Elizabeth.Simmons@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 74, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 74, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 74, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Diane', 'Richardson', 'F', NOW(), 'Diane.Richardson@testonly.com', NOW(), 'Y', '1', '1');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 75, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 75, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 75, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Betty', 'Gonzales', 'F', NOW(), 'Betty.Gonzales@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 76, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 76, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 76, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Margaret', 'Bell', 'F', NOW(), 'Margaret.Bell@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 77, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 77, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 77, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Julie', 'Ramirez', 'F', NOW(), 'Julie.Ramirez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 78, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 78, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 78, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Christina', 'Hall', 'F', NOW(), 'Christina.Hall@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 79, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 79, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 79, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kelly', 'Washington', 'F', NOW(), 'Kelly.Washington@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 80, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 80, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 80, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kathleen', 'Kelly', 'F', NOW(), 'Kathleen.Kelly@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 81, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 81, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 81, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Carolyn', 'Cook', 'F', NOW(), 'Carolyn.Cook@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 82, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 82, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 82, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Virginia', 'Gonzalez', 'F', NOW(), 'Virginia.Gonzalez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 83, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 83, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 83, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Ruth', 'Walker', 'F', NOW(), 'Ruth.Walker@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 84, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 84, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 84, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Pamela', 'Rodriguez', 'F', NOW(), 'Pamela.Rodriguez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 85, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 85, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 85, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Patricia', 'Griffin', 'F', NOW(), 'Patricia.Griffin@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 86, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 86, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 86, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Sarah', 'Coleman', 'F', NOW(), 'Sarah.Coleman@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 87, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 87, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 87, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Alice', 'Cooper', 'F', NOW(), 'Alice.Cooper@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 88, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 88, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 88, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Laura', 'Richardson', 'F', NOW(), 'Laura.Richardson@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 89, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 89, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 89, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Susan', 'Taylor', 'F', NOW(), 'Susan.Taylor@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 90, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 90, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 90, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Katherine', 'Hernandez', 'F', NOW(), 'Katherine.Hernandez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 91, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 91, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 91, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Phyllis', 'Rogers', 'F', NOW(), 'Phyllis.Rogers@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 92, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 92, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 92, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Cheryl', 'Ross', 'F', NOW(), 'Cheryl.Ross@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 93, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 93, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 93, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Brenda', 'Brown', 'F', NOW(), 'Brenda.Brown@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 94, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 94, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 94, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Kimberly', 'Martinez', 'F', NOW(), 'Kimberly.Martinez@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 95, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 95, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 95, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Teresa', 'Smith', 'F', NOW(), 'Teresa.Smith@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 96, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 96, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 96, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Diane', 'King', 'F', NOW(), 'Diane.King@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 97, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 97, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 97, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Janice', 'Roberts', 'F', NOW(), 'Janice.Roberts@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 98, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 98, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 98, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Doris', 'Perry', 'F', NOW(), 'Doris.Perry@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 99, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 99, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 99, null);


insert into `player` (given_name, family_name, gender, date_of_birth, email, last_played, receive_emails, country_id, state_id)
  VALUES ('Judy', 'Flores', 'F', NOW(), 'Judy.Flores@testonly.com', NOW(), 'Y', '1', '2');
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 1, 100, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 2, 100, null);
insert into `rating` (mean, standard_deviation, last_calculated, sport_id, player_id, team_id) VALUES (2500, 173, NOW(), 3, 100, null);

-- password = Abc123
INSERT INTO `account` (`given_name`, `family_name`, `organisation`, `email`, `password`, `access_level`, `date_created`, `active`) VALUES ('Test', 'User', 'Fake Club', 'test@user.com', '$2y$10$iLapRftpH.OTlQZ0vXhAIOmv1X7U.mk3HIICY7XDCfWZJ4zyBTa9u', '2', '2019-05-14 22:45:55', 'Y');

-- password = Abc123
INSERT INTO `account` (`given_name`, `family_name`, `organisation`, `email`, `password`, `access_level`, `date_created`, `active`) VALUES ('Disabled', 'Account', 'Fake Club', 'locked@account.com', '$2y$10$p52ay93RXErJOVSCOCwBbe60bRdZfZXG29bZ232xpdaqAXcORj.xq', '2', '2019-05-14 22:48:47', 'N');

INSERT INTO `account` (`given_name`, `family_name`, `organisation`, `email`, `password`, `access_level`, `date_created`, `active`) VALUES ('Grant', 'Upson', 'University of Tasmania', 'gupson@utas.edu.au', '$2y$10$7ODsm/dnPQamRGTGeVmg4ezNJS0LUe/QYvZcEjxPEMMygEjeQpF6m', '0', '2019-05-14 22:48:47', 'Y');
