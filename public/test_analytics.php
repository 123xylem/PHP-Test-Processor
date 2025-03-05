<?php
// public/test_analytics.php
require_once '../src/bootstrap.php';
// require_once '../src/DataImporter.php';
// require_once '../src/AnalyticsProcessor.php';

try {
  // Import data
  $importer = new DataImporter('../data/sample_data.json');
  $importer->import();
  $eventInfo = $importer->getEventInfo();
  $signals = $importer->getSignalData();

  // Process analytics
  $analytics = new AnalyticsProcessor($signals, $eventInfo);

  echo "<h1>Event Analytics: {$eventInfo['name']}</h1>";

  echo "<h2>Zone Distribution</h2>";
  $distribution = $analytics->calculateZoneDistribution();
  echo "<pre>";
  print_r($distribution);
  echo "</pre>";

  echo "<h2>Hourly Activity</h2>";
  $hourlyActivity = $analytics->calculateHourlyActivity();
  echo "<pre>";
  print_r($hourlyActivity);
  echo "</pre>";

  echo "<h2>Zone Density</h2>";
  $density = $analytics->calculateZoneDensity();
  echo "<pre>";
  print_r($density);
  echo "</pre>";

  echo "<h2>Full Report</h2>";
  $report = $analytics->generateFullReport();
  echo "<pre>";
  print_r($report);
  echo "</pre>";
} catch (Exception $e) {
  echo "<h1>Error</h1>";
  echo "<p>{$e->getMessage()}</p>";
}
