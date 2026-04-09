-- Target the specific database provided by cPanel
USE `zw79_EMW_Database`;

-- ##########################################
-- SECTION 1: ADDRESS SEEDING
-- ##########################################
INSERT INTO `address` (`buildingNumber`, `streetName`, `city`, `county`, `postcode`) VALUES
('5', 'Saffron Close', 'Maidstone', 'Kent', 'ME16 0US'), -- EMW HQ
('12', 'Canterbury High St', 'Canterbury', 'Kent', 'CT1 2BD'),
('101', 'London Road', 'Stratford', 'London', 'E15 1AZ'),
('42B', 'Chelmsford Way', 'Chelmsford', 'Essex', 'CM1 1GU'),
('8', 'University Way', 'Rochester', 'Kent', 'ME1 1GY');

-- ##########################################
-- SECTION 2: ADMIN SEEDING (Governance Stakeholder)
-- ##########################################
INSERT INTO `admin` (`firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`) VALUES
('Sam', 'System', 'SystemSam', '$2y$10$AdminHashExample123', 'admin@eventsmeetsworld.co.uk', '07714165861'); --

-- ##########################################
-- SECTION 3: VENDOR SEEDING (Providers)
-- ##########################################
INSERT INTO `vendor` (`vendorfirstName`, `vendorlastName`, `userName`, `password`, `vendorOrginisationRole`, `vendorOrginisationName`, `phoneNumber`, `email`) VALUES
('Ali', 'EMW', 'EMW_official', '$2y$10$VendorHash1', 'Director', 'Events Meets World Ltd', '07714165861', 'help@eventsmeetsworld.co.uk'), --
('Pete', 'Professional', 'ProPete', '$2y$10$VendorHash2', 'Owner', 'Majestic Marquees Kent', '07123456789', 'pete@majesticmarquees.co.uk'), --
('Sarah', 'Chef', 'LondonCater', '$2y$10$VendorHash3', 'Manager', 'Premier Event Catering', '02071234567', 'sarah@premiercater.com');

-- ##########################################
-- SECTION 4: CLIENT SEEDING (DIY Planners)
-- ##########################################
INSERT INTO `client` (`addressID`, `firstName`, `lastName`, `userName`, `password`, `email`, `phoneNumber`, `isBanned`) VALUES
(2, 'Becky', 'Busy', 'BusyBecky', '$2y$10$ClientHash1', 'becky@gmail.com', '07987654321', FALSE); --

-- ##########################################
-- SECTION 5: SERVICE SEEDING (Actual EMW Items)
-- ##########################################
INSERT INTO `service` (`serviceName`, `serviceDecription`, `servicePrice`) VALUES
('Marquee (3X6M)', 'Enough space for 36 standing or 26 seated in rows.', 300.00), --
('Marquee (4X8M)', 'Enough space for 64 standing or 46 seated in rows.', 500.00), --
('Candy Floss Machine', 'Commercial grade candy floss maker for any event.', 250.00), --
('Trestle Table (6ft)', 'Strong wooden table seating 6-8 people.', 15.00), --
('Event Management', 'Full professional planning and coordination service.', 1500.00); --

-- ##########################################
-- SECTION 6: EVENT TYPE SEEDING
-- ##########################################
INSERT INTO `eventType` (`eventTypeName`) VALUES
('Wedding'), --
('Birthday Party'), --
('Launch Event'), --
('Corporate Function'); --

-- ##########################################
-- SECTION 7: ALLOCATIONS (The Matchmaking Logic)
-- ##########################################

-- Assign Services to Vendors
INSERT INTO `vendorServiceAllocation` (`vendorID`, `serviceID`) VALUES
(1, 3), -- EMW provides Candy Floss
(1, 4), -- EMW provides Tables
(2, 1), -- Pro Pete provides Small Marquee
(2, 2); -- Pro Pete provides Large Marquee

-- Assign Services to Event Types (Matchmaking Tags)
INSERT INTO `eventTypeAllocation` (`serviceID`, `eventTypeID`) VALUES
(1, 1), -- Small Marquee for Weddings
(2, 1), -- Large Marquee for Weddings
(3, 2), -- Candy Floss for Birthdays
(5, 3); -- Event Management for Launch Events

-- ##########################################
-- SECTION 8: CHAT SEEDING (Vertical Slice Communication)
-- ##########################################
INSERT INTO `chat` (`clientID`, `vendorID`) VALUES
(1, 2); -- Becky chatting with Pro Pete

INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`) VALUES
(1, 1, 'client', 'Hi Pete, do you have the 4X8M Marquee available for June 12th?'); --