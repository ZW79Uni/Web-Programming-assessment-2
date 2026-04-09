-- ##########################################
-- DATABASE RESET SCRIPT
-- Purpose: Wipe all tables to allow for a clean schema rerun.
-- ##########################################

-- Disable constraints to allow dropping tables in any order
SET FOREIGN_KEY_CHECKS = 0;

-- Drop Allocation/Join tables first
DROP TABLE IF EXISTS `eventTypeAllocation`;
DROP TABLE IF EXISTS `clientServiceAllocation`;
DROP TABLE IF EXISTS `vendorServiceAllocation`;

-- Drop Entity tables
DROP TABLE IF EXISTS `eventType`;
DROP TABLE IF EXISTS `blog`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `chat`;
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `service`;
DROP TABLE IF EXISTS `vendor`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `address`;

-- Re-enable constraints
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Database reset complete.' AS 'Status';