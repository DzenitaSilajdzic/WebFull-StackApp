INSERT INTO users (username, password, email, name, role, status)
VALUES (
    'adminuser', 
    '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', -- Replace with an actual BCRYPT hash!
    'admin@test.com', 
    'Test Admin', 
    'admin', 
    'active'
);