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