<?php
class CursoProfessor {
    private $conn;
    private $table_name = 'curso_professores';

    public $id;
    public $formacao_id;
    public $professor_id;
    public $disciplina_id;
    public $created_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new association
    public function create() {
        $query = "INSERT INTO " . $this->table_name .
                 " (formacao_id, professor_id, disciplina_id, created_by)" .
                 " VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(1, $this->formacao_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $this->professor_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $this->disciplina_id, PDO::PARAM_INT);
        $stmt->bindParam(4, $this->created_by, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read instructors for a course
    public function readByCourse($formacao_id) {
        $query = "SELECT ci.id, d.nome as professor, di.nome as disciplina, ci.created_at" .
                 " FROM " . $this->table_name . " ci" .
                 " JOIN professores d ON ci.professor_id = d.id" .
                 " JOIN disciplinas di ON ci.disciplina_id = di.id" .
                 " WHERE ci.formacao_id = ?" .
                 " ORDER BY ci.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $formacao_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Delete assignment
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
