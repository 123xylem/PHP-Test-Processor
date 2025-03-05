<?php
// public/import_data.php
require_once '../src/bootstrap.php';
// require_once '../src/DataImporter.php';
// require_once '../src/AnalyticsProcessor.php';
// require_once '../src/EventRepository.php';

// Import data
$importer = new DataImporter('../data/sample_data.json');
$importer->import();
$eventInfo = $importer->getEventInfo();

$signals = $importer->getSignalData();

// Save to database
$repository = new EventRepository();

// Save event
$eventId = $repository->saveEvent($eventInfo);

echo "<h1>Event Imported</h1>";
echo "<p>Event ID: {$eventId}</p>";

// Save signals
$signalCount = $repository->saveSignals($eventId, $signals);
echo "<p>Imported {$signalCount} signals</p>";

// Generate and save analytics
$analytics = new AnalyticsProcessor($signals, $eventInfo);
$report = $analytics->generateFullReport();

$analyticsId = $repository->saveAnalytics($eventId, 'full_report', $report);
echo "<p>Analytics report generated (ID: {$analyticsId})</p>";

echo "<p><a href='view_report.php?event_id={$eventId}'>View Report</a></p>";
