<?php
class Library {
    private $userId;
    
    public function __construct($userId) {
        $this->userId = $userId;
    }
    
    public function getMovies() {
        return fetchAll(
            "SELECT DISTINCT m.*, d.name as director_name, c.name as category_name
             FROM movies m
             JOIN order_items oi ON m.id = oi.movie_id
             JOIN orders o ON oi.order_id = o.id
             LEFT JOIN directors d ON m.director_id = d.id
             LEFT JOIN categories c ON m.category_id = c.id
             WHERE o.user_id = ? AND o.status = 'completed'
             ORDER BY m.title ASC",
            [$this->userId]
        );
    }
    
    public function getMovie($movieId) {
        $movie = fetchOne(
            "SELECT m.*, d.name as director_name, c.name as category_name,
                    (SELECT COUNT(1) FROM orders o JOIN order_items oi ON o.id = oi.order_id 
                     WHERE o.user_id = ? AND oi.movie_id = ? AND o.status = 'completed') as purchased
             FROM movies m
             LEFT JOIN directors d ON m.director_id = d.id
             LEFT JOIN categories c ON m.category_id = c.id
             WHERE m.id = ?",
            [$this->userId, $movieId, $movieId]
        );
        
        if ($movie) {
            $movie['purchased'] = (int)$movie['purchased'] > 0;
        }
        
        return $movie;
    }
} 