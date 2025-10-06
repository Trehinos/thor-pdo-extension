<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Database\PdoExtension\ArrayCrud;
use Thor\Database\PdoExtension\Criteria;
use Thor\Database\PdoExtension\Handler;
use Thor\Database\PdoExtension\Requester;

final class ArrayCrudTest extends TestCase
{
    private static Requester $req;

    public static function setUpBeforeClass(): void
    {
        self::$req = new Requester(new Handler('sqlite::memory:'));
        self::$req->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
    }

    public function testCrudFlow(): void
    {
        $crud = new ArrayCrud('users', ['id'], self::$req);

        // Create one
        $idString = $crud->createOne(['id' => 1, 'name' => 'Alice']);
        $this->assertSame('1', $idString);

        // Read one
        $row = $crud->readOne(['id' => 1]);
        $this->assertSame('Alice', $row['name'] ?? null);

        // Update
        $ok = $crud->update(['name' => 'Alice Cooper'], new Criteria(['id' => 1]));
        $this->assertTrue($ok);
        $row = $crud->readOne(['id' => 1]);
        $this->assertSame('Alice Cooper', $row['name'] ?? null);

        // Create multiple
        $ok = $crud->createMultiple([
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Cara'],
        ]);
        $this->assertTrue($ok);

        // Read multiple by criteria
        $rows = $crud->readMultipleBy(new Criteria(['OR' => ['id' => [1,2,3]]]));
        $this->assertCount(3, $rows);

        // Delete
        $ok = $crud->delete(new Criteria(['id' => [1,2,3]]));
        $this->assertTrue($ok);
        $this->assertSame([], $crud->readMultipleBy(new Criteria(['id' => [1,2,3]])));
    }
}
