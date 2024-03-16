<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BugReport;

class BugReportRepository extends Repository
{
    protected static string $table = 'reports';
    protected static string $className = BugReport::class;
}