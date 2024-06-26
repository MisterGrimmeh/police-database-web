CREATE TABLE
  `complaint_report` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `event_id` integer,
    `crime_id` integer,
    `perpetrator_entity_id` integer,
    `perpetrator_identity_id` integer,
    `victim_entity_id` integer,
    `victim_identity_id` integer,
    `reporter_entity_id` integer,
    `reporter_identity_id` integer,
    `taken_by_employee_id` integer NOT NULL,
    `entered_by_employee_id` integer NOT NULL,
    `occurance_location_id` integer,
    `occurance_began` datetime,
    `occurance_ceased` datetime
  );

CREATE TABLE
  `medical_report` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `event_id` integer,
    `complaint_id` integer NOT NULL,
    `injured_entity_id` integer,
    `inujured_identity_id` integer,
    `taken_by_employee_id` integer NOT NULL,
    `entered_by_employee_id` integer NOT NULL,
    `occurance_location_id` integer,
    `when_injured` datetime
  );

CREATE TABLE
  `arrest_report` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `event_id` integer,
    `crime_id` integer,
    `complaint_id` integer NOT NULL,
    `arrestee_entity_id` integer NOT NULL,
    `arrestee_identity_id` integer NOT NULL,
    `arresst_by_employee_id` integer NOT NULL,
    `entered_by_employee_id` integer NOT NULL,
    `occurance_location_id` integer NOT NULL,
    `when_occurred` datetime NOT NULL
  );

CREATE TABLE
  `entity` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `primary_identity_id` integer
  );

CREATE TABLE
  `identity` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `primary_entity_id` integer,
    `first_name` varchar(35),
    `middle_name` varchar(35),
    `last_name` varchar(35),
    `alias` varchar(35),
    `date_of_birth` date,
    `last_known_residence` integer,
    `tel_number` varchar(15),
    `email` varchar(320)
  );

CREATE TABLE
  `employee` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `entity_id` integer NOT NULL,
    `title` varchar(35) NOT NULL
  );

CREATE TABLE
  `location` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `address_id` integer,
    `geo_lat` decimal(8,6),
    `geo_long` decimal(9,6),
    `primary_street` varchar(35),
    `secondary_street` varchar(35),
    `tertiary_street` varchar(35),
    `city` varchar(35),
    `region` varchar(35),
    `country_code` varchar(3),
    `fulltext_desc` varchar(512),
    CHECK (`geo_lat` BETWEEN 90.0 AND -90.0),
    CHECK (`geo_long` BETWEEN 180.0 AND -180.0)
  );

CREATE TABLE
  `address` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `building_number` varchar(20) NOT NULL,
    `unit_number` varchar(20),
    `street_name` varchar(35) NOT NULL,
    `city_name` varchar(35) NOT NULL,
    `postal_code` varchar(10) NOT NULL,
    `state_code` varchar(3) NOT NULL,
    `mailing_address_fulltext` varchar(255)
  );

CREATE TABLE
  `event` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `occurance_began` datetime,
    `occurance_ceased` datetime
  );

CREATE TABLE
  `crime` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `title` varchar(255),
    `code` varchar(40) NOT NULL,
    `class` varchar(1) NOT NULL
  );

CREATE INDEX `report_number` ON `complaint_report` (`id`, `event_id`);

CREATE INDEX `report_number` ON `medical_report` (`id`, `event_id`);

CREATE INDEX `report_number` ON `arrest_report` (`id`, `event_id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`crime_id`) REFERENCES `crime` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`perpetrator_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`perpetrator_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`victim_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`victim_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`reporter_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`reporter_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`taken_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`occurance_location_id`) REFERENCES `location` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`injured_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`inujured_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`taken_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`occurance_location_id`) REFERENCES `location` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`crime_id`) REFERENCES `crime` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arrestee_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arrestee_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arresst_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`occurance_location_id`) REFERENCES `location` (`id`);

ALTER TABLE `entity` ADD FOREIGN KEY (`primary_indentity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`primary_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`last_known_residence`) REFERENCES `location` (`id`);

ALTER TABLE `employee` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`address_id`) REFERENCES `address` (`id`);

CREATE VIEW
  open_complaint AS
SELECT
  *
FROM
  `complaint_report`
WHERE
  `occurance_ceased` IS NULL;

CREATE VIEW
  show_employees AS
SELECT
  `entity`.`id` AS `entity_id`,
  `employee`.`id` AS `employee_id`,
  `first_name`,
  `middle_name`,
  `last_name`,
  `alias`,
  `date_of_birth`,
  `last_known_residence`,
  `tel_number`,
  `email`
FROM
  `identity`
  JOIN `entity` ON `identity`.`id` = `entity`.`primary_identity_id`
  JOIN `employee` ON `entity`.`id` = `employee`.`entity_id`;

DELIMITER //

CREATE FUNCTION number_identities_for_entity (entity_id int) RETURNS int DETERMINISTIC BEGIN

DECLARE number_of_identities INT;

SELECT
  COUNT(*)
FROM
  `identity`
WHERE
  `primary_entity_id` = entity_id INTO number_of_identities;

RETURN number_of_identities;

END //

CREATE PROCEDURE GetIdentityForEntity (IN entity_id int) BEGIN

SELECT
  *,
  number_identities_for_entity (entity_id) AS number_of_identities
FROM
  `identity`
WHERE
  `id` = (
    SELECT
      `primary_identity_id`
    FROM
      `entity`
    WHERE
      `id` = entity_id
  );

END //

CREATE PROCEDURE GetAllIdentitiesForEntity (IN entity_id int) BEGIN

SELECT
  *
FROM
  `identity`
WHERE
  `primary_entity_id` = entity_id;

END //

CREATE TRIGGER create_placeholder_id AFTER INSERT ON `entity` FOR EACH ROW BEGIN

INSERT INTO
  `identity` (`primary_entity_id`)
VALUES
  (NEW.id);

END //

DELIMITER ;