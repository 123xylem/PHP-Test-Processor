<?php
// src/EventRepository.php

require_once 'Database.php';

class EventRepository
{
  private $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  public function saveEvent($eventData)
  {
    $sql = "INSERT INTO events (id, name, date, venue) 
            VALUES (:id, :name, :date, :venue)
            ON DUPLICATE KEY UPDATE 
            name = :update_name, date = :update_date, venue = :update_venue;";

    $params = [
      ':id' => $eventData['event_id'],
      ':name' => $eventData['name'],
      ':date' => $eventData['date'],
      ':venue' => $eventData['venue'],
      ':update_name' => $eventData['name'],
      ':update_date' => $eventData['date'],
      ':update_venue' => $eventData['venue']
    ];

    $this->db->query($sql, $params);
    return $eventData['event_id'];
  }

  public function saveSignals($eventId, $signals)
  {
    $count = 0;
    var_dump($signals, 'signals');
    foreach ($signals as $signal) {
      $sql = "INSERT INTO signals (id, event_id, timestamp, device_id, signal_strength, zone, x, y) 
                    VALUES (:id, :event_id, :timestamp, :device_id, :signal_strength, :zone, :x, :y)
                    ON DUPLICATE KEY UPDATE 
                    timestamp = :update_timestamp, signal_strength = :update_signal_strength, zone = :update_zone, x = :update_x, y = :update_y";

      $params = [
        ':id' => $signal['id'],
        ':event_id' => $eventId,
        ':timestamp' => $signal['timestamp'],
        ':device_id' => $signal['device_id'],
        ':signal_strength' => $signal['signal_strength'],
        ':zone' => $signal['zone'],
        ':x' => $signal['x'],
        ':y' => $signal['y'],
        ':update_timestamp' => $signal['timestamp'],
        ':update_signal_strength' => $signal['signal_strength'],
        ':update_zone' => $signal['zone'],
        ':update_x' => $signal['x'],
        ':update_y' => $signal['y']
      ];

      $this->db->query($sql, $params);
      $count++;
    }

    return $count;
  }

  public function saveAnalytics($eventId, $reportType, $reportData)
  {
    $sql = "INSERT INTO analytics (event_id, report_type, report_data, generated_at) 
                VALUES (:event_id, :report_type, :report_data, :generated_at)";

    $params = [
      ':event_id' => $eventId,
      ':report_type' => $reportType,
      ':report_data' => json_encode($reportData),
      ':generated_at' => date('Y-m-d H:i:s')
    ];

    $this->db->query($sql, $params);
    return $this->db->lastInsertId();
  }

  public function getEvent($eventId)
  {
    $sql = "SELECT * FROM events WHERE id = :id";
    return $this->db->fetch($sql, [':id' => $eventId]);
  }

  public function getAllEvents()
  {
    $sql = "SELECT * FROM events";
    return $this->db->fetch($sql);
  }

  public function getEventSignals($eventId)
  {
    $sql = "SELECT * FROM signals WHERE event_id = :event_id";
    return $this->db->fetchAll($sql, [':event_id' => $eventId]);
  }

  public function getLatestAnalytics($eventId, $reportType)
  {
    $sql = "SELECT * FROM analytics 
                WHERE event_id = :event_id AND report_type = :report_type 
                ORDER BY generated_at DESC LIMIT 1";

    $params = [
      ':event_id' => $eventId,
      ':report_type' => $reportType
    ];

    return $this->db->fetch($sql, $params);
  }
}
