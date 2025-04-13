-- Création de la base de données
CREATE DATABASE IF NOT EXISTS moviecart;
USE moviecart;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des réalisateurs
CREATE TABLE IF NOT EXISTS directors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    bio TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des films
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(6,2) NOT NULL,
    image_url VARCHAR(255),
    director_id INT,
    category VARCHAR(50) NOT NULL,
    release_year YEAR,
    duration INT,
    rating DECIMAL(2,1),
    actors TEXT,
    featured BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (director_id) REFERENCES directors(id)
);

-- Table des achats
CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

-- Insertion de quelques réalisateurs
INSERT INTO directors (name, bio, image_url) VALUES
('Christopher Nolan', 'Christopher Nolan est un réalisateur, scénariste et producteur britannico-américain né le 30 juillet 1970 à Londres.', 'https://m.media-amazon.com/images/M/MV5BNjE3NDQyOTYyMV5BMl5BanBnXkFtZTcwODE3MDQ0NA@@._V1_UY317_CR7,0,214,317_AL_.jpg'),
('Frank Darabont', 'Frank Darabont est un réalisateur, scénariste et producteur américain, né le 28 janvier 1959 à Montbéliard en France.', 'https://m.media-amazon.com/images/M/MV5BNjk0MTkxNzQwOF5BMl5BanBnXkFtZTcwODM5OTMwNA@@._V1_UY317_CR20,0,214,317_AL_.jpg'),
('Quentin Tarantino', 'Quentin Tarantino est un réalisateur, scénariste, producteur et acteur américain né le 27 mars 1963 à Knoxville, au Tennessee.', 'https://m.media-amazon.com/images/M/MV5BMTgyMjI3ODA3Nl5BMl5BanBnXkFtZTcwNzY2MDYxOQ@@._V1_UX214_CR0,0,214,317_AL_.jpg'),
('Lana et Lilly Wachowski', 'Lana et Lilly Wachowski sont deux sœurs réalisatrices, scénaristes et productrices américaines.', 'https://m.media-amazon.com/images/M/MV5BMTU1MzY2MTEyNV5BMl5BanBnXkFtZTcwNDU4MTc4Nw@@._V1_UY317_CR16,0,214,317_AL_.jpg'),
('Robert Zemeckis', 'Robert Zemeckis est un réalisateur, producteur et scénariste américain né le 14 mai 1952 à Chicago, dans l\'Illinois.', 'https://m.media-amazon.com/images/M/MV5BMTgyMTI5ODA5Nl5BMl5BanBnXkFtZTcwMDA0NzQxMw@@._V1_UY317_CR15,0,214,317_AL_.jpg'),
('Martin Scorsese', 'Martin Scorsese est un réalisateur, producteur, scénariste et acteur américain, né le 17 novembre 1942 à New York.', 'https://m.media-amazon.com/images/M/MV5BMTcyNDA4Nzk3N15BMl5BanBnXkFtZTcwNDYzMjMxMw@@._V1_UX214_CR0,0,214,317_AL_.jpg'),
('Steven Spielberg', 'Steven Spielberg est un réalisateur, producteur et scénariste américain né le 18 décembre 1946 à Cincinnati.', 'https://m.media-amazon.com/images/M/MV5BMTY1NjAzNzE1MV5BMl5BanBnXkFtZTYwNTk0ODc0._V1_UX214_CR0,0,214,317_AL_.jpg');

-- Insertion de quelques films
INSERT INTO movies (title, description, price, image_url, director_id, category, release_year, duration, rating, actors, featured) VALUES
('Inception', 'Dom Cobb est un voleur expérimenté, le meilleur dans l\'art dangereux de l\'extraction, voler les secrets les plus intimes enfouis au plus profond du subconscient durant une phase de rêve.', 19.99, 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg', 1, 'Sci-Fi', 2010, 148, 4.8, 'Leonardo DiCaprio, Joseph Gordon-Levitt, Ellen Page, Tom Hardy', 1),
('The Shawshank Redemption', 'Andy Dufresne, condamné à perpétuité pour le meurtre de sa femme et de son amant, est envoyé à la prison de Shawshank. Après des années d\'incarcération, il se forge une amitié avec Red, un détenu qui possède des contacts à l\'intérieur et à l\'extérieur.', 14.99, 'https://m.media-amazon.com/images/M/MV5BNDE3ODcxYzMtY2YzZC00NmNlLWJiNDMtZDViZWM2MzIxZDYwXkEyXkFqcGdeQXVyNjAwNDUxODI@._V1_.jpg', 2, 'Drama', 1994, 142, 4.9, 'Tim Robbins, Morgan Freeman, Bob Gunton', 1),
('The Dark Knight', 'Batman, Gordon et Harvey Dent sont déterminés à faire disparaître le crime organisé de Gotham. Mais ils se heurtent bientôt à un esprit criminel qui répand la terreur et le chaos dans la ville : le Joker.', 24.99, 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg', 1, 'Action', 2008, 152, 4.7, 'Christian Bale, Heath Ledger, Aaron Eckhart, Michael Caine', 1),
('Pulp Fiction', 'L\'odyssée sanglante et burlesque de petits malfrats dans la jungle de Hollywood, à travers trois histoires qui s\'entremêlent.', 17.99, 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg', 3, 'Crime', 1994, 154, 4.6, 'John Travolta, Samuel L. Jackson, Uma Thurman, Bruce Willis', 1),
('The Matrix', 'Un pirate informatique apprend de mystérieux rebelles la véritable nature de sa réalité et son rôle dans la guerre contre ses contrôleurs.', 16.99, 'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Zi00ZTVkLWI0MTEtMDllZjNkYzNjNTc4L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg', 4, 'Sci-Fi', 1999, 136, 4.7, 'Keanu Reeves, Laurence Fishburne, Carrie-Anne Moss', 1),
('Forrest Gump', 'L\'histoire de Forrest Gump, un homme simple d\'esprit, qui se retrouve impliqué dans presque tous les événements majeurs de l\'histoire des États-Unis.', 15.99, 'https://m.media-amazon.com/images/M/MV5BNWIwODRlZTUtY2U3ZS00Yzg1LWJhNzYtMmZiYmEyNmU1NjMzXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg', 5, 'Drama', 1994, 142, 4.8, 'Tom Hanks, Robin Wright, Gary Sinise', 1),
('The Departed', 'Un agent infiltré et une taupe dans la police essaient de s\'identifier l\'un l\'autre tout en infiltrant un gang irlandais à Boston.', 18.99, 'https://m.media-amazon.com/images/M/MV5BMTI1MTY2OTIxNV5BMl5BanBnXkFtZTYwNjQ4NjY3._V1_.jpg', 6, 'Crime', 2006, 151, 4.6, 'Leonardo DiCaprio, Matt Damon, Jack Nicholson', 0),
('Goodfellas', 'Henry Hill et ses amis travaillent dans les coulisses de la mafia organisée au cours des cinq décennies entre les années 60 et 90.', 19.99, 'https://m.media-amazon.com/images/M/MV5BY2NkZjEzMDgtN2RjYy00YzM1LWI4ZmQtMjIwYjFjNmI3ZGEwXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg', 6, 'Crime', 1990, 146, 4.7, 'Robert De Niro, Ray Liotta, Joe Pesci', 0),
('Jurassic Park', 'Durant une visite d\'un parc à thème peuplé de dinosaures créés à partir d\'ADN préhistorique, les dinosaures terrifiants s\'échappent et commencent à terroriser les visiteurs.', 17.99, 'https://m.media-amazon.com/images/M/MV5BMjM2MDgxMDg0Nl5BMl5BanBnXkFtZTgwNTM2OTM5NDE@._V1_.jpg', 7, 'Action', 1993, 127, 4.5, 'Sam Neill, Laura Dern, Jeff Goldblum', 0),
('Saving Private Ryan', 'Suite au débarquement en Normandie, un groupe de soldats américains doit partir en mission pour retrouver et ramener sain et sauf le simple soldat James Ryan.', 23.99, 'https://m.media-amazon.com/images/M/MV5BZjhkMDM4MWItZTVjOC00ZDRhLThmYTAtM2I5NzBmNmNlMzI1XkEyXkFqcGdeQXVyNDYyMDk5MTU@._V1_.jpg', 7, 'War', 1998, 169, 4.8, 'Tom Hanks, Matt Damon, Tom Sizemore', 0),
('Interstellar', 'Une équipe d\'explorateurs voyage à travers un trou de ver dans l\'espace dans le but de trouver une nouvelle planète habitable et sauver l\'humanité d\'une Terre mourante.', 21.99, 'https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg', 1, 'Sci-Fi', 2014, 169, 4.7, 'Matthew McConaughey, Anne Hathaway, Jessica Chastain', 0),
('The Green Mile', 'La vie de gardiens de prison affectés par l\'arrivée d\'un nouveau détenu condamné à mort pour le meurtre de deux fillettes, mais qui possède un don mystérieux.', 16.99, 'https://m.media-amazon.com/images/M/MV5BMTUxMzQyNjA5MF5BMl5BanBnXkFtZTYwOTU2NTY3._V1_.jpg', 2, 'Drama', 1999, 189, 4.6, 'Tom Hanks, Michael Clarke Duncan, David Morse', 0);

-- Insertion d'un administrateur par défaut (mot de passe: admin123)
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@moviecart.com', '$2y$10$7rLSvRVyTQORapkDOqmkhetjF6H9lJHngr4hJMSM2lHXyNzqdwjgC');

-- Quelques achats pour test
INSERT INTO purchases (user_id, movie_id, price) VALUES
(1, 1, 19.99),
(1, 3, 24.99),
(1, 5, 16.99); 