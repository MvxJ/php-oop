<?php

namespace Tests\Unit;

use App\Database\QueryBuilder;
use App\Helpers\DbQueryBuilderFactory;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    private BugReportRepository $bugReportRepository;
    private QueryBuilder $queryBuilder;
    public function setUp(): void
    {
        $this->queryBuilder = DbQueryBuilderFactory::make(
            'database', 'pdo', ['db_name' => 'bug_app_test']
        );
        $this->queryBuilder->beginTransaction();

        $this->bugReportRepository = new BugReportRepository($this->queryBuilder);
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->queryBuilder->rollback();
        parent::tearDown();
    }

    public function testItCanCreateRecordWithEntity(): void
    {
        $newBugReport = $this->createBugReportEntity();

        self::assertInstanceOf(BugReport::class, $newBugReport);
        self::assertNotNull($newBugReport->getId());
        self::assertEquals('Type 2', $newBugReport->getType());
        self::assertEquals('https://example.com', $newBugReport->getLink());
        self::assertEquals('This is example message', $newBugReport->getMessage());
        self::assertEquals('example@example.com', $newBugReport->getEmail());
    }

    public function testItCanUpdateEntity(): void
    {
        $newBugReport = $this->createBugReportEntity();
        $newBugReport->setMessage('Updated message')
            ->setEmail('new@example.com');

        $updatedBugReport = $this->bugReportRepository->update($newBugReport);

        self::assertEquals('Updated message', $updatedBugReport->getMessage());
        self::assertEquals('new@example.com', $updatedBugReport->getEmail());
        self::assertEquals('Type 2', $updatedBugReport->getType());
        self::assertEquals('https://example.com', $updatedBugReport->getLink());
    }

    public function testItCanFindEntity(): void
    {
        $newBugReport = $this->createBugReportEntity();
        $foundBugReport = $this->bugReportRepository->find($newBugReport->getId());

        self::assertNotNull($foundBugReport);
        self::assertEquals($newBugReport->getId(), $foundBugReport->getId());
    }

    public function testItCanDeleteGivenEntity(): void
    {
        $newBugReport = $this->createBugReportEntity();
        $this->bugReportRepository->delete($newBugReport);

        $foundBugReport = $this->bugReportRepository->find($newBugReport->getId());

        self::assertNull($foundBugReport);
    }

    private function createBugReportEntity(): BugReport
    {
        $bugReport = new BugReport();
        $bugReport->setReportType('Type 2')
            ->setLink('https://example.com')
            ->setMessage('This is example message')
            ->setEmail('example@example.com');

        return $this->bugReportRepository->create($bugReport);
    }
}