<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Остальной код...
?>
CREATE TABLE department_sales (
    id SERIAL PRIMARY KEY,
    department VARCHAR(50),
    sales INTEGER
);

-- Inserting data into department_sales
INSERT INTO department_sales (department, sales) VALUES
('Marketing', 15000),
('Sales', 22000),
('IT', 18000),
('Support', 9000),
('HR', 12000),
('Finance', 16000),
('Legal', 8000),

('Operations', 11000);
