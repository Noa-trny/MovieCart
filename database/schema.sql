-- Create database if not exists
CREATE DATABASE IF NOT EXISTS moviecart;
USE moviecart;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Directors table
CREATE TABLE IF NOT EXISTS directors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Actors table
CREATE TABLE IF NOT EXISTS actors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Movies table
CREATE TABLE IF NOT EXISTS movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    release_year INT,
    duration INT,
    price DECIMAL(10,2) NOT NULL,
    poster_path VARCHAR(255),
    director_id INT,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Movie Actors (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS movie_actors (
    movie_id INT,
    actor_id INT,
    role VARCHAR(100),
    PRIMARY KEY (movie_id, actor_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    movie_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, slug, description) VALUES
('Action', 'action', 'Action-packed movies with thrilling sequences'),
('Drama', 'drama', 'Emotional and character-driven stories'),
('Comedy', 'comedy', 'Funny and entertaining movies'),
('Sci-Fi', 'sci-fi', 'Science fiction and futuristic stories'),
('Horror', 'horror', 'Scary and suspenseful movies');

-- Insert sample directors
INSERT INTO directors (name, biography) VALUES
('Christopher Nolan', 'British-American filmmaker known for complex narratives and practical effects'),
('Martin Scorsese', 'American filmmaker known for his work in crime and drama genres'),
('Steven Spielberg', 'American filmmaker known for his work in various genres including adventure and sci-fi');

-- Insert sample actors
INSERT INTO actors (name, biography) VALUES
('Leonardo DiCaprio', 'American actor and producer known for his work in various genres'),
('Tom Cruise', 'American actor and producer known for his work in action films'),
('Meryl Streep', 'American actress known for her versatility and numerous awards');

-- Insert sample movies
INSERT INTO movies (title, description, release_year, duration, price, director_id, category_id) VALUES
('Inception', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 2010, 148, 19.99, 1, 1),
('The Departed', 'An undercover cop and a mole in the police attempt to identify each other while infiltrating an Irish gang in South Boston.', 2006, 151, 14.99, 2, 2),
('E.T. the Extra-Terrestrial', 'A troubled child summons the courage to help a friendly alien escape Earth and return to his home world.', 1982, 115, 12.99, 3, 4);

-- Insert sample movie actors
INSERT INTO movie_actors (movie_id, actor_id, role) VALUES
(1, 1, 'Cobb'),
(2, 1, 'Billy Costigan'),
(3, 2, 'Elliott');

-- Insert sample user (password: password123)
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 