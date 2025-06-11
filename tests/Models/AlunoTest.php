<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Aluno;
use Dotenv\Dotenv;
use PDO;

class AlunoTest extends TestCase
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

        // Clean up test data
        self::$db->exec("DELETE FROM alunos WHERE matricula LIKE 'TEST%'");
    }

    public function testCreateAndRead(): void
    {
        $aluno = new Aluno(self::$db);
        $aluno->matricula = 'TEST123';
        $aluno->nome = 'Test User';
        $aluno->posto = 'Tester';
        $aluno->forca = 'OUTRO';

        $this->assertTrue($aluno->create());
        $this->assertNotEmpty($aluno->id);

        $aluno2 = new Aluno(self::$db);
        $aluno2->id = $aluno->id;
        $this->assertTrue($aluno2->readOne());
        $this->assertEquals('TEST123', $aluno2->matricula);
        $this->assertEquals('TEST USER', $aluno2->nome);
    }

    public function testCountAndPagination(): void
    {
        $aluno = new Aluno(self::$db);

        // Ensure at least one record
        $total = $aluno->countAll();
        $this->assertIsInt($total);

        // Test paged
        $stmt = $aluno->readPaged(5, 0);
        $this->assertLessThanOrEqual(5, $stmt->rowCount());

        // Test search count
        $countSearch = $aluno->countSearch('TEST');
        $this->assertIsInt($countSearch);
    }
}
