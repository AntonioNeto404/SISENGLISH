<?php
class Curso {
    private $conn;
    private $table_name = "formacoes";
    
    public $id;
    public $curso;
    public $ano;
    public $turma;
    public $inicio;
    public $termino;
    public $local;
    public $situacao;
    public $nome;
    public $tipo_capacitacao;
    public $modalidade;
    public $unidade;
    public $carga_horaria;
    public $instituicao;
    public $municipio;
    public $autorização;
    public $parecer;
    
    public function __construct($db) {
        $this->conn = $db;
    }
      // Create new course
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (curso, ano, turma, inicio, termino, local, situacao, tipo_capacitacao, modalidade, 
                  unidade, carga_horaria, instituicao, municipio, autorização, parecer) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        // Sanitize data - Apenas removendo tags HTML, preservando acentuação
        $this->curso = strip_tags($this->curso);
        $this->ano = strip_tags($this->ano);
        $this->turma = strip_tags($this->turma);
        $this->inicio = strip_tags($this->inicio);
        $this->termino = strip_tags($this->termino);
        $this->local = strip_tags($this->local);
        $this->situacao = strip_tags($this->situacao);
        $this->tipo_capacitacao = strip_tags($this->tipo_capacitacao);
        $this->modalidade = strip_tags($this->modalidade);
        $this->unidade = strip_tags($this->unidade);
        $this->carga_horaria = strip_tags($this->carga_horaria);
        $this->instituicao = strip_tags($this->instituicao);
        $this->municipio = strip_tags($this->municipio);
        $this->autorização = strip_tags($this->autorização);
        $this->parecer = strip_tags($this->parecer);        // Bind parameters
        $stmt->bindParam(1, $this->curso);
        $stmt->bindParam(2, $this->ano);
        $stmt->bindParam(3, $this->turma);
        $stmt->bindParam(4, $this->inicio);
        $stmt->bindParam(5, $this->termino);
        $stmt->bindParam(6, $this->local);
        $stmt->bindParam(7, $this->situacao);
        $stmt->bindParam(8, $this->tipo_capacitacao);
        $stmt->bindParam(9, $this->modalidade);
        $stmt->bindParam(10, $this->unidade);
        $stmt->bindParam(11, $this->carga_horaria);
        $stmt->bindParam(12, $this->instituicao);
        $stmt->bindParam(13, $this->municipio);
        $stmt->bindParam(14, $this->autorização);
        $stmt->bindParam(15, $this->parecer);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    // Read all courses
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ano DESC, inicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Filter courses
    public function filter($curso = null, $ano = null, $turma = null, $situacao = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if(!empty($curso)) {
            $query .= " AND curso LIKE :curso";
        }
        
        if(!empty($ano)) {
            $query .= " AND ano LIKE :ano";
        }
        
        if(!empty($turma)) {
            $query .= " AND turma LIKE :turma";
        }
        
        if(!empty($situacao)) {
            $query .= " AND situacao LIKE :situacao";
        }
        
        $query .= " ORDER BY ano DESC, inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        if(!empty($curso)) {
            // Sanitize input
            $curso = strip_tags($curso);
            $curso = "%{$curso}%";
            $stmt->bindParam(":curso", $curso);
        }
        
        if(!empty($ano)) {
            // Sanitize input
            $ano = strip_tags($ano);
            $ano = "%{$ano}%";
            $stmt->bindParam(":ano", $ano);
        }
        
        if(!empty($turma)) {
            // Sanitize input
            $turma = strip_tags($turma);
            $turma = "%{$turma}%";
            $stmt->bindParam(":turma", $turma);
        }
        
        if(!empty($situacao)) {
            // Sanitize input
            $situacao = strip_tags($situacao);
            $situacao = "%{$situacao}%";
            $stmt->bindParam(":situacao", $situacao);
        }
        
        $stmt->execute();
        
        return $stmt;
    }

    // Read one course
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->curso = $row['curso'];
            $this->ano = $row['ano'];
            $this->turma = $row['turma'];
            $this->inicio = $row['inicio'];
            $this->termino = $row['termino'];
            $this->local = $row['local'];
            $this->situacao = $row['situacao'];
            $this->tipo_capacitacao = $row['tipo_capacitacao'] ?? '';
            $this->modalidade = $row['modalidade'] ?? '';
            $this->unidade = $row['unidade'] ?? '';
            $this->carga_horaria = $row['carga_horaria'] ?? '';
            $this->instituicao = $row['instituicao'] ?? '';
            $this->municipio = $row['municipio'] ?? '';
            $this->autorização = $row['autorização'] ?? '';
            $this->parecer = $row['parecer'] ?? '';
            return true;
        }
        
        return false;
    }    // Update course
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                  curso = ?, ano = ?, turma = ?, inicio = ?, termino = ?, 
                  local = ?, situacao = ?, tipo_capacitacao = ?, modalidade = ?, 
                  unidade = ?, carga_horaria = ?, instituicao = ?, municipio = ?, 
                  autorização = ?, parecer = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Sanitize and prepare data - Apenas removendo tags HTML, preservando acentuação
        $this->curso = strip_tags($this->curso);
        $this->ano = strip_tags($this->ano);
        $this->turma = strip_tags($this->turma);
        $this->inicio = strip_tags($this->inicio);
        $this->termino = strip_tags($this->termino);
        $this->local = strip_tags($this->local);
        $this->situacao = strip_tags($this->situacao);
        $this->tipo_capacitacao = strip_tags($this->tipo_capacitacao);
        $this->modalidade = strip_tags($this->modalidade);
        $this->unidade = strip_tags($this->unidade);
        $this->carga_horaria = strip_tags($this->carga_horaria);
        $this->instituicao = strip_tags($this->instituicao);
        $this->municipio = strip_tags($this->municipio);
        $this->autorização = strip_tags($this->autorização);
        $this->parecer = strip_tags($this->parecer);

        // Bind values
        $stmt->bindParam(1, $this->curso);
        $stmt->bindParam(2, $this->ano);
        $stmt->bindParam(3, $this->turma);
        $stmt->bindParam(4, $this->inicio);
        $stmt->bindParam(5, $this->termino);
        $stmt->bindParam(6, $this->local);
        $stmt->bindParam(7, $this->situacao);
        $stmt->bindParam(8, $this->tipo_capacitacao);
        $stmt->bindParam(9, $this->modalidade);
        $stmt->bindParam(10, $this->unidade);
        $stmt->bindParam(11, $this->carga_horaria);
        $stmt->bindParam(12, $this->instituicao);
        $stmt->bindParam(13, $this->municipio);
        $stmt->bindParam(14, $this->autorização);
        $stmt->bindParam(15, $this->parecer);
        $stmt->bindParam(16, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Delete course
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Filter courses by period
    public function filterByPeriodo($start, $end) {
        // Sanitize inputs
        $start = htmlspecialchars(strip_tags($start));
        $end = htmlspecialchars(strip_tags($end));
        
        $query = "SELECT * FROM " . $this->table_name . " WHERE inicio >= :start AND termino <= :end ORDER BY inicio DESC";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(":start", $start);
        $stmt->bindParam(":end", $end);
        $stmt->execute();
        return $stmt;
    }

    // Count all courses
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Count courses matching filters
    public function countFilter($curso = null, $ano = null, $turma = null, $situacao = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        if(!empty($curso)) $query .= " AND curso LIKE :curso";
        if(!empty($ano)) $query .= " AND ano LIKE :ano";
        if(!empty($turma)) $query .= " AND turma LIKE :turma";
        if(!empty($situacao)) $query .= " AND situacao LIKE :situacao";
        $stmt = $this->conn->prepare($query);
        if(!empty($curso)) { $val = "%".strip_tags(html_entity_decode($curso, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':curso', $val); }
        if(!empty($ano))   { $val = "%".strip_tags(html_entity_decode($ano, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%";   $stmt->bindParam(':ano', $val); }
        if(!empty($turma)){ $val = "%".strip_tags(html_entity_decode($turma, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':turma', $val); }
        if(!empty($situacao)) { $val = "%".strip_tags(html_entity_decode($situacao, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':situacao', $val); }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Read paginated courses without filters
    public function readPaged($limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY ano DESC, inicio DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Read paginated courses with filters
    public function filterPaged($curso = null, $ano = null, $turma = null, $situacao = null, $limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        if(!empty($curso)) $query .= " AND curso LIKE :curso";
        if(!empty($ano)) $query .= " AND ano LIKE :ano";
        if(!empty($turma)) $query .= " AND turma LIKE :turma";
        if(!empty($situacao)) $query .= " AND situacao LIKE :situacao";
        $query .= " ORDER BY ano DESC, inicio DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        if(!empty($curso)) { $val = "%".strip_tags(html_entity_decode($curso, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':curso', $val); }
        if(!empty($ano))   { $val = "%".strip_tags(html_entity_decode($ano, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%";   $stmt->bindParam(':ano', $val); }
        if(!empty($turma)){ $val = "%".strip_tags(html_entity_decode($turma, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':turma', $val); }
        if(!empty($situacao)) { $val = "%".strip_tags(html_entity_decode($situacao, ENT_QUOTES | ENT_HTML5, 'UTF-8'))."%"; $stmt->bindParam(':situacao', $val); }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>
