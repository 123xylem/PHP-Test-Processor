<?php
// src/ApiRouter.php

class ApiRouter
{
  private $routes = [];

  public function addRoute($method, $path, $handler)
  {
    $this->routes[] = [
      'method' => $method,
      'path' => $path,
      'handler' => $handler
    ];
  }

  public function handleRequest()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Remove base path if needed
    $basePath = '/event-analytics/public/api';
    if (strpos($path, $basePath) === 0) {
      $path = substr($path, strlen($basePath));
    }

    // Default to / if empty
    if (empty($path)) {
      $path = '/';
    }

    foreach ($this->routes as $route) {
      // Simple string matching for now
      if ($route['method'] === $method && $route['path'] === $path) {
        return call_user_func($route['handler']);
      }
    }

    // No route found
    header('HTTP/1.1 404 Not Found');
    return ['error' => 'Route not found'];
  }

  public function run()
  {
    $result = $this->handleRequest();
    header('Content-Type: application/json');
    echo json_encode($result);
  }
}
