INSERT INTO users (username, password, email, name, role, status)
VALUES (
    'adminuser', 
    '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    'admin@test.com', 
    'Test Admin', 
    'admin', 
    'active'
);

INSERT INTO users (username, password, email, role) VALUES 
('test_user', 'password123', 'test@example.com', 'visitor');

INSERT INTO studios (name, founding_date, status) VALUES 
('Studio Ghibli', '1985-06-15', 'working'),
('MAPPA', '2011-06-14', 'working'),
('Ufotable', '2000-10-01', 'working'),
('Wit Studio', '2012-06-01', 'working');

INSERT INTO categories (name, status) VALUES 
('Action', 'active'), ('Adventure', 'active'), ('Fantasy', 'active'), 
('Drama', 'active'), ('Romance', 'active'), ('Sci-Fi', 'active'), 
('Horror', 'active'), ('Comedy', 'active');

ALTER TABLE anime 
MODIFY COLUMN status ENUM('active', 'airing', 'finished', 'coming', 'hidden', 'deleted') DEFAULT 'active';


INSERT INTO anime (title, type, details, release_date, popularity, rating, image_url, status)
VALUES 
('Vinland Saga', 'TV Series', 'A young man seeks revenge in the Viking age.', '2019-07-07', 8100, 8.73, 'https://cdn.myanimelist.net/images/anime/1500/103005.jpg', 'active'),
('Death Note', 'TV Series', 'A student finds a notebook that can kill anyone whose name is written in it.', '2006-10-04', 9500, 8.62, 'https://cdn.myanimelist.net/images/anime/9/9453.jpg', 'active'),
('Fullmetal Alchemist: B', 'TV Series', 'Two brothers search for the Philosophers Stone.', '2009-04-05', 9850, 9.10, 'https://cdn.myanimelist.net/images/anime/1223/96544.jpg', 'active'),
('Cowboy Bebop', 'TV Series', 'Bounty hunters travel the galaxy catching outlaws.', '1998-04-03', 7500, 8.75, 'https://cdn.myanimelist.net/images/anime/4/19644.jpg', 'active'),
('Your Name', 'Movie', 'Two teenagers share a magical connection across distance.', '2016-08-26', 8900, 8.85, 'https://cdn.myanimelist.net/images/anime/5/87048.jpg', 'active');-- Changed 'active' to 'airing'


INSERT INTO episodes (anime_id, episode_number, title, video_url, status)
VALUES 
(1, 1, 'Im Luffy!', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(1, 2, 'The Great Swordsman', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(2, 1, 'Cruelty', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(2, 2, 'Trainer', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(3, 1, 'To You', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(3, 2, 'That Day', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(4, 1, 'Sukuna', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(4, 2, 'For Myself', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(6, 1, 'Somewhere Not Here', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(6, 2, 'Sword', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(7, 1, 'Rebirth', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(7, 2, 'Confrontation', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(8, 1, 'Alchemist', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(9, 1, 'Asteroid Blues', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(10, 1, 'Body Swap', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active');

ALTER TABLE episodes 
ADD COLUMN episode_number INT UNSIGNED NOT NULL AFTER anime_id,
ADD COLUMN title VARCHAR(350) AFTER episode_number,
ADD COLUMN video_url VARCHAR(1000) AFTER title;

ALTER TABLE episodes 
MODIFY COLUMN status ENUM('aired', 'coming', 'hidden', 'deleted', 'active') DEFAULT 'coming';


INSERT INTO episodes (anime_id, episode_number, title, video_url, status) VALUES 
(1, 1, 'Im Luffy!', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(1, 2, 'The Great Swordsman', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(2, 1, 'Somewhere Not Here', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(2, 2, 'Sword', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(3, 1, 'Rebirth', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(3, 2, 'Confrontation', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(4, 1, 'Alchemist', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(5, 1, 'Asteroid Blues', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active'),
(6, 1, 'Body Swap', 'https://www.w3schools.com/html/mov_bbb.mp4', 'active');

ALTER TABLE comments 
MODIFY COLUMN reply_id INT UNSIGNED DEFAULT NULL,
MODIFY COLUMN status ENUM('active', 'hidden', 'deleted') DEFAULT 'active';


INSERT INTO comments (user_id, anime_id, reply_id, text, status) VALUES 
(5, 1, NULL, 'One Piece is getting so good lately!', 'active'),
(6, 2, NULL, 'Vinland Saga has the best character development.', 'active'),
(7, 3, NULL, 'I still think Death Note is a masterpiece.', 'active'),
(5, 6, NULL, 'Your Name made me cry, 10/10.', 'active'),
(6, 4, NULL, 'Fullmetal Alchemist is the perfect starter anime.', 'active');


INSERT INTO comments (user_id, anime_id, reply_id, text, status) VALUES 
(7, 1, 
    (SELECT id FROM (SELECT id FROM comments WHERE anime_id = 1 LIMIT 1) as tmp), 
    'I totally agree, the Wano arc was insane!', 
    'active'
);

INSERT INTO anime_studios (anime_id, studio_id) VALUES 
(1, 4), (2, 3), (3, 4), (4, 2), (5, 1), (6, 4);

INSERT INTO anime_categories (anime_id, category_id)
VALUES 
(1, 1), 
(1, 2),
(2, 1), 
(2, 4),
(3, 4), 
(3, 7),
(4, 1), 
(4, 2), 
(4, 4),
(5, 1), 
(5, 6),
(6, 4), 
(6, 5);

INSERT INTO users (username, password, email, name, role, status)
VALUES (
    'admin', 
    '$2y$10$aiG.p/././././././././././././././././././././././.', 
    'admin@admin', 
    'admin', 
    'admin', 
    'active'
);