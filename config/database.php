<?php
// config/database.php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    
    public function __construct() {
        // Read from environment or set defaults
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'siscap03_db';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
    }
    
    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $this->host, $this->db_name); // Changed charset to utf8mb4
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'" // Added SET NAMES
            ]);
        } catch (PDOException $exception) {
            // Log error and exit
            error_log('Database Connection Error: ' . $exception->getMessage());
            die('Database connection failed.');
        }
        
        return $this->conn;
    }
    
    // Check if system has expired
    public function checkExpiration() {
        try {
            $query = "SELECT * FROM expiracao WHERE id = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $expiry_date = strtotime($row["data_exp"]);
                $current_date = time();
                
                if($current_date > $expiry_date) {
                    return true; // System has expired
                }
            }
            return false; // Not expired
        } catch(PDOException $exception) {
            return false;
        }
    }
}
?>