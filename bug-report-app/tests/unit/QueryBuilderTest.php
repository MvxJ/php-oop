<?php

namespace Tests\Unit;

use App\Database\PDOConnection;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    protected QueryBuilder $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder(
            (new PDOConnection(
                Config::get('database', 'pdo'),
                ['db_name' => 'bug_app_test']
            ))->getConnection()
        );
        parent::setUp();
    }

    public function testItCanCreateRecords(): void
    {
        $data = [];
        $id = $this->queryBuilder->table('reports')->create($data);
        self::assertNotNull($id);
    }

    public function testItCAnPerformRawQuery(): void
    {
        $result = $this->queryBuilder->raw('SELECT * FROM reports;');
        self::assertNotNull($result);
    }

    public function testItCanPerformSelectQuery(): void
    {
        $result = $this->queryBuilder
            ->table('reports')
            ->select('*')
            ->where('id', 1)
            ->first();

        self::assertSame(1, (int)$result->id);
    }

    public function testItCanPerformSelectQueryWIthMultipleWhere(): void
    {
        $result = $this->queryBuilder
            ->table('reports')
            ->select('*')
            ->where('id', 1)
            ->where('report_type', '=', 'Report type 1')
            ->first();

        self::assertNotNull($result);
        self::assertSame(1, (int)$result->id);
        self::assertSame('Report type 1', $result->report_type);
    }
}