USE `h6zp02h_EMW_Database`;

-- ==========================================
-- STORY 1: Matchmaking Engine (Busy Becky)
-- Verified Kent vendors providing Wedding services
-- ==========================================
SELECT
    v.vendorOrginisationName AS `Vendor`,
    s.serviceName AS `Service`,
    s.servicePrice AS `Price`,
    a.city AS `Location`,
    v.isVerified AS `Vetted?`,
    v.automationScore AS `Trust Score`
FROM `vendor` v
JOIN `address` a ON v.addressID = a.addressID
JOIN `vendorServiceAllocation` vsa ON v.vendorID = vsa.vendorID
JOIN `service` s ON vsa.serviceID = s.serviceID
JOIN `eventTypeAllocation` eta ON s.serviceID = eta.serviceID
JOIN `eventType` et ON eta.eventTypeID = et.eventTypeID
WHERE a.county = 'Kent'
  AND et.eventTypeName = 'Wedding'
  AND v.isVerified = TRUE
ORDER BY v.automationScore DESC, s.servicePrice ASC;

-- ==========================================
-- STORY 2: Vendor Showcase / Vendor Portal (Proud Pete)
-- Vendors uploading high-res video blog showcases to prove quality
-- ==========================================
-- (Simulated query for showcasing vendor's approved high-res 'blog/portfolio' content)
SELECT
    b.blogID AS `Post ID`,
    v.vendorOrginisationName AS `Vendor`,
    v.vendorOrginisationRole AS `Role`,
    LENGTH(b.blogContent) AS `Blog Payload Size (Bytes)`
FROM `blog` b
JOIN `vendor` v ON b.posterID = v.vendorID
WHERE b.posterType = 'vendor';

-- ==========================================
-- STORY 3: Trust and Reputation System
-- ==========================================
SELECT
    v.vendorOrginisationName AS `Vendor`,
    ROUND(AVG(r.rating), 1) AS `Average Stars`,
    COUNT(r.reviewID) AS `Total Reviews`,
    GROUP_CONCAT(r.comment ORDER BY r.createdAt DESC SEPARATOR ' | ') AS `Latest Feedback`
FROM `vendor` v
LEFT JOIN `review` r ON v.vendorID = r.vendorID
GROUP BY v.vendorID, v.vendorOrginisationName
ORDER BY `Average Stars` DESC;

-- ==========================================
-- STORY 4A: Secure Chat Membership (manual fail demo)
-- Run this statement manually in meeting to show hard DB rejection:
-- INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`)
-- VALUES (1, 99, 'client', 'I am trying to hack this chat!');
--
-- Expected error:
-- ERROR 1644 (45000): Client is not a participant in this chat.
-- ==========================================

-- ==========================================
-- STORY 4B: Secure Chat Membership (automated check)
-- This lets the script continue and reports expected rejection as PASSED.
-- ==========================================
DROP PROCEDURE IF EXISTS `sp_story_04_check`;
DELIMITER //
CREATE PROCEDURE `sp_story_04_check`()
BEGIN
    DECLARE rejected BOOLEAN DEFAULT FALSE;

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET rejected = TRUE;
        ROLLBACK;
    END;

    START TRANSACTION;
    INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`)
    VALUES (1, 99, 'client', 'I am trying to hack this chat!');
    ROLLBACK;

    SELECT
        'Secure Chat Membership' AS `Story`,
        IF(rejected, 'PASSED', 'FAILED') AS `Result`,
        IF(rejected,
           'Invalid sender blocked by trigger.',
           'Invalid sender unexpectedly accepted.') AS `Details`;
END //
DELIMITER ;

CALL `sp_story_04_check`();
DROP PROCEDURE IF EXISTS `sp_story_04_check`;

-- ==========================================
-- STORY 5: Governance Audit Feed (System Sam)
-- ==========================================
SELECT
    al.actionDate AS `Time`,
    a.username AS `Admin`,
    al.actionType AS `Action`,
    al.targetType AS `Target`,
    al.reason AS `Justification`
FROM `auditLog` al
JOIN `admin` a ON al.adminID = a.adminID
ORDER BY al.actionDate DESC;

-- ==========================================
-- STORY 6: Chat Function (Becky)
-- Client chatting with a vendor to obtain clarity on booking
-- ==========================================
SELECT
    c.chatID AS `Chat Thread`,
    m.senderType AS `Sender Role`,
    IF(m.senderType = 'client', cl.firstName, 
       IF(m.senderType = 'vendor', v.vendorOrginisationName, 'Admin')) AS `Name`,
    m.messageContent AS `Message Payload`
FROM `message` m
JOIN `chat` c ON m.chatID = c.chatID
JOIN `client` cl ON c.clientID = cl.clientID
JOIN `vendor` v ON c.vendorID = v.vendorID
WHERE c.clientID = 1 AND c.vendorID = 2
ORDER BY m.messageID ASC;

-- ==========================================
-- STORY 7: Service Catalogue (Proud Pete)
-- Vendor showcasing the specific services they offer
-- ==========================================
SELECT
    v.vendorOrginisationName AS `Vendor`,
    s.serviceName AS `Service Offered`,
    s.serviceDecription AS `Description`,
    s.servicePrice AS `Price (£)`,
    IF(s.isAvailable, 'Yes', 'No') AS `Currently Available?`
FROM `service` s
JOIN `vendorServiceAllocation` vsa ON s.serviceID = vsa.serviceID
JOIN `vendor` v ON vsa.vendorID = v.vendorID
WHERE v.vendorOrginisationName = 'Majestic Marquees Kent'
ORDER BY s.servicePrice ASC;
