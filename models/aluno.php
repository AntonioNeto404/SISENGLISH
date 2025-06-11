<?php
class Aluno {
    private $conn;
    private $table_name = "alunos";
    
    public $id;
    public $matricula;
    public $nome;
    public $posto; // Manter compatibilidade com banco atual
    public $forca; // Manter compatibilidade com banco atual
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new student
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (matricula, nome, posto, forca) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize data
        $this->matricula = strip_tags($this->matricula);
        $this->nome = strip_tags($this->nome);
        $this->posto = strip_tags($this->posto);
        $this->forca = strip_tags($this->forca);
        
        // Convert to uppercase
        $this->nome = mb_strtoupper($this->nome, 'UTF-8');
        
        // Bind values
        $stmt->bindParam(1, $this->matricula);
        $stmt->bindParam(2, $this->nome);
        $stmt->bindParam(3, $this->posto);
        $stmt->bindParam(4, $this->forca);
        
        if($stmt->execute()) {
            // Obter o ID do registro inserido
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Read all students
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read paginated students
    public function readPaged($limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Search students
    public function search($keyword) {
        $keyword = strip_tags($keyword);
        
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nome LIKE ? OR matricula LIKE ? 
                  ORDER BY nome";
        
        $stmt = $this->conn->prepare($query);
        
        $keyword = "%{$keyword}%";
        $stmt->bindParam(1, $keyword);
        $stmt->bindParam(2, $keyword);
        
        $stmt->execute();
        
        return $stmt;
    }
      // Search students with pagination
    public function searchPaged($keyword, $limit, $offset) {
        $keyword = html_entity_decode(strip_tags($keyword), ENT_QUOTES, 'UTF-8');
        $query = "SELECT * FROM " . $this->table_name . " WHERE nome LIKE :kw OR matricula LIKE :kw ORDER BY nome LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kw', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    
    // Get student by ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->matricula = $row['matricula'];
            $this->nome = $row['nome'];
            $this->posto = $row['posto'];
            $this->forca = $row['forca'];
            return true;
        }
        
        return false;
    }

    // Get students not enrolled in a specific course
    public function readNotEnrolled($curso_id) {
        $query = "SELECT a.* FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = ?
                  )
                  ORDER BY a.nome";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $curso_id);
        $stmt->execute();
        
        return $stmt;
    }

    // Search students not enrolled in a specific course
    public function searchNotEnrolled($curso_id, $keyword) {
        $query = "SELECT a.* FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = ?
                  )
                  AND (a.nome LIKE ? OR a.matricula LIKE ?)
                  ORDER BY a.nome";
        
        $stmt = $this->conn->prepare($query);
        
        $keyword = "%{$keyword}%";
        $stmt->bindParam(1, $curso_id);
        $stmt->bindParam(2, $keyword);
        $stmt->bindParam(3, $keyword);
        
        $stmt->execute();
        
        return $stmt;
    }    // Update student
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET matricula = ?, nome = ?, posto = ?, forca = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Sanitize and prepare data
        $this->matricula = strip_tags($this->matricula);
        $this->nome = mb_strtoupper(strip_tags($this->nome), 'UTF-8');
        $this->posto = strip_tags($this->posto);
        $this->forca = strip_tags($this->forca);

        // Bind values        $stmt->bindParam(1, $this->matricula);
        $stmt->bindParam(2, $this->nome);
        $stmt->bindParam(3, $this->posto);
        $stmt->bindParam(4, $this->forca);
        $stmt->bindParam(5, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete student
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

    // Search students by matricula
    public function searchByMatricula($matricula) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE matricula LIKE ? 
                  ORDER BY matricula";
        
        $stmt = $this->conn->prepare($query);
        
        $matricula = "%{$matricula}%";
        $stmt->bindParam(1, $matricula);
        
        $stmt->execute();
        
        return $stmt;
    }

    // Count all students
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }    // Count students matching search
    public function countSearch($keyword) {
        $keyword = html_entity_decode(strip_tags($keyword), ENT_QUOTES, 'UTF-8');
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE nome LIKE ? OR matricula LIKE ?";
        $stmt = $this->conn->prepare($query);
        $kw = "%{$keyword}%";
        $stmt->bindParam(1, $kw);
        $stmt->bindParam(2, $kw);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Count students not enrolled in a specific course
    public function countNotEnrolled($curso_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = ?
                  )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $curso_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Count not-enrolled students matching search
    public function countSearchNotEnrolled($curso_id, $keyword) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = ?
                  )
                  AND (a.nome LIKE ? OR a.matricula LIKE ?)";
        $stmt = $this->conn->prepare($query);
        $kw = "%" . strip_tags($keyword) . "%";
        $stmt->bindParam(1, $curso_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $kw, PDO::PARAM_STR);
        $stmt->bindParam(3, $kw, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Read paginated not-enrolled students
    public function readNotEnrolledPaged($curso_id, $limit, $offset) {
        $query = "SELECT a.* FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = :curso_id
                  )
                  ORDER BY a.nome
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':curso_id', (int)$curso_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Search and paginate not-enrolled students
    public function searchNotEnrolledPaged($curso_id, $keyword, $limit, $offset) {
        $query = "SELECT a.* FROM " . $this->table_name . " a
                  WHERE a.id NOT IN (
                      SELECT m.estudante_id FROM matriculas m WHERE m.formacao_id = :curso_id
                  )
                  AND (a.nome LIKE :kw OR a.matricula LIKE :kw)
                  ORDER BY a.nome
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':curso_id', (int)$curso_id, PDO::PARAM_INT);
        $stmt->bindValue(':kw', "%" . strip_tags($keyword) . "%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>
