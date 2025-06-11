<?php
class Disciplina {
    private $conn;
    private $table_name = "disciplinas";
    
    public $id;
    public $nome;
    public $descricao;
    public $carga_horaria;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new disciplina
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, descricao, carga_horaria) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and prepare data
        $this->nome = strip_tags($this->nome);
        $this->descricao = strip_tags($this->descricao);
        
        // Bind values
        $stmt->bindParam(1, $this->nome);
        $stmt->bindParam(2, $this->descricao);
        $stmt->bindParam(3, $this->carga_horaria);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }

    // Find disciplina by name
    public function findByName($name) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nome = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $name);
        $stmt->execute();
        return $stmt;
    }
    
    // Read all disciplinas
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read one disciplina
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->nome = $row['nome'];
            $this->descricao = $row['descricao'];
            $this->carga_horaria = $row['carga_horaria'];
            return $stmt;
        }
        
        return false;
    }

    // Update disciplina
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome = ?, descricao = ?, carga_horaria = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->nome = strip_tags($this->nome);
        $this->descricao = strip_tags($this->descricao);
        
        // Bind values
        $stmt->bindParam(1, $this->nome);
        $stmt->bindParam(2, $this->descricao);
        $stmt->bindParam(3, $this->carga_horaria);
        $stmt->bindParam(4, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete disciplina
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>