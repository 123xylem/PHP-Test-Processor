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
    // Start clean - no output before headers
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Log instead of var_dump for debugging
    error_log("Original path: " . $path);

    // Remove base path if needed
    $basePath = '/event-analytics/public/api';
    if (strpos($path, $basePath) === 0) {
      $path = substr($path, strlen($basePath));
    }

    // Default to / if empty
    if (empty($path)) {
      $path = '/';
    }

    // Remove trailing slash for consistency (except for root path)
    if ($path !== '/' && substr($path, -1) === '/') {
      $path = rtrim($path, '/');
    }

    error_log("Normalized path: " . $path);
    error_log("Looking for route: " . $method . " " . $path);

    // Debug all registered routes
    foreach ($this->routes as $index => $route) {
      error_log("Route {$index}: {$route['method']} {$route['path']}");
    }

    // Find matching route
    foreach ($this->routes as $route) {
      // Check if this is a parameterized route
      if (strpos($route['path'], '{') !== false) {
        $routePattern = preg_replace('/{([^}]+)}/', '([^/]+)', $route['path']);
        $routePattern = '@^' . $routePattern . '$@';

        if ($route['method'] === $method && preg_match($routePattern, $path, $matches)) {
          // Extract parameter names
          preg_match_all('/{([^}]+)}/', $route['path'], $paramNames);

          // Build parameters array
          $params = [];
          foreach ($paramNames[1] as $index => $name) {
            $params[$name] = $matches[$index + 1];
          }

          error_log("Route matched with params: " . json_encode($params));
          return call_user_func($route['handler'], $params);
        }
      } else {
        // Simple string matching for non-parameterized routes
        if ($route['method'] === $method && $route['path'] === $path) {
          error_log("Route matched: " . $route['path']);
          return call_user_func($route['handler'], []);
        }
      }
    }

    // No route found
    error_log("No route found for: " . $method . " " . $path);
    header('HTTP/1.1 404 Not Found');
    return ['error' => 'Route not found', 'path' => $path];
  }

  public function run()
  {
    $result = $this->handleRequest();
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

    echo json_encode($result);
  }
}
