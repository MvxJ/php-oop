<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Database\QueryBuilder;
use App\Entity\BugReport;
use App\Helpers\DbQueryBuilderFactory;
use App\Helpers\HttpClient;
use App\Repository\BugReportRepository;
use PHPUnit\Framework\TestCase;

class CrudTest extends TestCase
{
    protected BugReportRepository $bugReportRepository;
    protected QueryBuilder $queryBuilder;
    protected HttpClient $httpClient;

    public function setUp(): void
    {
        $this->httpClient = new HttpClient();
        $this->queryBuilder = DbQueryBuilderFactory::make();
        $this->bugReportRepository = new BugReportRepository($this->queryBuilder);
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testItCanCreateReportUsingPostRequest(): BugReport
    {
        $postData = $this->getPostData(['add' => true]);
        $response = \json_decode(
            $this->httpClient->post('http://localhost:8000/src/add.php', $postData),
            true
        );

        self::assertEquals(200, $response['statusCode']);

        $results = $this->bugReportRepository->findBy(
            [
                'report_type' => 'Audio Issue',
                'email' => 'example@example.com'
            ]
        );

        /** @var BugReport $bugReport */
        $bugReport = $results[0] ?? [];

        self::assertInstanceOf(BugReport::class, $bugReport);
        self::assertSame('Audio Issue', $bugReport->getReportType());
        self::assertSame('http://example.com', $bugReport->getLink());

        return $bugReport;
    }

    /**
     * @depends testItCanCreateReportUsingPostRequest
     */
    public function testItCanUpdateReportUsingPostRequest(BugReport $bugReport): BugReport
    {
        $postData = $this->getPostData(
            [
                'update' => true,
                'email' => 'test@example.com',
                'link' => 'http://test.com',
                'message' => 'Example message',
                'report_type' => 'Audio Issue',
                'id' => $bugReport->getId()
            ]
        );
        $response = \json_decode(
            $this->httpClient->post('http://localhost:8000/src/update.php', $postData),
            true
        );

        self::assertEquals(200, $response['statusCode']);

        $result = $this->bugReportRepository->find($bugReport->getId());

        self::assertInstanceOf(BugReport::class, $result);
        self::assertSame('Audio Issue', $result->getReportType());
        self::assertSame('http://test.com', $result->getLink());
        self::assertSame('test@example.com', $result->getEmail());

        return $result;
    }

    /**
     * @depends testItCanUpdateReportUsingPostRequest
     */
    public function testItCanDeleteReportUsingPostRequest(BugReport $bugReport): void
    {
        $postData = [
            'delete' => true,
            'id' => $bugReport->getId()
        ];

        $response = \json_decode(
            $this->httpClient->post('http://localhost:8000/src/delete.php', $postData),
            true
        );

        self::assertEquals(200, $response['statusCode']);

        $result = $this->bugReportRepository->find($bugReport->getId());

        self::assertNull($result);
    }

    private function getPostData(array $options = []): array
    {
        return array_merge(
            [
                'email' => 'example@example.com',
                'link' => 'http://example.com',
                'message' => 'Example message',
                'report_type' => 'Audio Issue'
            ],
            $options
        );
    }
}