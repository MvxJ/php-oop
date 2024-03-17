<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Helpers\HttpClient;
use PHPUnit\Framework\TestCase;

class HomePageTest extends TestCase
{
    public function testItCanVisitHomePageAndSeeRelevantData(): void
    {
        $client = new HttpClient();
        $response = $client->get('http://localhost:8000');
        $response = json_decode($response, true);

        self::assertSame(200, $response['statusCode']);
        self::assertStringContainsString('Bug Report App', $response['body']);
        self::assertStringContainsString('<h2>Manage <b>Bug Reports</b></h2>', $response['body']);
    }
}