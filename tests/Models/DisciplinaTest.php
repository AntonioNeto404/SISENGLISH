<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Disciplina;
use Dotenv\Dotenv;
use PDO;

class DisciplinaTest extends TestCase
{
    private static $db;
    private static $discId;

    public static function setUpBeforeClass(): void
    {
        // Load environment and connect to DB
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'siscap03_db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ensure disciplinas table exists via migration
        $migration = file_get_contents(__DIR__ . '/../../database/create_disciplinas_table.sql');
        foreach (array_filter(array_map('trim', explode(';', $migration))) as $stmt) {
            if ($stmt) self::$db->exec($stmt);
        }
    }

    public function testCreateAndReadOne(): void
    {
        // Cleanup any previous test record
        self::$db->exec("DELETE FROM disciplinas WHERE nome = 'TEST_DISC'");

        $disc = new Disciplina(self::$db);
        $disc->nome = 'TEST_DISC';
        $disc->descricao = 'Descrição de teste';
        $disc->carga_horaria = 10;
        $this->assertTrue($disc->create());

        // Fetch via readAll
        $rows = $disc->readAll()->fetchAll(PDO::FETCH_ASSOC);
        $found = false;
        foreach ($rows as $r) {
            if ($r['nome'] === 'TEST_DISC') {
                $found = true;
                self::$discId = $r['id'];
            }
        }
        $this->assertTrue($found);

        // Test readOne
        $disc2 = new Disciplina(self::$db);
        $disc2->id = self::$discId;
        $stmt = $disc2->readOne();
        $this->assertEquals('TEST_DISC', $disc2->nome);
        $this->assertEquals('Descrição de teste', $disc2->descricao);
        $this->assertEquals(10, $disc2->carga_horaria);
    }

    public function testUpdate(): void
    {
        $disc = new Disciplina(self::$db);
        $disc->id = self::$discId;
        $disc->nome = 'UPDATED_DISC';
        $disc->descricao = 'Atualizado';
        $disc->carga_horaria = 20;
        $this->assertTrue($disc->update());

        $disc2 = new Disciplina(self::$db);
        $disc2->id = self::$discId;
        $disc2->readOne();
        $this->assertEquals('UPDATED_DISC', $disc2->nome);
        $this->assertEquals('Atualizado', $disc2->descricao);
        $this->assertEquals(20, $disc2->carga_horaria);
    }

    public function testDelete(): void
    {
        $disc = new Disciplina(self::$db);
        $disc->id = self::$discId;
        $this->assertTrue($disc->delete());

        // Ensure deletion
        $stmt = self::$db->prepare('SELECT id FROM disciplinas WHERE id = ?');
        $stmt->execute([self::$discId]);
        $this->assertFalse((bool)$stmt->fetch());
    }
}
