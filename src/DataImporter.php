<?php
// src/DataImporter.php

class DataImporter
{
  private $dataFile;
  private $eventData = [];

  public function __construct($dataFile)
  {
    $this->dataFile = $dataFile;
  }

  public function import()
  {
    // if (!file_exists($this->dataFile)) {
    //   throw new Exception("Data file not found: {$this->dataFile}");
    // }

    $content = file_get_contents($this->dataFile);
    $data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new Exception("Invalid JSON data: " . json_last_error_msg());
    }

    $this->eventData = $data;
    return $this->eventData;
  }

  public function getEventInfo()
  {
    return [
      'event_id' => $this->eventData['event_id'] ?? null,
      'name' => $this->eventData['name'] ?? null,
      'date' => $this->eventData['date'] ?? null,
      'venue' => $this->eventData['venue'] ?? null
    ];
  }

  public function getSignalData()
  {
    return $this->eventData['signals'] ?? [];
  }
}
