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

    $content = file_get_contents($this->dataFile);

    $data = json_decode($content, true);
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
