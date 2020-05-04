<?php
namespace Moxio\SQLiteExtendedAPI\Test\WrappedConnection;

use Moxio\SQLiteExtendedAPI\Facade;
use Moxio\SQLiteExtendedAPI\WrappedConnection;
use PHPUnit\Framework\TestCase;

class ExtensionLoadingTest extends TestCase {
    private \PDO $pdo;
    private WrappedConnection $wrapped_connection;

    protected function setUp(): void {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->wrapped_connection = Facade::wrapPDO($this->pdo);
    }

    private const EXTENSION = 'mod_spatialite.so';
    private const EXTENSION_VERIFICATION_QUERY = "SELECT ST_AsText(ST_GeomFromText('POINT(155000 463000)'))";

    public function testLoadExtensionLoadsAnSQLiteExtension() {
        $extension_dir = ini_get('sqlite3.extension_dir') ?: '/usr/lib/x86_64-linux-gnu';
        $extension_file = $extension_dir . '/' . self::EXTENSION;
        if (!file_exists($extension_file)) {
            $this->markTestSkipped(sprintf("SQLite extension file '%s' needed for test not found", self::EXTENSION));
        }

        $this->wrapped_connection->loadExtension(self::EXTENSION);
        $this->assertNotFalse($this->pdo->query(self::EXTENSION_VERIFICATION_QUERY));
    }
}
