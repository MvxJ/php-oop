<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\DbQueryBuilderFactory;
use App\Repository\BugReportRepository;
use App\Logger\Logger;
use App\Exception\BadRequestException;

if (isset($_POST, $_POST['update'])) {
    $reportType = $_POST['report_type'];
    $message = $_POST['message'];
    $link = $_POST['link'];
    $email = $_POST['email'];

    $logger = new Logger();

    try {
        $queryBuilder = DbQueryBuilderFactory::get();
        $repository = new BugReportRepository($queryBuilder);
        $report = $repository->find((int)$_POST['id']);

        $report->setReportType($reportType);
        $report->setEmail($email);
        $report->setMessage($message);
        $report->setLink($link);

        $newReport = $repository->update($report);
    } catch (Throwable $exception) {
        $logger->critical($exception->getMessage(), $_POST);

        throw new BadRequestException($exception->getMessage(), [$exception], 400);
    }

    $logger->info(
        'Bug report was updated',
        [
            'id' => $newReport->getId()
        ]
    );

    $bugReports = $repository->findAll();
}