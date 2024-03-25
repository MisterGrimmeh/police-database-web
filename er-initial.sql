CREATE TABLE `complaint_report` (
  `id` integer UNIQUE AUTO_INCREMENT,
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
  `occurance_ceased` datetime,
  PRIMARY KEY (`id`, `event_id`)
);

CREATE TABLE `medical_report` (
  `id` integer UNIQUE AUTO_INCREMENT,
  `event_id` integer,
  `complaint_id` integer NOT NULL,
  `injured_entity_id` integer,
  `inujured_identity_id` integer,
  `taken_by_employee_id` integer NOT NULL,
  `entered_by_employee_id` integer NOT NULL,
  `occurance_location_id` integer,
  `when_injured` datetime,
  PRIMARY KEY (`id`, `event_id`)
);

CREATE TABLE `arrest_report` (
  `id` integer UNIQUE AUTO_INCREMENT,
  `event_id` integer,
  `crime_id` integer,
  `complaint_id` integer NOT NULL,
  `arrestee_entity_id` integer NOT NULL,
  `arrestee_identity_id` integer NOT NULL,
  `arresst_by_employee_id` integer NOT NULL,
  `entered_by_employee_id` integer NOT NULL,
  `occurance_location_id` integer NOT NULL,
  `when_occurred` datetime NOT NULL,
  PRIMARY KEY (`id`, `event_id`)
);

CREATE TABLE `entity` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `primary_indentity_id` integer
);

CREATE TABLE `identity` (
  `id` interger PRIMARY KEY AUTO_INCREMENT,
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

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

CREATE TABLE `crime_complaint_report` (
  `crime_id` integer,
  `complaint_report_crime_id` integer,
  PRIMARY KEY (`crime_id`, `complaint_report_crime_id`)
);

ALTER TABLE `crime_complaint_report` ADD FOREIGN KEY (`crime_id`) REFERENCES `crime` (`id`);

ALTER TABLE `crime_complaint_report` ADD FOREIGN KEY (`complaint_report_crime_id`) REFERENCES `complaint_report` (`crime_id`);


CREATE TABLE `entity_complaint_report` (
  `entity_id` integer,
  `complaint_report_perpetrator_entity_id` integer,
  PRIMARY KEY (`entity_id`, `complaint_report_perpetrator_entity_id`)
);

ALTER TABLE `entity_complaint_report` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `entity_complaint_report` ADD FOREIGN KEY (`complaint_report_perpetrator_entity_id`) REFERENCES `complaint_report` (`perpetrator_entity_id`);


CREATE TABLE `identity_complaint_report` (
  `identity_id` interger,
  `complaint_report_perpetrator_identity_id` integer,
  PRIMARY KEY (`identity_id`, `complaint_report_perpetrator_identity_id`)
);

ALTER TABLE `identity_complaint_report` ADD FOREIGN KEY (`identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity_complaint_report` ADD FOREIGN KEY (`complaint_report_perpetrator_identity_id`) REFERENCES `complaint_report` (`perpetrator_identity_id`);


CREATE TABLE `entity_complaint_report(1)` (
  `entity_id` integer,
  `complaint_report_victim_entity_id` integer,
  PRIMARY KEY (`entity_id`, `complaint_report_victim_entity_id`)
);

ALTER TABLE `entity_complaint_report(1)` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `entity_complaint_report(1)` ADD FOREIGN KEY (`complaint_report_victim_entity_id`) REFERENCES `complaint_report` (`victim_entity_id`);


CREATE TABLE `identity_complaint_report(1)` (
  `identity_id` interger,
  `complaint_report_victim_identity_id` integer,
  PRIMARY KEY (`identity_id`, `complaint_report_victim_identity_id`)
);

ALTER TABLE `identity_complaint_report(1)` ADD FOREIGN KEY (`identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity_complaint_report(1)` ADD FOREIGN KEY (`complaint_report_victim_identity_id`) REFERENCES `complaint_report` (`victim_identity_id`);


CREATE TABLE `entity_complaint_report(2)` (
  `entity_id` integer,
  `complaint_report_reporter_entity_id` integer,
  PRIMARY KEY (`entity_id`, `complaint_report_reporter_entity_id`)
);

ALTER TABLE `entity_complaint_report(2)` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `entity_complaint_report(2)` ADD FOREIGN KEY (`complaint_report_reporter_entity_id`) REFERENCES `complaint_report` (`reporter_entity_id`);


CREATE TABLE `identity_complaint_report(2)` (
  `identity_id` interger,
  `complaint_report_reporter_identity_id` integer,
  PRIMARY KEY (`identity_id`, `complaint_report_reporter_identity_id`)
);

ALTER TABLE `identity_complaint_report(2)` ADD FOREIGN KEY (`identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity_complaint_report(2)` ADD FOREIGN KEY (`complaint_report_reporter_identity_id`) REFERENCES `complaint_report` (`reporter_identity_id`);


ALTER TABLE `complaint_report` ADD FOREIGN KEY (`taken_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `complaint_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `complaint_report` (`occurance_location_id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

CREATE TABLE `entity_medical_report` (
  `entity_id` integer,
  `medical_report_injured_entity_id` integer,
  PRIMARY KEY (`entity_id`, `medical_report_injured_entity_id`)
);

ALTER TABLE `entity_medical_report` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `entity_medical_report` ADD FOREIGN KEY (`medical_report_injured_entity_id`) REFERENCES `medical_report` (`injured_entity_id`);


CREATE TABLE `identity_medical_report` (
  `identity_id` interger,
  `medical_report_inujured_identity_id` integer,
  PRIMARY KEY (`identity_id`, `medical_report_inujured_identity_id`)
);

ALTER TABLE `identity_medical_report` ADD FOREIGN KEY (`identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity_medical_report` ADD FOREIGN KEY (`medical_report_inujured_identity_id`) REFERENCES `medical_report` (`inujured_identity_id`);


ALTER TABLE `medical_report` ADD FOREIGN KEY (`taken_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `medical_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `medical_report` (`occurance_location_id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

CREATE TABLE `crime_arrest_report` (
  `crime_id` integer,
  `arrest_report_crime_id` integer,
  PRIMARY KEY (`crime_id`, `arrest_report_crime_id`)
);

ALTER TABLE `crime_arrest_report` ADD FOREIGN KEY (`crime_id`) REFERENCES `crime` (`id`);

ALTER TABLE `crime_arrest_report` ADD FOREIGN KEY (`arrest_report_crime_id`) REFERENCES `arrest_report` (`crime_id`);


ALTER TABLE `arrest_report` ADD FOREIGN KEY (`complaint_id`) REFERENCES `complaint_report` (`id`);

CREATE TABLE `entity_arrest_report` (
  `entity_id` integer,
  `arrest_report_arrestee_entity_id` integer,
  PRIMARY KEY (`entity_id`, `arrest_report_arrestee_entity_id`)
);

ALTER TABLE `entity_arrest_report` ADD FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`);

ALTER TABLE `entity_arrest_report` ADD FOREIGN KEY (`arrest_report_arrestee_entity_id`) REFERENCES `arrest_report` (`arrestee_entity_id`);


CREATE TABLE `identity_arrest_report` (
  `identity_id` interger,
  `arrest_report_arrestee_identity_id` integer,
  PRIMARY KEY (`identity_id`, `arrest_report_arrestee_identity_id`)
);

ALTER TABLE `identity_arrest_report` ADD FOREIGN KEY (`identity_id`) REFERENCES `identity` (`id`);

ALTER TABLE `identity_arrest_report` ADD FOREIGN KEY (`arrest_report_arrestee_identity_id`) REFERENCES `arrest_report` (`arrestee_identity_id`);


ALTER TABLE `arrest_report` ADD FOREIGN KEY (`arresst_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `arrest_report` ADD FOREIGN KEY (`entered_by_employee_id`) REFERENCES `employee` (`id`);

ALTER TABLE `location` ADD FOREIGN KEY (`id`) REFERENCES `arrest_report` (`occurance_location_id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`id`) REFERENCES `entity` (`primary_indentity_id`);

ALTER TABLE `identity` ADD FOREIGN KEY (`primary_entity_id`) REFERENCES `entity` (`id`);

CREATE TABLE `location_identity` (
  `location_id` integer,
  `identity_last_known_residence` integer,
  PRIMARY KEY (`location_id`, `identity_last_known_residence`)
);

ALTER TABLE `location_identity` ADD FOREIGN KEY (`location_id`) REFERENCES `location` (`id`);

ALTER TABLE `location_identity` ADD FOREIGN KEY (`identity_last_known_residence`) REFERENCES `identity` (`last_known_residence`);


ALTER TABLE `entity` ADD FOREIGN KEY (`id`) REFERENCES `employee` (`entity_id`);

ALTER TABLE `address` ADD FOREIGN KEY (`id`) REFERENCES `location` (`address_id`);
