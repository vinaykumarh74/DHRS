-- Add reset token columns to users table for password reset functionality
-- Run this script to add the missing columns

USE dhrs_db;

-- Add reset token columns to users table
ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN reset_token_expiry DATETIME DEFAULT NULL;

-- Add index for better performance
CREATE INDEX idx_reset_token ON users(reset_token);

-- Verify the changes
DESCRIBE users;
