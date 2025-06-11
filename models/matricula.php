<?php
class Matricula {
    private $conn;
    private $table_name = "matriculas";
    
    public $id;
    public $formacao_id;
    public $estudante_id;
    public $situacao;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Enroll student in course
    public function create() {
        // Check if already enrolled
        $check_query = "SELECT * FROM " . $this->table_name . " WHERE formacao_id = ? AND estudante_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->formacao_id);
        $check_stmt->bindParam(2, $this->estudante_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return false; // Already enrolled
        }
        
        // Proceed with enrollment
        $query = "INSERT INTO " . $this->table_name . " 
                  (formacao_id, estudante_id, situacao) 
                  VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Set default situacao if not provided
        if(empty($this->situacao)) {
            $this->situacao = "ATIVO";
        }
        
        // Sanitize data
        $this->situacao = strip_tags($this->situacao);
        
        // Bind values
        $stmt->bindParam(1, $this->formacao_id);
        $stmt->bindParam(2, $this->estudante_id);
        $stmt->bindParam(3, $this->situacao);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
      // Get students in a course
    public function getStudentsByCourse() {
        $query = "SELECT m.id, m.situacao, a.id as estudante_id, a.matricula, a.nome, a.nível de inglês, a.forca 
                  FROM " . $this->table_name . " m
                  JOIN estudantes a ON m.estudante_id = a.id
                  WHERE m.formacao_id = ?
                  ORDER BY a.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->formacao_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get courses for a student
    public function getCoursesByStudent() {
        $query = "SELECT m.id, m.situacao, f.curso, f.ano, f.turma, f.inicio, f.termino 
                  FROM " . $this->table_name . " m
                  JOIN formacoes f ON m.formacao_id = f.id
                  WHERE m.estudante_id = ?
                  ORDER BY f.inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->estudante_id);
        $stmt->execute();
        
        return $stmt;
    }

    // Update matricula status
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET situacao = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize data
        $this->situacao = strip_tags($this->situacao);
        
        // Bind values
        $stmt->bindParam(1, $this->situacao);
        $stmt->bindParam(2, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Delete matricula
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
