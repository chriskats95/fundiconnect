-- Create Admin Account for FundiConnect
-- This script creates an admin user account

USE fundiconnect;

-- Update existing admin account (or create if doesn't exist)
-- Email: admin@fundi-connect.ug
-- Password: Admin@123
-- Replace the password hash with the new one
UPDATE users 
SET password = '$2y$10$mR2FWGDwmi8LQ4abOCXrb.t/p0edtTRlPr.9xiDN8VSEbQKFT3osO',
    full_name = 'System Administrator',
    phone = '+254700000000',
    role = 'admin',
    status = 'active'
WHERE email = 'admin@fundi-connect.ug';

-- If no rows were updated, insert a new admin account
INSERT IGNORE INTO users (email, password, full_name, phone, role, status, created_at) 
VALUES (
    'admin@fundi-connect.ug', 
    '$2y$10$mR2FWGDwmi8LQ4abOCXrb.t/p0edtTRlPr.9xiDN8VSEbQKFT3osO', 
    'System Administrator', 
    '+254700000000', 
    'admin', 
    'active',
    NOW()
);

-- Verify the admin account was created
SELECT id, email, full_name, role, status, created_at 
FROM users 
WHERE role = 'admin';
