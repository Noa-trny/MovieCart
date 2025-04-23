<?php
class Library {
    private $userId;
    
    public function __construct($userId) {
        $this->userId = $userId;
    }
    
    public function getMovies() {
        $conn = connectDB();
        $movies = [];
        
        $stmt = $conn->prepare(
            "SELECT DISTINCT m.*, d.name as director_name, c.name as category_name, o.created_at as purchase_date
             FROM movies m
             JOIN order_items oi ON m.id = oi.movie_id
             JOIN orders o ON oi.order_id = o.id
             LEFT JOIN directors d ON m.director_id = d.id
             LEFT JOIN categories c ON m.category_id = c.id
             WHERE o.user_id = ? AND o.status = 'completed'
             ORDER BY o.created_at DESC, m.title ASC"
        );
        
        if ($stmt) {
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
            
            $stmt->close();
        }
        
        $conn->close();
        return $movies;
    }
    
    public function getMovie($movieId) {
        $conn = connectDB();
        $movie = null;
        
        $stmt = $conn->prepare(
            "SELECT m.*, d.name as director_name, c.name as category_name,
                    (SELECT COUNT(1) FROM orders o JOIN order_items oi ON o.id = oi.order_id 
                     WHERE o.user_id = ? AND oi.movie_id = ? AND o.status = 'completed') as purchased
             FROM movies m
             LEFT JOIN directors d ON m.director_id = d.id
             LEFT JOIN categories c ON m.category_id = c.id
             WHERE m.id = ?"
        );
        
        if ($stmt) {
            $stmt->bind_param("iii", $this->userId, $movieId, $movieId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $movie = $result->fetch_assoc();
                $movie['purchased'] = (int)$movie['purchased'] > 0;
            }
            
            $stmt->close();
        }
        
        $conn->close();
        return $movie;
    }
} 