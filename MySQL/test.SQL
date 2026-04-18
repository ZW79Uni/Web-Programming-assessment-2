USE `h6zp02h_EMW_Database`;

-- Trackable integration checks for UI readiness
CREATE TEMPORARY TABLE IF NOT EXISTS `test_results` (
    `test_id` VARCHAR(20) NOT NULL,
    `objective` VARCHAR(150) NOT NULL,
    `query_method` VARCHAR(150) NOT NULL,
    `result` VARCHAR(10) NOT NULL,
    `details` VARCHAR(255) NOT NULL
);

-- DB-INT-01: Verify Booking Ownership
SET @booking_total := (SELECT COUNT(*) FROM `booking`);
SET @booking_joined := (
    SELECT COUNT(*)
    FROM `booking` b
    INNER JOIN `client` c ON c.`clientID` = b.`clientID`
    INNER JOIN `vendor` v ON v.`vendorID` = b.`vendorID`
);

INSERT INTO `test_results` (`test_id`, `objective`, `query_method`, `result`, `details`)
VALUES (
    'DB-INT-01',
    'Verify Booking Ownership',
    'Join booking -> client -> vendor',
    IF(@booking_total = @booking_joined, 'PASSED', 'FAILED'),
    CONCAT('booking_rows=', @booking_total, ', joined_rows=', @booking_joined)
);

-- DB-INT-02: Rating Aggregation
SET @rating_groups := (
    SELECT COUNT(*)
    FROM (
        SELECT r.`vendorID`, AVG(r.`rating`) AS avg_rating
        FROM `review` r
        GROUP BY r.`vendorID`
    ) t
);
SET @expected_rating_rows := (
    SELECT COUNT(*)
    FROM `vendor` v
    INNER JOIN (
        SELECT r.`vendorID`, ROUND(AVG(r.`rating`), 2) AS avg_rating
        FROM `review` r
        GROUP BY r.`vendorID`
    ) x ON x.`vendorID` = v.`vendorID`
    WHERE (v.`vendorID` = 1 AND x.`avg_rating` = 4.00)
       OR (v.`vendorID` = 2 AND x.`avg_rating` = 5.00)
);

INSERT INTO `test_results` (`test_id`, `objective`, `query_method`, `result`, `details`)
VALUES (
    'DB-INT-02',
    'Rating Aggregation',
    'AVG(rating) GROUP BY vendorID',
    IF(@rating_groups > 0 AND @expected_rating_rows = 2, 'PASSED', 'FAILED'),
    CONCAT('rating_groups=', @rating_groups, ', expected_rows_matched=', @expected_rating_rows)
);

-- DB-INT-03: Trigger Security (invalid sender membership)
DROP PROCEDURE IF EXISTS `sp_test_db_int_03`;
DELIMITER $$
CREATE PROCEDURE `sp_test_db_int_03`()
BEGIN
    DECLARE did_fail BOOLEAN DEFAULT FALSE;

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET did_fail = TRUE;
        ROLLBACK;
    END;

    START TRANSACTION;
    INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`)
    VALUES (1, 3, 'vendor', 'DB-INT-03 invalid membership test');
    ROLLBACK;

    INSERT INTO `test_results` (`test_id`, `objective`, `query_method`, `result`, `details`)
    VALUES (
        'DB-INT-03',
        'Trigger Security',
        'Attempt INSERT with invalid senderID',
        IF(did_fail, 'PASSED', 'FAILED'),
        IF(did_fail,
            'Trigger rejected invalid chat sender membership.',
            'Invalid membership insert was accepted unexpectedly.'
        )
    );
END$$
DELIMITER ;

CALL `sp_test_db_int_03`();
DROP PROCEDURE IF EXISTS `sp_test_db_int_03`;

SELECT `test_id` AS `Test ID`,
       `objective` AS `Objective`,
       `query_method` AS `Query Method`,
       `result` AS `Result`,
       `details` AS `Details`
FROM `test_results`
ORDER BY `test_id`;

DROP TEMPORARY TABLE IF EXISTS `test_results`;
