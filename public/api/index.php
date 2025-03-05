<?php
// public/api/index.php\
var_dump($_SERVER['REQUEST_URI']);

require_once '../../src/ApiRouter.php';
require_once '../../src/EventRepository.php';
require_once '../../src/AnalyticsProcessor.php';

$router = new ApiRouter();

// GET /
$router->addRoute('GET', '/', function () {
    return [
        'name' => 'Event Analytics API',
        'version' => '1.0.0',
        'endpoints' => [
            '/events' => 'Get all events',
            '/events/{id}' => 'Get event by ID',
            '/events/{id}/signals' => 'Get signals for an event',
            '/events/{id}/analytics' => 'Get analytics for an event'
        ]
    ];
});

// GET /events/{id}
$router->addRoute('GET', '/events', function () {
    $repository = new EventRepository();
    $events = $repository->getAllEvents();
    return ['events' => $events];
});

// GET /events/{id}
$router->addRoute('GET', '/events/{id}', function () {
    if (!isset($_GET['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $_GET['id'];
    $repository = new EventRepository();
    $event = $repository->getEvent($eventId);

    if (!$event) {
        header('HTTP/1.1 404 Not Found');
        return ['error' => 'Event not found'];
    }

    return ['event' => $event];
});

// GET /events/{id}/signals
$router->addRoute('GET', '/events/{id}/signals', function () {
    if (!isset($_GET['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $_GET['id'];
    $repository = new EventRepository();
    $signals = $repository->getEventSignals($eventId);

    return [
        'event_id' => $eventId,
        'signal_count' => count($signals),
        'signals' => $signals
    ];
});

// GET /events/{id}/analytics
$router->addRoute('GET', '/events/{id}/analytics', function () {
    if (!isset($_GET['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $_GET['id'];
    $reportType = $_GET['type'] ?? 'full_report';

    $repository = new EventRepository();
    $analytics = $repository->getLatestAnalytics($eventId, $reportType);

    if (!$analytics) {
        // Generate on-the-fly if not found
        $event = $repository->getEvent($eventId);
        $signals = $repository->getEventSignals($eventId);

        if (!$event || empty($signals)) {
            header('HTTP/1.1 404 Not Found');
            return ['error' => 'Event or signals not found'];
        }

        $processor = new AnalyticsProcessor($signals, $event);
        $report = $processor->generateFullReport();

        $repository->saveAnalytics($eventId, $reportType, $report);

        return [
            'event_id' => $eventId,
            'report_type' => $reportType,
            'generated_now' => true,
            'data' => $report
        ];
    }

    return [
        'event_id' => $eventId,
        'report_type' => $analytics['report_type'],
        'generated_at' => $analytics['generated_at'],
        'data' => json_decode($analytics['report_data'], true)
    ];
});

// Run the router
$router->run();
