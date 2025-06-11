<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use CursoInstrutor;
use Dotenv\Dotenv;
use PDO;

class CursoInstrutorTest extends TestCase
{
    private static $db;
    private static $courseId;
    private static $docId;
    private static $discId;

    public static function setUpBeforeClass(): void
    {
        // Load env and establish DB connection
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'siscap03_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ensure pivot table exists by running migration
        $migration = file_get_contents(__DIR__ . '/../../database/create_curso_instrutores_table.sql');
        $stmts = array_filter(array_map('trim', explode(';', $migration)));
        foreach ($stmts as $stmt) {
            if ($stmt) {
                self::$db->exec($stmt);
            }
        }
        // Cleanup any existing dummy data
        self::$db->exec("DELETE FROM formacoes WHERE curso = 'TESTC'");
        self::$db->exec("DELETE FROM docentes WHERE matricula = 'TST' OR cpf = '00000000000'");
        self::$db->exec("DELETE FROM disciplinas WHERE nome = 'DISC'");
        // Create dummy course, docente, disciplina for pivot
        $stmt = self::$db->prepare("INSERT INTO formacoes (curso, ano, turma, inicio, termino, local, situacao) VALUES ('TESTC', '2025','T','2025-01-01','2025-01-02','L','EM ANDAMENTO')");
        $stmt->execute();
        self::$courseId = self::$db->lastInsertId();
        $stmt = self::$db->prepare("INSERT INTO docentes (matricula,nome,cpf,email) VALUES ('TST','TST','00000000000','t@t.com')");
        $stmt->execute();
        self::$docId = self::$db->lastInsertId();
        $stmt = self::$db->prepare("INSERT INTO disciplinas (nome) VALUES ('DISC')");
        $stmt->execute();
        self::$discId = self::$db->lastInsertId();
    }

    public function testCreateAndRead(): void
    {
        $pivot = new CursoInstrutor(self::$db);
        $pivot->formacao_id = self::$courseId;
        $pivot->docente_id = self::$docId;
        $pivot->disciplina_id = self::$discId;
        $pivot->created_by = 1;
        $this->assertTrue($pivot->create());
        $this->assertNotEmpty($pivot->id);

        $rows = (new CursoInstrutor(self::$db))->readByCourse(self::$courseId);
        $found = false;
        foreach ($rows as $r) {
            if ((int)$r['id'] === (int)$pivot->id) { $found = true; }
        }
        $this->assertTrue($found);

        // cleanup
        $pivot->delete();
    }

    public function testDeleteNonexistent(): void
    {
        $pivot = new CursoInstrutor(self::$db);
        $pivot->id = 0;
        $this->assertFalse($pivot->delete());
    }
}
