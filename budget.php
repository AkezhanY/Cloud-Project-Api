<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Остальной код...
?>
CREATE TABLE budget (
    id SERIAL PRIMARY KEY,
    category VARCHAR(50),
    percent INTEGER
);

-- Inserting data into budget
INSERT INTO budget (category, percent) VALUES
('Salaries', 40),
('Advertising', 20),
('Development', 25),
('Office', 15),
('Training', 5),
('R&D', 10),
('IT infrastructure', 8),

('Maintenance', 12);
