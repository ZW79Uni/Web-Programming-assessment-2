-- Target the specific database provided by cPanel
USE `zw79_EMW_Database`;

-- ##########################################
-- SECTION 1: LDM TABLE DEFINITIONS
-- ##########################################

-- Zach's Tables
CREATE TABLE IF NOT EXISTS `address` (
    `addressID` BIGINT NOT NULL AUTO_INCREMENT,
    `streetName` VARCHAR(60) NOT NULL,
    `houseNumber` VARCHAR(10) NOT NULL, -- Changed from INT for 4B/Flat 2 compatibility
    `city` VARCHAR(60) NOT NULL,
    `county` VARCHAR(60) NOT NULL,
    `postcode` VARCHAR(8) NOT NULL,
    PRIMARY KEY (`addressID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `client` (
    `clientID` BIGINT NOT NULL AUTO_INCREMENT,
    `addressID` BIGINT NOT NULL,
    `firstName` VARCHAR(50) NOT NULL,
    `lastName` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL, -- password email and phone number constraints will be validated on input level and hashed
    `email` VARCHAR(50) NOT NULL,
    `phoneNumber` VARCHAR(11) NOT NULL,
    `isBanned` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`clientID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `vendor` (
    `vendorID` BIGINT NOT NULL AUTO_INCREMENT,
    `firstName` VARCHAR(50) NOT NULL,
    `lastName` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL, -- password email and phone number constraints will be validated on input level and hashed
    `email` VARCHAR(50) NOT NULL,
    `phoneNumber` VARCHAR(11) NOT NULL,
    `vendorOrginisationRole` VARCHAR(25) NOT NULL, 
    `vendorOrginisationName` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`vendorID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `service` (
    `serviceID` BIGINT NOT NULL AUTO_INCREMENT,
    `serviceName` VARCHAR(50) NOT NULL, 
    `serviceDecription` VARCHAR(500) NOT NULL,
    `servicePrice` DECIMAL(10,2) NOT NULL, -- Checks will be done on input level
    PRIMARY KEY (`serviceID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `vendorServiceAllocation` (
    `vendorServiceID` BIGINT NOT NULL AUTO_INCREMENT,
    `vendorID` BIGINT NOT NULL,
    `serviceID` BIGINT NOT NULL,
    PRIMARY KEY (`vendorServiceID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clientServiceAllocation` (
    `clientServiceID` BIGINT NOT NULL AUTO_INCREMENT,
    `clientID` BIGINT NOT NULL,
    `serviceID` BIGINT NOT NULL,
    PRIMARY KEY (`clientServiceID`)
) ENGINE=InnoDB;

-- Tom's Tables
CREATE TABLE IF NOT EXISTS `admin` (
    `adminID` BIGINT NOT NULL AUTO_INCREMENT,
    `firstName` VARCHAR(50) NOT NULL,
    `lastName` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(320) NOT NULL UNIQUE,
    `phoneNumber` VARCHAR(12) NOT NULL,
    PRIMARY KEY (`adminID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `chat` (
    `chatID` BIGINT NOT NULL AUTO_INCREMENT,
    `clientID` BIGINT NOT NULL,
    `vendorID` BIGINT NOT NULL,
    PRIMARY KEY (`chatID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `message` (
    `messageID` BIGINT NOT NULL AUTO_INCREMENT,
    `chatID` BIGINT NOT NULL,
    `senderID` BIGINT NOT NULL,
    `senderType` ENUM('client', 'vendor') NOT NULL,
    `messageContent` TEXT NOT NULL,
    PRIMARY KEY (`messageID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `blog` (
    `blogID` BIGINT NOT NULL AUTO_INCREMENT,
    `posterID` BIGINT NOT NULL,
    `posterType` ENUM('admin') NOT NULL DEFAULT 'admin',
    `blogContent` MEDIUMBLOB NOT NULL,
    PRIMARY KEY (`blogID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `eventType` (
    `eventTypeID` BIGINT NOT NULL AUTO_INCREMENT,
    `eventTypeName` VARCHAR(25) NOT NULL,
    PRIMARY KEY (`eventTypeID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `eventTypeAllocation` (
    `eventTypeAllocationID` BIGINT NOT NULL AUTO_INCREMENT,
    `serviceID` BIGINT NOT NULL,
    `eventTypeID` BIGINT NOT NULL,
    PRIMARY KEY (`eventTypeAllocationID`)
) ENGINE=InnoDB;

-- ##########################################
-- SECTION 2: Updates for Scaling and Functionality
-- ##########################################

-- Add missing columns for Verification and Scaling
ALTER TABLE `vendor` 
ADD COLUMN `isVerified` BOOLEAN NOT NULL DEFAULT FALSE,
ADD COLUMN `automationScore` INT DEFAULT 0;

-- Add availability for matchmaking logic
ALTER TABLE `service` 
ADD COLUMN `isAvailable` BOOLEAN NOT NULL DEFAULT TRUE;

-- Create the Bookmarks/Favorites table
CREATE TABLE IF NOT EXISTS `clientFavorites` (
    `favoriteID` BIGINT NOT NULL AUTO_INCREMENT,
    `clientID` BIGINT NOT NULL,
    `vendorID` BIGINT NOT NULL,
    PRIMARY KEY (`favoriteID`),
    CONSTRAINT `fk_fav_client` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`) ON DELETE CASCADE,
    CONSTRAINT `fk_fav_vendor` FOREIGN KEY (`vendorID`) REFERENCES `vendor` (`vendorID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ##########################################
-- SECTION 3: RELATIONSHIPS (FOREIGN KEYS)
-- ##########################################

-- Zach's Relationships
ALTER TABLE `client` ADD CONSTRAINT `fk_client_address` FOREIGN KEY (`addressID`) REFERENCES `address` (`addressID`);
ALTER TABLE `vendorServiceAllocation` ADD CONSTRAINT `fk_vsa_vendor` FOREIGN KEY (`vendorID`) REFERENCES `vendor` (`vendorID`);
ALTER TABLE `vendorServiceAllocation` ADD CONSTRAINT `fk_vsa_service` FOREIGN KEY (`serviceID`) REFERENCES `service` (`serviceID`);
ALTER TABLE `clientServiceAllocation` ADD CONSTRAINT `fk_csa_client` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`);
ALTER TABLE `clientServiceAllocation` ADD CONSTRAINT `fk_csa_service` FOREIGN KEY (`serviceID`) REFERENCES `service` (`serviceID`);

-- Tom's Relationships
ALTER TABLE `chat` ADD CONSTRAINT `fk_chat_client` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`);
ALTER TABLE `chat` ADD CONSTRAINT `fk_chat_vendor` FOREIGN KEY (`vendorID`) REFERENCES `vendor` (`vendorID`);
ALTER TABLE `message` ADD CONSTRAINT `fk_msg_chat` FOREIGN KEY (`chatID`) REFERENCES `chat` (`chatID`);
ALTER TABLE `eventTypeAllocation` ADD CONSTRAINT `fk_eta_service` FOREIGN KEY (`serviceID`) REFERENCES `service` (`serviceID`);
ALTER TABLE `eventTypeAllocation` ADD CONSTRAINT `fk_eta_type` FOREIGN KEY (`eventTypeID`) REFERENCES `eventType` (`eventTypeID`);