<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\DbQueryBuilderFactory;
use App\Repository\BugReportRepository;
use App\Logger\Logger;
use App\Exception\BadRequestException;

if (isset($_POST, $_POST['delete'])) {
    $logger = new Logger();

    try {
        $queryBuilder = DbQueryBuilderFactory::get();
        $repository = new BugReportRepository($queryBuilder);
        $report = $repository->find((int)$_POST['id']);

        $repository->delete($report);
    } catch (Throwable $exception) {
        $logger->critical($exception->getMessage(), $_POST);

        throw new BadRequestException($exception->getMessage(), [$exception], 400);
    }

    $logger->info(
        'Bug report was deleted',
        [
            'id' => $report->getId()
        ]
    );

    $bugReports = $repository->findAll();
}