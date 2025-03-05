<?php
// public/api/index.php\
require_once __DIR__ . '/../../src/bootstrap.php';

$router = new ApiRouter();

$router->addRoute('GET', '', function () {
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

$router->addRoute('GET', '/events', function () {
    $repository = new EventRepository();
    $events = $repository->getAllEvents();
    ob_start();
    var_dump($events);
    $output = ob_get_clean();
    error_log($output);
    return ['events' => $events];
});

// GET /events/{id}
$router->addRoute('GET', '/events/{id}', function ($params) {
    if (!isset($params['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $params['id'];
    $repository = new EventRepository();
    $event = $repository->getEvent($eventId);

    if (!$event) {
        header('HTTP/1.1 404 Not Found');
        return ['error' => 'Event not found'];
    }

    return ['event' => $event];
});

// POST /events/{id}
$router->addRoute('POST', '/events', function ($params) {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata, true);
    if (!isset($request['event_id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $request['event_id'];
    $repository = new EventRepository();
    $event = $repository->saveEvent($request);

    if (!$event) {
        header('HTTP/1.1 404 Not Found');
        return ['error' => 'Event not found'];
    }

    return ['event' => $event];
});



// GET /events/{id}/signals
$router->addRoute('GET', '/events/{id}/signals', function ($params) {
    if (!isset($params['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $params['id'];
    $repository = new EventRepository();
    $signals = $repository->getEventSignals($eventId);

    return [
        'event_id' => $eventId,
        'signal_count' => count($signals),
        'signals' => $signals
    ];
});

// GET /events/{id}/analytics
$router->addRoute('GET', '/events/{id}/analytics', function ($params) {
    if (!isset($params['id'])) {
        header('HTTP/1.1 400 Bad Request');
        return ['error' => 'Event ID is required'];
    }

    $eventId = $params['id'];
    $reportType = $params['type'] ?? 'full_report';

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
