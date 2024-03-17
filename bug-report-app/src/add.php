<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\BugReport;
use App\Helpers\DbQueryBuilderFactory;
use App\Repository\BugReportRepository;
use App\Logger\Logger;
use App\Exception\BadRequestException;

if (isset($_POST, $_POST['add'])) {
    $reportType = $_POST['report_type'];
    $message = $_POST['message'];
    $link = $_POST['link'];
    $email = $_POST['email'];

    $bugReport = new BugReport();
    $bugReport->setReportType($reportType);
    $bugReport->setEmail($email);
    $bugReport->setMessage($message);
    $bugReport->setLink($link);

    $logger = new Logger();

    try {
        $queryBuilder = DbQueryBuilderFactory::get();
        $repository = new BugReportRepository($queryBuilder);
        $newReport = $repository->create($bugReport);
    } catch (Throwable $exception) {
        $logger->critical($exception->getMessage(), $_POST);

        throw new BadRequestException($exception->getMessage(), [$exception], 400);
    }

    $logger->info(
        'New bug report created',
        [
            'id' => $newReport->getId(),
            'type' => $bugReport->getReportType()
        ]
    );

    $bugReports = $repository->findAll();
}