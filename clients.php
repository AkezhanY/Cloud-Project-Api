<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Остальной код...
?>
CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    type VARCHAR(50),
    count INTEGER
);

-- Inserting data into clients
INSERT INTO clients (type, count) VALUES
('Regular', 150),
('VIP', 45),
('New', 80),
('Companies', 60),
('Partners', 30),
('Resellers', 40),
('Distributors', 25),

('Government', 35);
