<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use PDO;

class DocenteTest extends TestCase
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
        self::$db->exec("DELETE FROM docentes WHERE matricula LIKE 'TEST%'");
    }

    public function testCreateAndRead(): void
    {
        $docente = new \Docente(self::$db);
        $docente->matricula = 'TESTDOC';
        $docente->nome = 'Doc Test';
        $docente->cpf = '12345678901';
        $docente->email = 'doc@test.com';
        // minimal required
        $this->assertTrue($docente->create());
        $this->assertNotEmpty($docente->id);

        $d2 = new \Docente(self::$db);
        $d2->id = $docente->id;
        $this->assertTrue($d2->readOne());
        $this->assertEquals('TESTDOC', $d2->matricula);
        $this->assertEquals('DOC TEST', $d2->nome);
    }

    public function testCountAndPagination(): void
    {
        $docente = new \Docente(self::$db);
        $total = $docente->countAll();
        $this->assertIsInt($total);

        $stmt = $docente->readPaged(5, 0);
        $this->assertLessThanOrEqual(5, $stmt->rowCount());

        $countSearch = $docente->countSearch('TEST');
        $this->assertIsInt($countSearch);
    }
}
