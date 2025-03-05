<?php
// public/api_client.php

function callApi($endpoint)
{
  $baseUrl = 'http://localhost/event-analytics/public/api';
  $url = $baseUrl . $endpoint;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  return json_decode($response, true);
}

// Get event ID from query string
$eventId = $_GET['event_id'] ?? 'EVT001';

// Get API data
$eventData = callApi("/event/{$eventId}");
$analyticsData = callApi("/event/{$eventId}/analytics");

?>
<!DOCTYPE html>
<html>

<head>
  <title>Event Analytics API Client</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .card {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .error {
      color: red;
    }

    pre {
      background: #f5f5f5;
      padding: 10px;
      border-radius: 4px;
      overflow: auto;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Event Analytics API Client</h1>

    <?php if (isset($eventData['error'])): ?>
      <div class="card error">
        <h2>Error</h2>
        <p><?php echo $eventData['error']; ?></p>
      </div>
    <?php else:  var_dump($eventData); ?>
      <div class="card">
        <h2>Event Information</h2>
        <p><strong>ID:</strong> <?php echo $eventData['event']['id']; ?></p>
        <p><strong>Name:</strong> <?php echo $eventData['event']['name']; ?></p>
        <p><strong>Date:</strong> <?php echo $eventData['event']['date']; ?></p>
        <p><strong>Venue:</strong> <?php echo $eventData['event']['venue']; ?></p>
      </div>

      <?php if (!isset($analyticsData['error'])): ?>
        <div class="card">
          <h2>Analytics Report</h2>
          <p><strong>Generated:</strong> <?php echo $analyticsData['generated_at'] ?? 'Just now'; ?></p>

          <?php if (isset($analyticsData['data']['zone_distribution'])): ?>
            <h3>Zone Distribution</h3>
            <ul>
              <?php foreach ($analyticsData['data']['zone_distribution'] as $zone => $data): ?>
                <li><?php echo $zone; ?>: <?php echo $data['count']; ?> signals (<?php echo $data['percentage']; ?>%)</li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <?php if (isset($analyticsData['data']['hourly_activity'])): ?>
            <h3>Hourly Activity</h3>
            <ul>
              <?php foreach ($analyticsData['data']['hourly_activity'] as $hour => $count): ?>
                <li><?php echo $hour; ?>:00 - <?php echo $count; ?> signals</li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <h3>Raw Data</h3>
          <pre><?php echo json_encode($analyticsData['data'], JSON_PRETTY_PRINT); ?></pre>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>

</html>