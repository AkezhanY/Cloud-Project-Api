CREATE TABLE hourly_activity (
    id SERIAL PRIMARY KEY,
    hour INTEGER,
    users INTEGER
);

-- Inserting data into hourly_activity
INSERT INTO hourly_activity (hour, users) VALUES
(9, 120), (10, 250), (11, 310), (12, 280),
(13, 200), (14, 270), (15, 320), (16, 300),
(17, 350), (18, 400), (19, 450), (20, 500);