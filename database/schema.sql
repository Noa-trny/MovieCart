CREATE DATABASE IF NOT EXISTS moviecart;
USE moviecart;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS directors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS actors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

CREATE TABLE IF NOT EXISTS movie_actors (
    movie_id INT,
    actor_id INT,
    role VARCHAR(100),
    PRIMARY KEY (movie_id, actor_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    movie_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Insertion des catégories
INSERT INTO categories (name, slug, description) VALUES
('Action', 'action', 'Action-packed movies with thrilling sequences'),
('Drama', 'drama', 'Emotional and character-driven stories'),
('Comedy', 'comedy', 'Funny and entertaining movies'),
('Sci-Fi', 'sci-fi', 'Science fiction and futuristic stories'),
('Horror', 'horror', 'Scary and suspenseful movies'),
('Adventure', 'adventure', 'Exciting journey and exploration stories');

-- Insertion des réalisateurs
INSERT INTO directors (name, biography) VALUES
('Quentin Tarantino', 'American filmmaker known for his nonlinear storylines and stylized violence'),
('James Cameron', 'Canadian filmmaker known for his work in science fiction and action films'),
('Ridley Scott', 'British filmmaker known for his work in science fiction and historical epics'),
('David Fincher', 'American filmmaker known for his dark and stylish thrillers'),
('Wes Anderson', 'American filmmaker known for his distinctive visual and narrative style'),
('Christopher Nolan', 'British-American filmmaker known for complex narratives and practical effects'),
('Martin Scorsese', 'American filmmaker known for his work in crime and drama genres'),
('Steven Spielberg', 'American filmmaker known for his work in various genres including adventure and sci-fi'),
('Denis Villeneuve', 'Canadian filmmaker known for his ambitious and visually striking sci-fi films'),
('Jordan Peele', 'American filmmaker known for his innovative horror films with social commentary'),
('Guillermo del Toro', 'Mexican filmmaker known for his fantasy and horror films'),
('Greta Gerwig', 'American filmmaker and actress known for her coming-of-age films'),
('Bong Joon-ho', 'South Korean filmmaker known for his genre-blending films with social themes'),
('Taika Waititi', 'New Zealand filmmaker known for his quirky comedy and adventure films'),
('Edgar Wright', 'British filmmaker known for his fast-paced editing style and comedy films'),
('Jon Favreau', 'American filmmaker known for his work in the Marvel universe and adventure films'),
('Peter Jackson', 'New Zealand filmmaker known for The Lord of the Rings trilogy');

-- Insertion des acteurs
INSERT INTO actors (name, biography) VALUES
('Brad Pitt', 'American actor and producer known for his versatile roles'),
('Scarlett Johansson', 'American actress known for her work in various genres'),
('Robert Downey Jr.', 'American actor known for his role as Iron Man'),
('Emma Stone', 'American actress known for her work in comedy and drama'),
('Denzel Washington', 'American actor known for his powerful performances'),
('Leonardo DiCaprio', 'American actor and producer known for his work in various genres'),
('Tom Cruise', 'American actor and producer known for his work in action films'),
('Meryl Streep', 'American actress known for her versatility and numerous awards'),
('John Travolta', 'American actor known for his roles in Pulp Fiction and Grease'),
('Kate Winslet', 'British actress known for her roles in Titanic and Eternal Sunshine'),
('Russell Crowe', 'New Zealand actor known for his role in Gladiator'),
('Ralph Fiennes', 'British actor known for his diverse roles including in Grand Budapest Hotel'),
('Aaron Eckhart', 'American actor known for his role as Harvey Dent in The Dark Knight'),
('Ray Liotta', 'American actor known for his role in Goodfellas'),
('Sam Neill', 'New Zealand actor known for his role in Jurassic Park'),
('Sam Worthington', 'Australian actor known for his role in Avatar'),
('Matt Damon', 'American actor known for his roles in various films including The Martian'),
('Ben Affleck', 'American actor and director known for various roles including in Gone Girl'),
('Bill Murray', 'American actor known for his comedy roles and work with Wes Anderson'),
('Tom Hanks', 'American actor known for his diverse roles including in Saving Private Ryan'),
('Timothée Chalamet', 'American actor known for his roles in Dune and Call Me by Your Name'),
('Zendaya', 'American actress known for her roles in Euphoria and Dune'),
('Daniel Kaluuya', 'British actor known for his roles in Get Out and Black Panther'),
('Lupita Nyong''o', 'Kenyan-Mexican actress known for her roles in 12 Years a Slave and Us'),
('Sally Hawkins', 'British actress known for her role in The Shape of Water'),
('Doug Jones', 'American actor known for his creature performances in Guillermo del Toro films'),
('Saoirse Ronan', 'Irish-American actress known for her roles in Lady Bird and Little Women'),
('Florence Pugh', 'British actress known for her roles in Midsommar and Little Women'),
('Song Kang-ho', 'South Korean actor known for his role in Parasite'),
('Choi Woo-shik', 'South Korean actor known for his role in Parasite'),
('Chris Hemsworth', 'Australian actor known for his role as Thor'),
('Karen Gillan', 'Scottish actress known for her roles in Guardians of the Galaxy and Jumanji'),
('Dwayne Johnson', 'American actor and former wrestler known for his action and comedy roles'),
('Jack Black', 'American actor and comedian known for his energetic performances'),
('Elijah Wood', 'American actor known for his role as Frodo in The Lord of the Rings'),
('Viggo Mortensen', 'Danish-American actor known for his role as Aragorn in The Lord of the Rings'),
('Simon Pegg', 'British actor known for his comedy work with Edgar Wright'),
('Nick Frost', 'British actor known for his collaborations with Simon Pegg');

-- Insertion d'un utilisateur
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertion des films avec leurs posters
INSERT INTO movies (title, description, release_year, duration, price, poster_path, director_id, category_id) VALUES
('Pulp Fiction', 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.', 1994, 154, 14.99, 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg', 1, 1),
('Titanic', 'A seventeen-year-old aristocrat falls in love with a kind but poor artist aboard the luxurious, ill-fated R.M.S. Titanic.', 1997, 195, 12.99, 'https://m.media-amazon.com/images/M/MV5BYzYyN2FiZmUtYWYzMy00MzViLWJkZTMtOGY1ZjgzNWMwN2YxXkEyXkFqcGc@._V1_SX300.jpg', 2, 2),
('Gladiator', 'A former Roman General sets out to exact vengeance against the corrupt emperor who murdered his family and sent him into slavery.', 2000, 155, 15.99, 'https://m.media-amazon.com/images/M/MV5BMDliMmNhNDEtODUyOS00MjNlLTgxODEtN2U3NzIxMGVkZTA1L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg', 3, 1),
('Fight Club', 'An insomniac office worker and a devil-may-care soapmaker form an underground fight club that evolves into something much, much more.', 1999, 139, 13.99, 'https://m.media-amazon.com/images/M/MV5BMmEzNTkxYjQtZTc0MC00YTVjLTg5ZTEtZWMwOWVlYzY0NWIwXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg', 4, 1),
('The Grand Budapest Hotel', 'A writer encounters the owner of an aging high-class hotel, who tells him of his early years serving as a lobby boy in the hotel''s glorious years under an exceptional concierge.', 2014, 99, 11.99, 'https://m.media-amazon.com/images/M/MV5BMzM5NjUxOTEyMl5BMl5BanBnXkFtZTgwNjEyMDM0MDE@._V1_SX300.jpg', 5, 3),
('The Dark Knight', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 2008, 152, 16.99, 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_SX300.jpg', 6, 1),
('Goodfellas', 'The story of Henry Hill and his life in the mob, covering his relationship with his wife Karen Hill and his mob partners Jimmy Conway and Tommy DeVito in the Italian-American crime syndicate.', 1990, 146, 12.99, 'https://m.media-amazon.com/images/M/MV5BY2NkZjEzMDgtN2RjYy00YzM1LWI4ZmQtMjIwYjFjNmI3ZGEwXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg', 7, 1),
('Jurassic Park', 'A pragmatic paleontologist touring an almost complete theme park on an island in Central America is tasked with protecting a couple of kids after a power failure causes the park''s cloned dinosaurs to run loose.', 1993, 127, 14.99, 'https://m.media-amazon.com/images/M/MV5BMjM2MDgxMDg0Nl5BMl5BanBnXkFtZTgwNTM2OTM5NDE@._V1_SX300.jpg', 8, 4),
('Inglourious Basterds', 'In Nazi-occupied France during World War II, a plan to assassinate Nazi leaders by a group of Jewish U.S. soldiers coincides with a theatre owner''s vengeful plans for the same.', 2009, 153, 13.99, 'https://m.media-amazon.com/images/M/MV5BOTJiNDEzOWYtMTVjOC00ZjlmLWE0NGMtZmE1OWVmZDQ2OWJhXkEyXkFqcGdeQXVyNTIzOTk5ODM@._V1_SX300.jpg', 1, 1),
('Avatar', 'A paraplegic Marine dispatched to the moon Pandora on a unique mission becomes torn between following his orders and protecting the world he feels is his home.', 2009, 162, 15.99, 'https://m.media-amazon.com/images/M/MV5BZDA0OGQxNTItMDZkMC00N2UyLTg3MzMtYTJmNjg3Nzk5MzRiXkEyXkFqcGdeQXVyMjUzOTY1NTc@._V1_SX300.jpg', 2, 4),
('The Martian', 'An astronaut becomes stranded on Mars after his team assume him dead, and must rely on his ingenuity to find a way to signal to Earth that he is alive.', 2015, 144, 14.99, 'https://m.media-amazon.com/images/M/MV5BMTc2MTQ3MDA1Nl5BMl5BanBnXkFtZTgwODA3OTI4NjE@._V1_SX300.jpg', 3, 4),
('Gone Girl', 'With his wife''s disappearance having become the focus of an intense media circus, a man sees the spotlight turned on him when it''s suspected that he may not be innocent.', 2014, 149, 13.99, 'https://m.media-amazon.com/images/M/MV5BMTk0MDQ3MzAzOV5BMl5BanBnXkFtZTgwNzU1NzE3MjE@._V1_SX300.jpg', 4, 2),
('Moonrise Kingdom', 'A pair of young lovers flee their New England town, which causes a local search party to fan out to find them.', 2012, 94, 11.99, 'https://img.moviepostershop.com/moonrise-kingdom-movie-poster-2012-1020746934.jpg', 5, 3),
('Inception', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 2010, 148, 15.99, 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg', 6, 4),
('The Wolf of Wall Street', 'Based on the true story of Jordan Belfort, from his rise to a wealthy stock-broker living the high life to his fall involving crime, corruption and the federal government.', 2013, 180, 14.99, 'https://m.media-amazon.com/images/M/MV5BMjIxMjgxNTk0MF5BMl5BanBnXkFtZTgwNjIyOTg2MDE@._V1_SX300.jpg', 7, 2),
('Saving Private Ryan', 'Following the Normandy Landings, a group of U.S. soldiers go behind enemy lines to retrieve a paratrooper whose brothers have been killed in action.', 1998, 169, 13.99, 'https://m.media-amazon.com/images/M/MV5BZjhkMDM4MWItZTVjOC00ZDRhLThmYTAtM2I5NzBmNmNlMzI1XkEyXkFqcGdeQXVyNDYyMDk5MTU@._V1_SX300.jpg', 8, 1),
('Dune', 'A noble family becomes embroiled in a war for control over the galaxy''s most valuable asset while its heir becomes troubled by visions of a dark future.', 2021, 155, 16.99, 'https://m.media-amazon.com/images/M/MV5BN2FjNmEyNWMtYzM0ZS00NjIyLTg5YzYtYThlMGVjNzE1OGViXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_SX300.jpg', 9, 4),
('Get Out', 'A young African-American visits his white girlfriend''s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.', 2017, 104, 13.99, 'https://m.media-amazon.com/images/M/MV5BMjUxMDQwNjcyNl5BMl5BanBnXkFtZTgwNzcwMzc0MTI@._V1_SX300.jpg', 10, 5),
('Us', 'A family''s serene beach vacation turns to chaos when their doppelgängers appear and begin to terrorize them.', 2019, 116, 14.99, 'https://m.media-amazon.com/images/M/MV5BZTliNWJhM2YtNDc1MC00YTk1LWE2MGYtZmE4M2Y5ODdlNzQzXkEyXkFqcGdeQXVyMzY0MTE3NzU@._V1_SX300.jpg', 10, 5),
('The Shape of Water', 'At a top secret research facility in the 1960s, a lonely janitor forms a unique relationship with an amphibious creature that is being held in captivity.', 2017, 123, 13.99, 'https://m.media-amazon.com/images/M/MV5BNGNiNWQ5M2MtNGI0OC00MDA2LWI5NzEtMmZiYjVjMDEyOWYzXkEyXkFqcGdeQXVyMjM4NTM5NDY@._V1_SX300.jpg', 11, 2),
('Lady Bird', 'In 2002, an artistically inclined seventeen-year-old girl comes of age in Sacramento, California.', 2017, 94, 12.99, 'https://m.media-amazon.com/images/M/MV5BODhkZGE0NDQtZDc0Zi00YmQ4LWJiNmUtYTY1OGM1ODRmNGVkXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', 12, 2),
('Little Women', 'Jo March reflects back and forth on her life, telling the beloved story of the March sisters - four young women, each determined to live life on her own terms.', 2019, 135, 14.99, 'https://m.media-amazon.com/images/M/MV5BY2QzYTQyYzItMzAwYi00YjZlLThjNTUtNzMyMDdkYzJiNWM4XkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_SX300.jpg', 12, 2),
('Parasite', 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.', 2019, 132, 15.99, 'https://m.media-amazon.com/images/M/MV5BYWZjMjk3ZTItODQ2ZC00NTY5LWE0ZDYtZTI3MjcwN2Q5NTVkXkEyXkFqcGdeQXVyODk4OTc3MTY@._V1_SX300.jpg', 13, 2),
('Thor: Ragnarok', 'Thor must escape the alien planet Sakaar in time to save Asgard from Hela and the impending Ragnarök.', 2017, 130, 14.99, 'https://m.media-amazon.com/images/M/MV5BMjMyNDkzMzI1OF5BMl5BanBnXkFtZTgwODcxODg5MjI@._V1_SX300.jpg', 14, 6),
('Jumanji: Welcome to the Jungle', 'Four teenagers are sucked into a magical video game, and the only way they can escape is to work together to finish the game.', 2017, 119, 13.99, 'https://m.media-amazon.com/images/M/MV5BODQ0NDhjYWItYTMxZi00NTk2LWIzNDEtOWZiYWYxZjc2MTgxXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', 16, 6),
('The Lord of the Rings: The Fellowship of the Ring', 'A meek Hobbit from the Shire and eight companions set out on a journey to destroy the powerful One Ring and save Middle-earth from the Dark Lord Sauron.', 2001, 178, 15.99, 'https://m.media-amazon.com/images/M/MV5BN2EyZjM3NzUtNWUzMi00MTgxLWI0NTctMzY4M2VlOTdjZWRiXkEyXkFqcGdeQXVyNDUzOTQ5MjY@._V1_SX300.jpg', 17, 6),
('Shaun of the Dead', 'A man''s uneventful life is disrupted by the zombie apocalypse.', 2004, 99, 12.99, 'https://m.media-amazon.com/images/M/MV5BMTg5Mjk2NDMtZTk0Ny00YTQ0LWIzYWEtMWI5MGQ0Mjg1OTNkXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg', 15, 3),
('Hot Fuzz', 'A skilled London police officer is transferred to a small town with a dark secret.', 2007, 121, 12.99, 'https://m.media-amazon.com/images/M/MV5BMzg4MDJhMDMtYmJiMS00ZDZmLThmZWUtYTMwZDM1YTc5MWE2XkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg', 15, 3),
('The Jungle Book', 'After a threat from the tiger Shere Khan forces him to flee the jungle, a man-cub named Mowgli embarks on a journey of self discovery with the help of panther Bagheera and free-spirited bear Baloo.', 2016, 106, 13.99, 'https://m.media-amazon.com/images/M/MV5BMTc3NTUzNTI4MV5BMl5BanBnXkFtZTgwNjU0NjU5NzE@._V1_SX300.jpg', 16, 6);

-- Suppression des données existantes dans la table movie_actors pour éviter les conflits
DELETE FROM movie_actors;

-- Insertion des acteurs pour chaque film avec différents acteurs pour éviter les doublons de clé primaire
INSERT INTO movie_actors (movie_id, actor_id, role) VALUES
(1, 9, 'Vincent Vega'),      -- John Travolta dans Pulp Fiction
(2, 6, 'Jack Dawson'),       -- Leonardo DiCaprio dans Titanic
(2, 10, 'Rose DeWitt Bukater'), -- Kate Winslet dans Titanic
(3, 11, 'Maximus'),          -- Russell Crowe dans Gladiator
(4, 1, 'Tyler Durden'),      -- Brad Pitt dans Fight Club
(5, 12, 'M. Gustave'),       -- Ralph Fiennes dans The Grand Budapest Hotel
(6, 13, 'Harvey Dent'),      -- Aaron Eckhart dans The Dark Knight
(7, 14, 'Henry Hill'),       -- Ray Liotta dans Goodfellas
(8, 15, 'Dr. Alan Grant'),   -- Sam Neill dans Jurassic Park
(9, 1, 'Lt. Aldo Raine'),    -- Brad Pitt dans Inglourious Basterds
(10, 16, 'Jake Sully'),      -- Sam Worthington dans Avatar
(11, 17, 'Mark Watney'),     -- Matt Damon dans The Martian
(12, 18, 'Nick Dunne'),      -- Ben Affleck dans Gone Girl
(13, 19, 'Walt Bishop'),     -- Bill Murray dans Moonrise Kingdom
(14, 6, 'Dom Cobb'),         -- Leonardo DiCaprio dans Inception
(15, 6, 'Jordan Belfort'),   -- Leonardo DiCaprio dans The Wolf of Wall Street
(16, 20, 'Captain Miller'),  -- Tom Hanks dans Saving Private Ryan
(17, 21, 'Paul Atreides'),   -- Timothée Chalamet dans Dune
(17, 22, 'Chani'),           -- Zendaya dans Dune
(18, 23, 'Chris Washington'), -- Daniel Kaluuya dans Get Out
(19, 24, 'Adelaide Wilson/Red'), -- Lupita Nyong'o dans Us
(20, 25, 'Elisa Esposito'),  -- Sally Hawkins dans The Shape of Water
(20, 26, 'Amphibian Man'),   -- Doug Jones dans The Shape of Water
(21, 27, 'Christine "Lady Bird" McPherson'), -- Saoirse Ronan dans Lady Bird
(22, 27, 'Jo March'),        -- Saoirse Ronan dans Little Women
(22, 28, 'Amy March'),       -- Florence Pugh dans Little Women
(23, 29, 'Kim Ki-taek'),     -- Song Kang-ho dans Parasite
(23, 30, 'Kim Ki-woo'),      -- Choi Woo-shik dans Parasite
(24, 31, 'Thor'),           -- Chris Hemsworth dans Thor: Ragnarok
(25, 32, 'Ruby Roundhouse'), -- Karen Gillan dans Jumanji
(25, 33, 'Dr. Smolder Bravestone'), -- Dwayne Johnson dans Jumanji
(25, 34, 'Professor Shelly Oberon'), -- Jack Black dans Jumanji
(26, 35, 'Frodo Baggins'), -- Elijah Wood dans LOTR
(26, 36, 'Aragorn'),       -- Viggo Mortensen dans LOTR
(27, 37, 'Shaun'),         -- Simon Pegg dans Shaun of the Dead
(27, 38, 'Ed'),            -- Nick Frost dans Shaun of the Dead
(28, 37, 'Nicholas Angel'), -- Simon Pegg dans Hot Fuzz
(28, 38, 'Danny Butterman'), -- Nick Frost dans Hot Fuzz
(29, 33, 'Voice of Baloo'); -- Dwayne Johnson dans The Jungle Book

-- Requêtes de vérification des données
SELECT 'Directors' as 'Table', COUNT(*) as 'Count' FROM directors;
SELECT 'Actors' as 'Table', COUNT(*) as 'Count' FROM actors;
SELECT 'Movies' as 'Table', COUNT(*) as 'Count' FROM movies;
SELECT 'Movie Actors' as 'Table', COUNT(*) as 'Count' FROM movie_actors;

-- Liste des films avec leurs réalisateurs et catégories
SELECT 
    m.title as 'Film',
    d.name as 'Réalisateur',
    c.name as 'Catégorie',
    m.release_year as 'Année',
    m.duration as 'Durée (min)',
    m.price as 'Prix (€)',
    m.poster_path as 'Poster'
FROM movies m
JOIN directors d ON m.director_id = d.id
JOIN categories c ON m.category_id = c.id
ORDER BY m.title;

-- Liste des films avec leurs acteurs
SELECT 
    m.title as 'Film',
    a.name as 'Acteur',
    ma.role as 'Rôle'
FROM movies m
JOIN movie_actors ma ON m.id = ma.movie_id
JOIN actors a ON ma.actor_id = a.id
ORDER BY m.title, a.name; 