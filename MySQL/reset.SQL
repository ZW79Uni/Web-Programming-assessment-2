-- ##########################################
-- DATABASE RESET SCRIPT
-- Purpose: Wipe all tables to allow for a clean schema rerun.
-- ##########################################

-- Ensure we are targeting the correct database
USE `h6zp02h_EMW_Database`;

-- Disable constraints to allow a "nuclear" drop
SET FOREIGN_KEY_CHECKS = 0;

-- Drop Child/Dependent tables first (reverse dependency order)
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `chat`;
DROP TABLE IF EXISTS `clientFavorites`;
DROP TABLE IF EXISTS `booking`;
DROP TABLE IF EXISTS `review`;
DROP TABLE IF EXISTS `auditLog`;

-- Drop Allocation/Join tables
DROP TABLE IF EXISTS `eventTypeAllocation`;
DROP TABLE IF EXISTS `clientServiceAllocation`;
DROP TABLE IF EXISTS `vendorServiceAllocation`;

-- Drop Base Entity tables (Parents)
DROP TABLE IF EXISTS `eventType`;
DROP TABLE IF EXISTS `blog`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `service`;
DROP TABLE IF EXISTS `vendor`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `address`;

-- Re-enable constraints
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Database reset complete.' AS 'Status';