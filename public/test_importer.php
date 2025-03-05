<?php
// public/test_importer.php

require_once '../src/DataImporter.php';

try {
  $importer = new DataImporter('../data/sample_data.json');
  $data = $importer->import();

  echo "<h1>Event Information</h1>";
  $eventInfo = $importer->getEventInfo();
  echo "<pre>";
  print_r($eventInfo);
  echo "</pre>";

  echo "<h1>Signal Data</h1>";
  $signals = $importer->getSignalData();
  echo "<p>Total signals: " . count($signals) . "</p>";
  echo "<pre>";
  print_r(array_slice($signals, 0, 3)); // Show first 3 signals
  echo "</pre>";
} catch (Exception $e) {
  echo "<h1>Error</h1>";
  echo "<p>{$e->getMessage()}</p>";
}
