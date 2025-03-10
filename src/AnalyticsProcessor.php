<?php
// src/AnalyticsProcessor.php

class AnalyticsProcessor
{
  private $signals = [];
  private $eventInfo = [];

  public function __construct(array $signals, array $eventInfo)
  {
    $this->signals = $signals;
    $this->eventInfo = $eventInfo;
  }

  public function calculateZoneDistribution()
  {
    $distribution = [];

    foreach ($this->signals as $signal) {
      $zone = $signal['zone'];

      if (!isset($distribution[$zone])) {
        $distribution[$zone] = 0;
      }

      $distribution[$zone]++;
    }

    // Calculate percentages
    $total = count($this->signals);
    foreach ($distribution as $zone => $count) {
      $distribution[$zone] = [
        'count' => $count,
        'percentage' => round(($count / $total) * 100, 2)
      ];
    }

    return $distribution;
  }

  public function calculateHourlyActivity()
  {
    $hourlyActivity = [];

    foreach ($this->signals as $signal) {
      $timestamp = strtotime($signal['timestamp']);
      $hour = date('H', $timestamp);

      if (!isset($hourlyActivity[$hour])) {
        $hourlyActivity[$hour] = 0;
      }

      $hourlyActivity[$hour]++;
    }

    // Sort by hour
    ksort($hourlyActivity);

    return $hourlyActivity;
  }

  public function calculateDeviceMovements()
  {
    $devicePaths = [];

    foreach ($this->signals as $signal) {
      $deviceId = $signal['device_id'];
      $timestamp = strtotime($signal['timestamp']);
      $zone = $signal['zone'];
      $position = ['x' => $signal['x'], 'y' => $signal['y']];

      if (!isset($devicePaths[$deviceId])) {
        $devicePaths[$deviceId] = [];
      }

      $devicePaths[$deviceId][] = [
        'timestamp' => $timestamp,
        'zone' => $zone,
        'position' => $position
      ];
    }

    // Sort each device's path by timestamp
    foreach ($devicePaths as $deviceId => $path) {
      usort($devicePaths[$deviceId], function ($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
      });
    }

    return $devicePaths;
  }

  public function calculateZoneDensity()
  {
    $zoneAreas = [
      'entrance' => 100,
      'main_hall' => 500,
      'stage_area' => 300,
      'food_court' => 200,
      'exhibition' => 400
    ];

    // Count devices in each zone at the latest timestamp for each device
    $latestPositions = [];
    foreach ($this->signals as $signal) {
      $deviceId = $signal['device_id'];
      $timestamp = strtotime($signal['timestamp']);
      $zone = $signal['zone'];

      if (!isset($latestPositions[$deviceId]) || $timestamp > $latestPositions[$deviceId]['timestamp']) {
        $latestPositions[$deviceId] = [
          'timestamp' => $timestamp,
          'zone' => $zone
        ];
      }
    }

    // Count devices in each zone
    $zoneCounts = [];
    foreach ($latestPositions as $deviceId => $position) {
      $zone = $position['zone'];

      if (!isset($zoneCounts[$zone])) {
        $zoneCounts[$zone] = 0;
      }

      $zoneCounts[$zone]++;
    }

    // Calculate density (devices per square meter)
    $density = [];
    foreach ($zoneCounts as $zone => $count) {
      if (isset($zoneAreas[$zone])) {
        $density[$zone] = [
          'device_count' => $count,
          'area' => $zoneAreas[$zone],
          'density' => round($count / $zoneAreas[$zone], 4)
        ];
      }
    }

    return $density;
  }

  public function generateFullReport()
  {
    return [
      'event_info' => $this->eventInfo,
      'total_signals' => count($this->signals),
      'unique_devices' => count($this->calculateDeviceMovements()),
      'zone_distribution' => $this->calculateZoneDistribution(),
      'hourly_activity' => $this->calculateHourlyActivity(),
      'zone_density' => $this->calculateZoneDensity(),
      'generated_at' => date('Y-m-d H:i:s')
    ];
  }
}
