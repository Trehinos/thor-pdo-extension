<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Database\PdoExtension\Criteria;

final class CriteriaTest extends TestCase
{
    public function testSimpleEquality(): void
    {
        $c = new Criteria(['id' => 1]);
        $this->assertSame('WHERE id = ?', Criteria::getWhere($c));
        $this->assertSame([1], $c->getParams());
    }

    public function testInAndNull(): void
    {
        $c = new Criteria([
            'OR' => [
                'id' => [1, 2, 3],
                'deleted_at' => '!', // IS NOT NULL
            ],
        ]);
        $this->assertSame(
            'WHERE ("id" IN (?,?,?) OR deleted_at IS NOT NULL)',
            Criteria::getWhere($c)
        );
        $this->assertSame([1,2,3], $c->getParams());
    }

    public function testLikeOperators(): void
    {
        $c = new Criteria([
            'name' => '%foo%', // contains
            'alt'  => '%*bar', // ends with
            'alt2' => '*%baz', // starts with
        ]);
        $sql = $c->getSql();
        $this->assertStringContainsString('name LIKE ?', $sql);
        $this->assertStringContainsString('alt LIKE ?', $sql);
        $this->assertStringContainsString('alt2 LIKE ?', $sql);
        $this->assertSame(['%foo%','%bar','baz%'], $c->getParams());
    }
}
