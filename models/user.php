<?php
class User {
    private $conn;
    private $table_name = "usuarios";
    
    public $id;
    public $cognome;
    public $senha;
    public $nome;
    public $tipo;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Login user
    public function login() {
        // Verifica se está usando o novo sistema de senhas (com hash)
        $query = "SELECT id, cognome, senha, nome, tipo FROM " . $this->table_name . " WHERE cognome = ?";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $this->cognome);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica se a senha usa hash
            if (strlen($row['senha']) > 40) { // Hash bcrypt tem comprimento > 40
                // Verifica a senha usando password_verify
                if (password_verify($this->senha, $row['senha'])) {
                    $this->id = $row['id'];
                    $this->nome = $row['nome'];
                    $this->tipo = $row['tipo'];
                    return true;
                }
            } else {
                // Sistema antigo sem hash (verificação legada)
                if ($this->senha === $row['senha']) {
                    // Se o login for bem-sucedido, atualiza para o novo sistema de hash
                    $this->id = $row['id'];
                    $this->nome = $row['nome'];
                    $this->tipo = $row['tipo'];
                    
                    // Atualiza a senha para usar hash
                    $this->updatePasswordHash($this->id, $this->senha);
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    // Atualiza a senha do usuário para usar hash bcrypt
    private function updatePasswordHash($user_id, $senha) {
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Gera o hash da senha
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
        
        // Bind values
        $stmt->bindParam(1, $senha_hash);
        $stmt->bindParam(2, $user_id);
        
        $stmt->execute();
    }
    
    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (cognome, senha, nome, tipo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->cognome = strip_tags($this->cognome);
        $this->senha = password_hash(strip_tags($this->senha), PASSWORD_BCRYPT);
        $this->nome = strip_tags($this->nome);
        $this->tipo = strip_tags($this->tipo);
        
        // Bind values
        $stmt->bindParam(1, $this->cognome);
        $stmt->bindParam(2, $this->senha);
        $stmt->bindParam(3, $this->nome);
        $stmt->bindParam(4, $this->tipo);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Read all users
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY cognome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Verifica se o usuário existe
    public function userExists() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE cognome = ?";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $this->cognome);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'] > 0;
    }
    
    // Atualiza senha do usuário
    public function updatePassword() {
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Gera o hash da senha
        $this->senha = password_hash(strip_tags($this->senha), PASSWORD_BCRYPT);
        
        // Bind values
        $stmt->bindParam(1, $this->senha);
        $stmt->bindParam(2, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
