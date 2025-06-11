<?php
class Docente {
    // Database connection and table name
    private $conn;
    private $table_name = "docentes";
    
    // Object properties
    public $id;
    public $matricula;
    public $nome;
    public $rg;
    public $orgao_expedidor;
    public $cpf;
    public $lattes;
    public $email;
    public $fone_residencial;
    public $fone_profissional;
    public $fone_celular;
    public $instituicao;
    public $data_ingresso;
    public $cargo;
    public $lotacao;
    public $cidade_lotacao;
    public $disciplinas_professor;
    public $disciplinas_conteudista;
    public $formacao_tecnologica_1;
    public $formacao_tecnologica_2;
    public $graduacao_1;
    public $graduacao_2;
    public $graduacao_3;
    public $especializacao_gestao;
    public $especializacao_outros;
    public $mestrado;
    public $doutorado;
    public $pos_doutorado;
    public $situacao;
    public $classificacao;
    public $carga_horaria;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Read all professores
    public function readAll() {
        // Query to select all records
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read one professor
    public function readOne() {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind ID parameter
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set values to object properties
        if($row) {
            $this->matricula = $row['matricula'];
            $this->nome = $row['nome'];
            $this->rg = $row['rg'];
            $this->orgao_expedidor = $row['orgao_expedidor'];
            $this->cpf = $row['cpf'];
            $this->lattes = $row['lattes'];
            $this->email = $row['email'];
            $this->fone_residencial = $row['fone_residencial'];
            $this->fone_profissional = $row['fone_profissional'];
            $this->fone_celular = $row['fone_celular'];
            $this->instituicao = $row['instituicao'];
            $this->data_ingresso = $row['data_ingresso'];
            $this->cargo = $row['cargo'];
            $this->lotacao = $row['lotacao'];
            $this->cidade_lotacao = $row['cidade_lotacao'];
            $this->disciplinas_professor = $row['disciplinas_professor'];
            $this->disciplinas_conteudista = $row['disciplinas_conteudista'];
            $this->formacao_tecnologica_1 = $row['formacao_tecnologica_1'];
            $this->formacao_tecnologica_2 = $row['formacao_tecnologica_2'];
            $this->graduacao_1 = $row['graduacao_1'];
            $this->graduacao_2 = $row['graduacao_2'];
            $this->graduacao_3 = $row['graduacao_3'];
            $this->especializacao_gestao = $row['especializacao_gestao'];
            $this->especializacao_outros = $row['especializacao_outros'];
            $this->mestrado = $row['mestrado'];
            $this->doutorado = $row['doutorado'];
            $this->pos_doutorado = $row['pos_doutorado'];
            $this->situacao = $row['situacao'];
            $this->classificacao = $row['classificacao'];
            $this->carga_horaria = $row['carga_horaria'];
            
            return true;
        }
        
        return false;
    }
    
    // Create professor
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . " 
                  SET matricula=:matricula, nome=:nome, rg=:rg, orgao_expedidor=:orgao_expedidor, 
                  cpf=:cpf, lattes=:lattes, email=:email, fone_residencial=:fone_residencial, 
                  fone_profissional=:fone_profissional, fone_celular=:fone_celular, 
                  instituicao=:instituicao, data_ingresso=:data_ingresso, cargo=:cargo, 
                  lotacao=:lotacao, cidade_lotacao=:cidade_lotacao, 
                  disciplinas_professor=:disciplinas_professor, disciplinas_conteudista=:disciplinas_conteudista, 
                  formacao_tecnologica_1=:formacao_tecnologica_1, formacao_tecnologica_2=:formacao_tecnologica_2, 
                  graduacao_1=:graduacao_1, graduacao_2=:graduacao_2, graduacao_3=:graduacao_3, 
                  especializacao_gestao=:especializacao_gestao, especializacao_outros=:especializacao_outros, 
                  mestrado=:mestrado, doutorado=:doutorado, pos_doutorado=:pos_doutorado, 
                  situacao=:situacao, classificacao=:classificacao, carga_horaria=:carga_horaria";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->matricula = strip_tags($this->matricula);
        $this->nome = strip_tags($this->nome);
        // Convert to uppercase for consistency
        $this->nome = mb_strtoupper($this->nome, 'UTF-8');
        $this->rg = strip_tags($this->rg);
        $this->orgao_expedidor = strip_tags($this->orgao_expedidor);
        $this->cpf = strip_tags($this->cpf);
        $this->lattes = strip_tags($this->lattes);
        $this->email = strip_tags($this->email);
        $this->fone_residencial = strip_tags($this->fone_residencial);
        $this->fone_profissional = strip_tags($this->fone_profissional);
        $this->fone_celular = strip_tags($this->fone_celular);
        $this->instituicao = strip_tags($this->instituicao);
        $this->data_ingresso = strip_tags($this->data_ingresso);
        $this->cargo = strip_tags($this->cargo);
        $this->lotacao = strip_tags($this->lotacao);
        $this->cidade_lotacao = strip_tags($this->cidade_lotacao);
        $this->disciplinas_professor = strip_tags($this->disciplinas_professor);
        $this->disciplinas_conteudista = strip_tags($this->disciplinas_conteudista);
        $this->formacao_tecnologica_1 = strip_tags($this->formacao_tecnologica_1);
        $this->formacao_tecnologica_2 = strip_tags($this->formacao_tecnologica_2);
        $this->graduacao_1 = strip_tags($this->graduacao_1);
        $this->graduacao_2 = strip_tags($this->graduacao_2);
        $this->graduacao_3 = strip_tags($this->graduacao_3);
        $this->especializacao_gestao = strip_tags($this->especializacao_gestao);
        $this->especializacao_outros = strip_tags($this->especializacao_outros);
        $this->mestrado = strip_tags($this->mestrado);
        $this->doutorado = strip_tags($this->doutorado);
        $this->pos_doutorado = strip_tags($this->pos_doutorado);
        $this->situacao = strip_tags($this->situacao);
        $this->classificacao = strip_tags($this->classificacao);
        $this->carga_horaria = strip_tags($this->carga_horaria);
        
        // Bind parameters
        $stmt->bindParam(":matricula", $this->matricula);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":rg", $this->rg);
        $stmt->bindParam(":orgao_expedidor", $this->orgao_expedidor);
        $stmt->bindParam(":cpf", $this->cpf);
        $stmt->bindParam(":lattes", $this->lattes);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":fone_residencial", $this->fone_residencial);
        $stmt->bindParam(":fone_profissional", $this->fone_profissional);
        $stmt->bindParam(":fone_celular", $this->fone_celular);
        $stmt->bindParam(":instituicao", $this->instituicao);
        $stmt->bindParam(":data_ingresso", $this->data_ingresso);
        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":lotacao", $this->lotacao);
        $stmt->bindParam(":cidade_lotacao", $this->cidade_lotacao);
        $stmt->bindParam(":disciplinas_professor", $this->disciplinas_professor);
        $stmt->bindParam(":disciplinas_conteudista", $this->disciplinas_conteudista);
        $stmt->bindParam(":formacao_tecnologica_1", $this->formacao_tecnologica_1);
        $stmt->bindParam(":formacao_tecnologica_2", $this->formacao_tecnologica_2);
        $stmt->bindParam(":graduacao_1", $this->graduacao_1);
        $stmt->bindParam(":graduacao_2", $this->graduacao_2);
        $stmt->bindParam(":graduacao_3", $this->graduacao_3);
        $stmt->bindParam(":especializacao_gestao", $this->especializacao_gestao);
        $stmt->bindParam(":especializacao_outros", $this->especializacao_outros);
        $stmt->bindParam(":mestrado", $this->mestrado);
        $stmt->bindParam(":doutorado", $this->doutorado);
        $stmt->bindParam(":pos_doutorado", $this->pos_doutorado);
        $stmt->bindParam(":situacao", $this->situacao);
        $stmt->bindParam(":classificacao", $this->classificacao);
        $stmt->bindParam(":carga_horaria", $this->carga_horaria);
        
        // Execute query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Search for professores
    public function search($keywords) {
        // Sanitize
        $keywords = strip_tags($keywords);
        $keywords = "%{$keywords}%";
        
        // Query to search records
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nome LIKE ? OR matricula LIKE ? OR cpf LIKE ? 
                  ORDER BY nome";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update professor
    public function update() {
        // Sanitize
        $this->id = strip_tags($this->id);
        $this->matricula = strip_tags($this->matricula);
        $this->nome = strip_tags($this->nome);
        $this->nome = mb_strtoupper($this->nome, 'UTF-8');
        $this->rg = strip_tags($this->rg);
        $this->orgao_expedidor = strip_tags($this->orgao_expedidor);
        $this->cpf = strip_tags($this->cpf);
        $this->lattes = strip_tags($this->lattes);
        $this->email = strip_tags($this->email);
        $this->fone_residencial = strip_tags($this->fone_residencial);
        $this->fone_profissional = strip_tags($this->fone_profissional);
        $this->fone_celular = strip_tags($this->fone_celular);
        $this->instituicao = strip_tags($this->instituicao);
        $this->data_ingresso = strip_tags($this->data_ingresso);
        $this->cargo = strip_tags($this->cargo);
        $this->lotacao = strip_tags($this->lotacao);
        $this->cidade_lotacao = strip_tags($this->cidade_lotacao);
        $this->disciplinas_professor = strip_tags($this->disciplinas_professor);
        $this->disciplinas_conteudista = strip_tags($this->disciplinas_conteudista);
        $this->formacao_tecnologica_1 = strip_tags($this->formacao_tecnologica_1);
        $this->formacao_tecnologica_2 = strip_tags($this->formacao_tecnologica_2);
        $this->graduacao_1 = strip_tags($this->graduacao_1);
        $this->graduacao_2 = strip_tags($this->graduacao_2);
        $this->graduacao_3 = strip_tags($this->graduacao_3);
        $this->especializacao_gestao = strip_tags($this->especializacao_gestao);
        $this->especializacao_outros = strip_tags($this->especializacao_outros);
        $this->mestrado = strip_tags($this->mestrado);
        $this->doutorado = strip_tags($this->doutorado);
        $this->pos_doutorado = strip_tags($this->pos_doutorado);
        $this->situacao = strip_tags($this->situacao);
        $this->classificacao = strip_tags($this->classificacao);
        $this->carga_horaria = strip_tags($this->carga_horaria);
        
        // Query to update record
        $query = "UPDATE " . $this->table_name . " 
                  SET matricula=:matricula, nome=:nome, rg=:rg, orgao_expedidor=:orgao_expedidor, 
                  cpf=:cpf, lattes=:lattes, email=:email, fone_residencial=:fone_residencial, 
                  fone_profissional=:fone_profissional, fone_celular=:fone_celular, 
                  instituicao=:instituicao, data_ingresso=:data_ingresso, cargo=:cargo, 
                  lotacao=:lotacao, cidade_lotacao=:cidade_lotacao, 
                  disciplinas_professor=:disciplinas_professor, disciplinas_conteudista=:disciplinas_conteudista, 
                  formacao_tecnologica_1=:formacao_tecnologica_1, formacao_tecnologica_2=:formacao_tecnologica_2, 
                  graduacao_1=:graduacao_1, graduacao_2=:graduacao_2, graduacao_3=:graduacao_3, 
                  especializacao_gestao=:especializacao_gestao, especializacao_outros=:especializacao_outros, 
                  mestrado=:mestrado, doutorado=:doutorado, pos_doutorado=:pos_doutorado, 
                  situacao=:situacao, classificacao=:classificacao, carga_horaria=:carga_horaria
                  WHERE id = :id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":matricula", $this->matricula);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":rg", $this->rg);
        $stmt->bindParam(":orgao_expedidor", $this->orgao_expedidor);
        $stmt->bindParam(":cpf", $this->cpf);
        $stmt->bindParam(":lattes", $this->lattes);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":fone_residencial", $this->fone_residencial);
        $stmt->bindParam(":fone_profissional", $this->fone_profissional);
        $stmt->bindParam(":fone_celular", $this->fone_celular);
        $stmt->bindParam(":instituicao", $this->instituicao);
        $stmt->bindParam(":data_ingresso", $this->data_ingresso);
        $stmt->bindParam(":cargo", $this->cargo);
        $stmt->bindParam(":lotacao", $this->lotacao);
        $stmt->bindParam(":cidade_lotacao", $this->cidade_lotacao);
        $stmt->bindParam(":disciplinas_professor", $this->disciplinas_professor);
        $stmt->bindParam(":disciplinas_conteudista", $this->disciplinas_conteudista);
        $stmt->bindParam(":formacao_tecnologica_1", $this->formacao_tecnologica_1);
        $stmt->bindParam(":formacao_tecnologica_2", $this->formacao_tecnologica_2);
        $stmt->bindParam(":graduacao_1", $this->graduacao_1);
        $stmt->bindParam(":graduacao_2", $this->graduacao_2);
        $stmt->bindParam(":graduacao_3", $this->graduacao_3);
        $stmt->bindParam(":especializacao_gestao", $this->especializacao_gestao);
        $stmt->bindParam(":especializacao_outros", $this->especializacao_outros);
        $stmt->bindParam(":mestrado", $this->mestrado);
        $stmt->bindParam(":doutorado", $this->doutorado);
        $stmt->bindParam(":pos_doutorado", $this->pos_doutorado);
        $stmt->bindParam(":situacao", $this->situacao);
        $stmt->bindParam(":classificacao", $this->classificacao);
        $stmt->bindParam(":carga_horaria", $this->carga_horaria);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete professor
    public function delete() {
        // Query to delete a record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id of record to delete
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Count all professores
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Count professores matching search
    public function countSearch($keyword) {
        $keyword = htmlspecialchars(strip_tags($keyword));
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE nome LIKE ? OR matricula LIKE ? OR cpf LIKE ?";
        $stmt = $this->conn->prepare($query);
        $kw = "%{$keyword}%";
        $stmt->bindParam(1, $kw);
        $stmt->bindParam(2, $kw);
        $stmt->bindParam(3, $kw);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // Read paginated professores
    public function readPaged($limit, $offset) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Search professores with pagination
    public function searchPaged($keyword, $limit, $offset) {
        $keyword = htmlspecialchars(strip_tags($keyword));
        $query = "SELECT * FROM " . $this->table_name . " WHERE nome LIKE :kw OR matricula LIKE :kw OR cpf LIKE :kw ORDER BY nome LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':kw', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>