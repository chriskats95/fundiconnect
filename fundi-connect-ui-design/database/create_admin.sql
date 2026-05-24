-- Create Admin Account for FundiConnect
-- This script creates an admin user account

USE fundiconnect;

-- Insert Admin User
-- Email: admin@fundiconnect.com
-- Password: Admin@123
-- The password hash below is generated using PHP's password_hash() with PASSWORD_BCRYPT
INSERT INTO users (email, password, full_name, phone, role, status, created_at) 
VALUES (
    'admin@fundiconnect.com', 
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIxIvJ5wHi', 
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
