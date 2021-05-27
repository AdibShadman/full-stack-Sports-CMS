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
