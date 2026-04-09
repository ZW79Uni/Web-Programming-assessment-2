-- Target the specific database provided by cPanel
USE `zw79_EMW_Database`;

-- Zach's Section
CREATE TABLE IF NOT EXISTS `address` (
    `addressID` BIGINT NOT NULL AUTO_INCREMENT,
    `streetName` VARCHAR(60) NOT NULL,
    `houseNumber` INT NOT NULL, 
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
    `password` VARCHAR(50) NOT NULL, -- password email and phone number constraints will be validated on input level
    `email` VARCHAR(50) NOT NULL,
    `phoneNumber` VARCHAR(11) NOT NULL,
    `isBanned` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`clientID`),
    CONSTRAINT `fk_client_address` FOREIGN KEY (`addressID`) REFERENCES `address` (`addressID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `vendor` (
    `vendorID` BIGINT NOT NULL AUTO_INCREMENT,
    `firstName` VARCHAR(50) NOT NULL,
    `lastName` VARCHAR(50) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(50) NOT NULL, -- password email and phone number constraints will be validated on input level
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
    PRIMARY KEY (`vendorServiceID`),
    CONSTRAINT `fk_vsa_vendor` FOREIGN KEY (`vendorID`) REFERENCES `vendor` (`vendorID`),
    CONSTRAINT `fk_vsa_service` FOREIGN KEY (`serviceID`) REFERENCES `service` (`serviceID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clientServiceAllocation` (
    `clientServiceID` BIGINT NOT NULL AUTO_INCREMENT,
    `clientID` BIGINT NOT NULL,
    `serviceID` BIGINT NOT NULL,
    PRIMARY KEY (`clientServiceID`),
    CONSTRAINT `fk_csa_client` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`),
    CONSTRAINT `fk_csa_service` FOREIGN KEY (`serviceID`) REFERENCES `service` (`serviceID`)
) ENGINE=InnoDB;

-- Tom's Section
CREATE TABLE IF NOT EXISTS `message` (
    `messageID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`messageID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `chat` (
    `chatID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`chatID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `admin` (
    `adminID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`adminID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `blog` (
    `blogID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`blogID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `eventType` (
    `eventTypeID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`eventTypeID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `eventTypeAllocation` (
    `eventTypeAllocationID` BIGINT NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`eventTypeAllocationID`)
) ENGINE=InnoDB;