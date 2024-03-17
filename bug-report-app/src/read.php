<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repository\BugReportRepository;
use App\Helpers\DbQueryBuilderFactory;

$queryBuilder = DbQueryBuilderFactory::get();
$repository = new BugReportRepository($queryBuilder);

$bugReports = $repository->findAll();

