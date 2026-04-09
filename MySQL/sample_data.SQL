-- Target the specific database provided by cPanel
USE `zw79_EMW_Database`;

-- ##########################################
-- SECTION 1: ADDRESS SEEDING
-- ##########################################
INSERT INTO `address` (`houseNumber`, `streetName`, `city`, `county`, `postcode`) VALUES
('5', 'Saffron Close', 'Maidstone', 'Kent', 'ME16 0US'), -- EMW Registered Office
('12', 'Canterbury High St', 'Canterbury', 'Kent', 'CT1 2BD'),
('32', 'Jaggard Way', 'London', 'London', 'SW12 8SG'),
('101', 'Channels Estate', 'Chelmsford', 'Essex', 'CM3 3PT'),
('46', 'Chalton Street', 'London', 'London', 'NW1 1JB');

-- ##########################################
-- SECTION 2: ADMIN SEEDING (System Sam)
-- ##########################################
INSERT INTO `admin` (`firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`) VALUES
('Sam', 'System', 'SystemSam', '$2y$10$AdminHashExample123', 'admin@eventsmeetsworld.co.uk', '07714165861'); 

-- ##########################################
-- SECTION 3: VENDOR SEEDING (Professional Pete & Ali)
-- ##########################################
INSERT INTO `vendor` (`firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`, `vendorOrginisationRole`, `vendorOrginisationName`, `isVerified`, `automationScore`) VALUES
('Ali', 'EMW', 'EMW_official', '$2y$10$VendorHash1', 'help@eventsmeetsworld.co.uk', '07714165861', 'Director', 'Events Meets World Ltd', TRUE, 100),
('Pete', 'Professional', 'ProPete', '$2y$10$VendorHash2', 'pete@majesticmarquees.co.uk', '07123456789', 'Owner', 'Majestic Marquees Kent', TRUE, 85),
('Sarah', 'Chef', 'OwenCater', '$2y$10$VendorHash3', 'info@owenbrothers.co.uk', '02086752905', 'Manager', 'Owen Brothers Catering', FALSE, 45);

-- ##########################################
-- SECTION 4: CLIENT SEEDING (Busy Becky)
-- ##########################################
INSERT INTO `client` (`addressID`, `firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`, `isBanned`) VALUES
(2, 'Becky', 'Busy', 'BusyBecky', '$2y$10$ClientHash1', 'becky@gmail.com', '07987654321', FALSE);

-- ##########################################
-- SECTION 5: SERVICE SEEDING (EMW Prices)
-- ##########################################
INSERT INTO `service` (`serviceName`, `serviceDecription`, `servicePrice`, `isAvailable`) VALUES
('Marquee (3X6M)', 'Space for 36 standing or 26 seated in rows.', 300.00, TRUE),
('Marquee (4X8M)', 'Space for 64 standing or 46 seated in rows.', 500.00, TRUE),
('Candy Floss Machine', 'Commercial grade candy floss maker for any event.', 250.00, TRUE),
('Trestle Table (6ft)', 'Strong wooden table seating 6-8 people.', 15.00, TRUE),
('Full Event Management', 'Professional planning and coordination service.', 1500.00, TRUE);

-- ##########################################
-- SECTION 6: EVENT TYPE SEEDING
-- ##########################################
INSERT INTO `eventType` (`eventTypeName`) VALUES
('Wedding'),
('Birthday Party'),
('Launch Event'),
('Corporate Function');

-- ##########################################
-- SECTION 7: ALLOCATIONS
-- ##########################################
INSERT INTO `vendorServiceAllocation` (`vendorID`, `serviceID`) VALUES
(1, 3), (1, 4), -- EMW provides Candy Floss & Tables
(2, 1), (2, 2); -- Majestic provides Marquees

INSERT INTO `eventTypeAllocation` (`serviceID`, `eventTypeID`) VALUES
(1, 1), (2, 1), -- Marquees for Weddings
(3, 2),         -- Candy Floss for Birthdays
(5, 4);         -- Management for Corporate

-- ##########################################
-- SECTION 8: ENGAGEMENT (Favorites & Chat)
-- ##########################################
INSERT INTO `clientFavorites` (`clientID`, `vendorID`) VALUES
(1, 2); -- Becky saved Majestic Marquees

INSERT INTO `chat` (`clientID`, `vendorID`) VALUES
(1, 2);

INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`) VALUES
(1, 1, 'client', 'Hi Pete, do you have the 4X8M Marquee available for June 12th?');