<?php

namespace Tests\Unit;

use App\Database\PDOConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    protected PDOQueryBuilder $queryBuilder;

    public function setUp(): void
    {
        $credentials = array_merge(
            Config::get('database', 'pdo'),
            ['db_name' => 'bug_app_test']
        );
        $pdo = new PDOConnection($credentials);
        $this->queryBuilder = new PDOQueryBuilder(
            $pdo->connect()
        );
        parent::setUp();
    }

    public function testItCanCreateRecords(): void
    {
        $data = [
            'report_type' => 'Type 1',
            'message' => 'Example message',
            'link' => 'https://example.com',
            'email' => 'example@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $id = $this->queryBuilder->table('reports')->create($data);
        self::assertNotNull($id);
    }

    public function testItCanPerformRawQuery(): void
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