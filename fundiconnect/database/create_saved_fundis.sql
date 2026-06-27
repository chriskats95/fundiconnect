-- Create saved_fundis table
-- This allows clients to save favorite fundis

DROP TABLE IF EXISTS saved_fundis;

CREATE TABLE saved_fundis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    fundi_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (client_id, fundi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_saved_client ON saved_fundis(client_id);
CREATE INDEX idx_saved_fundi ON saved_fundis(fundi_id);

-- Check if successful
SHOW TABLES LIKE 'saved_fundis';