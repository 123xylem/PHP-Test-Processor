<?php
// public/import_data.php
require_once '../src/bootstrap.php';

if (isset($_GET['import']) && $_GET['import'] === 'customer') {
  $importer = new CustomerImporter('../data/customer_data.json');
  $importer->import();
  $customerInfo = $importer->getCustomerInfo();
  foreach ($customerInfo as $customer) {
    echo "<h1>Customer Imported</h1>";

    echo "<p>Customer ID: {$customer['id']}</p>";
    echo "<p>Customer Name: {$customer['name']}</p>";
    echo "<p>Customer Date Added: {$customer['date_added']}</p>";
    echo "<p>Customer Size: {$customer['size']}</p>";
    echo "<p>Customer Location: {$customer['location']['street']}, {$customer['location']['city']}, {$customer['location']['state']}, {$customer['location']['zip']}</p>";
    $repository = new CustomerRepository();
    $customerId = $repository->saveCustomer($customer);
  }
  return;
}

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
