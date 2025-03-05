<?php
require_once '../src/bootstrap.php';

// require_once '../src/EventRepository.php';
$repository = new EventRepository();
$eventId = $_GET['event_id'] ?? null;

if (!$eventId) {
  echo ErrorComponent::render('Event ID Required', 'Please provide an event ID to view the report.');
  exit;
}

$report = $repository->getLatestAnalytics($eventId, 'full_report');

if (!$report) {
  echo ErrorComponent::render('Event ID Not Found', 'Please provide a valid event ID to view the report.');
  exit;
}

$report_data = json_decode($report['report_data'], true);
$event_info = $report_data['event_info'];
$zone_density = $report_data['zone_density'];
$total_signals = $report_data['total_signals'];
$unique_devices = $report_data['unique_devices'];
$hourly_activity = $report_data['hourly_activity'];
$zone_distribution = $report_data['zone_distribution'];
$generated_at = $report_data['generated_at'];

?>

<h1>Event Report <?php echo $eventId; ?></h1>
<div class="report-section">
  <h2>Event Details</h2>
  <p><strong>Event Name:</strong> <?php echo $event_info['name']; ?></p>
  <p><strong>Date:</strong> <?php echo $event_info['date']; ?></p>
  <p><strong>Venue:</strong> <?php echo $event_info['venue']; ?></p>
  <p><strong>Report Generated:</strong> <?php echo $generated_at; ?></p>
</div>

<div class="report-section">
  <h2>Overview</h2>
  <p><strong>Total Signals:</strong> <?php echo $total_signals; ?></p>
  <p><strong>Unique Devices:</strong> <?php echo $unique_devices; ?></p>
</div>

<div class="report-section">
  <h2>Zone Distribution</h2>
  <ul>
    <?php foreach ($zone_distribution as $zone => $val): ?>
      <li><strong><?php echo $zone; ?>:</strong> <?php echo $val['count']; ?> signals</li>
    <?php endforeach; ?>
  </ul>
</div>

<div class="report-section">
  <h2>Hourly Activity</h2>
  <ul>
    <?php foreach ($hourly_activity as $hour => $count): ?>
      <li><strong>Hour <?php echo $hour; ?>:</strong> <?php echo $count; ?> signals</li>
    <?php endforeach; ?>
  </ul>
</div>

<div class="report-section">
  <h2>Zone Density</h2>
  <ul>
    <?php foreach ($zone_density as $zone => $density): ?>
      <li><strong><?php echo $zone; ?>:</strong> <?php echo $density['density']; ?> signals/m²</li>
    <?php endforeach; ?>
  </ul>
</div>