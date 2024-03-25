CREATE TABLE `complaint_report` (
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

CREATE TABLE `medical_report` (
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

CREATE TABLE `arrest_report` (
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

CREATE TABLE `entity` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `primary_indentity_id` integer
);

CREATE TABLE `identity` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `primary_entity_id` integer,
  `first_name` varchar(35),
  `middle_name` varchar(35),
  `last_name` varchar(35),
  `alias` varchar(35),
  `date_of_birth` date,
  `last_known_residence` integer,
  `tel_number` integer,
  `email` varchar(320)
);

CREATE TABLE `employee` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `entity_id` integer NOT NULL,
  `title` varchar(35) NOT NULL
);

CREATE TABLE `location` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `address_id` integer,
  `geo_lat` decimal,
  `geo_long` decimal,
  `primary_street` varchar(35),
  `secondary_street` varchar(35),
  `tertiary_street` varchar(35),
  `city` varchar(35),
  `region` varchar(35),
  `country_code` varchar(3),
  `fulltext_desc` varchar(512)
);

CREATE TABLE `address` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `building_number` varchar(20) NOT NULL,
  `unit_number` varchar(20),
  `street_name` varchar(35) NOT NULL,
  `city_name` varchar(35) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `state_code` varchar(3) NOT NULL,
  `mailing_address_fulltext` varchar(255)
);

CREATE TABLE `event` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `occurance_began` datetime,
  `occurance_ceased` datetime
);

CREATE TABLE `crime` (
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

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `complaint_report` (`occurance_location_id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`injured_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`inujured_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`taken_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `medical_report` (`occurance_location_id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`crime_id`) REFERENCES `crime` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arrestee_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arrestee_identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arresst_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `arrest_report` (`occurance_location_id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`id`) REFERENCES `entity` (`primary_indentity_id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`primary_entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`last_known_residence`) REFERENCES `location` (`id`);

ALTER TABLE `entity` ADD FOREIGN KEY (`id`) REFERENCES `employee` (`entity_id`);

ALTER TABLE `address` ADD FOREIGN KEY (`id`) REFERENCES `location` (`address_id`);
