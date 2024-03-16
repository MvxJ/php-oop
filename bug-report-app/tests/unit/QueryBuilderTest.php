<?php

namespace Tests\Unit;

use App\Database\MySQLiConnection;
use App\Database\MySQLiQueryBuilder;
use App\Database\PDOConnection;
use App\Database\PDOQueryBuilder;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use App\Helpers\DbQueryBuilderFactory;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    protected QueryBuilder $queryBuilder;

    public function setUp(): void
    {
        $credentials = array_merge(
            Config::get('database', 'pdo'),
            ['db_name' => 'bug_app_test']
        );
        $this->queryBuilder = DbQueryBuilderFactory::make(
            'database',
            'pdo',
            ['db_name' => 'bug_app_test']
        );
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->queryBuilder->rollback();
        parent::tearDown();
    }

    public function testItCanCreateRecords(): void
    {
        $id = $this->insertIntoTable();
        self::assertNotNull($id);
    }

    public function testItCanPerformRawQuery(): void
    {
        $id = $this->insertIntoTable();
        $result = $this->queryBuilder->raw('SELECT * FROM reports;')->get();
        self::assertNotNull($result);
    }

    public function testItCanPerformSelectQuery(): void
    {
        $id = $this->insertIntoTable();

        $result = $this->queryBuilder
            ->table('reports')
            ->select('*')
            ->where('id', '=', $id)
            ->runQuery()
            ->first();

        self::assertSame($id, (int)$result->id);
    }

    public function testItCanPerformSelectQueryWIthMultipleWhere(): void
    {
        $id = $this->insertIntoTable();
        $result = $this->queryBuilder
            ->table('reports')
            ->select('*')
            ->where('id', '=', $id)
            ->where('report_type', '=', 'Type 1')
            ->runQuery()
            ->first();

        self::assertNotNull($result);
        self::assertSame($id, (int)$result->id);
        self::assertSame('Type 1', $result->report_type);
    }

    public function testItCanFindById(): void
    {
        $id = $this->insertIntoTable();
        $result = $this->queryBuilder->table('reports')->select('*')->find($id);

        self::assertNotNull($result);
        self::assertSame($id, (int)$result->id);
        self::assertSame('Type 1', $result->report_type);
    }

    public function testItCanFindOneByValues(): void
    {
        $id = $this->insertIntoTable();
        $result = $this->queryBuilder->table('reports')->select('*')->findOneBy('report_type', 'Type 1');

        self::assertNotNull($result);
        self::assertSame($id, (int)$result->id);
        self::assertSame('Type 1', $result->report_type);
    }

    public function testItCanUpdateGivenRecord(): void
    {
        $id = $this->insertIntoTable();
        $count = $this->queryBuilder->table('reports')->update(
            ['report_type' => 'Type 1 UPDATED']
        )->where('id', '=', $id)->runQuery()->affected();
        $result = $this->queryBuilder->table('reports')->select('*')->findOneBy('id', $id);

        self::assertEquals(1, $count);
        self::assertNotNull($result);
        self::assertSame($id, (int)$result->id);
        self::assertSame('Type 1 UPDATED', $result->report_type);
    }

    public function testItCanDeleteGivenId(): void
    {
        $id = $this->insertIntoTable();
        $count = $this->queryBuilder->table('reports')->delete()->where('id', '=', $id)->runQuery()->affected();

        self::assertEquals(1, $count);
        $result = $this->queryBuilder->table('reports')->select('*')->find($id);
        self::assertNull($result);
    }

    private function insertIntoTable(): int
    {
        $data = [
            'report_type' => 'Type 1',
            'message' => 'Example message',
            'link' => 'https://example.com',
            'email' => 'example@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->queryBuilder->table('reports')->create($data);
    }
}