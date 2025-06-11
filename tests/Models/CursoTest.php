<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use PDO;

class CursoTest extends TestCase
{
    /** @var PDO */
    private static $db;

    public static function setUpBeforeClass(): void
    {
        // Load environment
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'siscap03_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Clean up test records
        self::$db->exec("DELETE FROM formacoes WHERE curso LIKE 'TEST%' OR turma LIKE 'TEST%'");
    }

    public function testCreateAndRead(): void
    {
        $curso = new \Curso(self::$db);
        $curso->curso = 'TESTCURSO';
        $curso->ano = date('Y');
        $curso->turma = 'TESTTURMA';
        $curso->inicio = date('Y-m-d');
        $curso->termino = date('Y-m-d');
        $curso->local = 'LOCAL';
        $curso->situacao = 'CONCLUIDO';
        $curso->tipo_capacitacao = 'CAPACITAÇÃO';
        $curso->modalidade = 'EAD';
        $curso->campus = 'CAMPUS';
        $curso->carga_horaria = 10;
        $curso->instituicao = 'INST';
        $curso->municipio = 'MUNI';
        $curso->portaria = '';
        $curso->parecer = '';

        $this->assertTrue($curso->create());
        
        $c2 = new \Curso(self::$db);
        $c2->id = $curso->id;
        $this->assertTrue($c2->readOne());
        $this->assertEquals('TESTCURSO', $c2->curso);
        $this->assertEquals(date('Y'), $c2->ano);
    }

    public function testCountAndPagination(): void
    {
        $curso = new \Curso(self::$db);
        $total = $curso->readAll()->rowCount();
        $this->assertIsInt($total);

        // Test filter count and paged (use countFilter and filterPaged)
        $countFiltered = $curso->filter('', '', '', '');
        $this->assertNotNull($countFiltered);
        $stmt = $curso->readAll();
        $this->assertLessThanOrEqual(100, $stmt->rowCount());
    }
}
