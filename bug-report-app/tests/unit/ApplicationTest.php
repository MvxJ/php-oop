<?php

namespace Tests\Unit;

use App\Helpers\App;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testCanGetApplicationInstance(): void
    {
        self::assertInstanceOf(App::class, new App());
    }

    public function testItCanGetBasicApplicationDatasetFromAppClass(): void
    {
        $application = new App();

        self::assertTrue($application->isExecutedFromCommandLine());
        self::assertSame('test', $application->getEnvironment());
        self::assertNotNull($application->getLogPath());
        self::assertInstanceOf(\DateTime::class, $application->getServerTime());
    }
}