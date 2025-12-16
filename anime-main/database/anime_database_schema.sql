CREATE DATABASE anime_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

use anime_db;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(250) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL,
    email VARCHAR(500) NOT NULL UNIQUE,
    name VARCHAR(350),
    profile_img VARCHAR(1000),
    role ENUM('admin', 'visitor') DEFAULT 'visitor',
    status ENUM('active', 'deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(350) NOT NULL,
    status ENUM('active', 'hidden', 'deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE studios (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(350) NOT NULL,
    founding_date DATE,
    logo VARCHAR(1000),
    status ENUM('working', 'unknown', 'closed') DEFAULT 'working'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE anime (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(350) NOT NULL,
    type VARCHAR(50),
    details MEDIUMTEXT,
    release_date DATE,
    popularity INT DEFAULT 0,
    status ENUM('airing', 'finished', 'deleted', 'coming', 'hidden') DEFAULT 'airing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE episodes (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anime_id INT UNSIGNED NOT NULL,
    season INT,
    date_posted DATETIME,
    duration TIME,
    views INT DEFAULT 0,
    status ENUM('aired', 'coming', 'hidden', 'deleted') DEFAULT 'coming',
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE comments (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    anime_id INT UNSIGNED NOT NULL,
    reply_id INT UNSIGNED NOT NULL,
    text TEXT NOT NULL,
    status ENUM('active', 'hidden', 'deleted') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reply_id) REFERENCES comments(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE anime_categories (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anime_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE anime_studios (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anime_id INT UNSIGNED NOT NULL,
    studio_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (studio_id) REFERENCES studios(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

