<?php
// src/Logger.php

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

class AppLogger
{
  private static $loggers = [];

  /**
   * Get a logger instance
   * 
   * @param string $channel The logging channel name
   * @return \Monolog\Logger
   */
  public static function getLogger($channel = 'app')
  {
    if (!isset(self::$loggers[$channel])) {
      self::$loggers[$channel] = self::createLogger($channel);
    }

    return self::$loggers[$channel];
  }

  /**
   * Create a new logger instance
   * 
   * @param string $channel The logging channel name
   * @return \Monolog\Logger
   */
  private static function createLogger($channel)
  {
    $logger = new Logger($channel);

    // Create logs directory if it doesn't exist
    $logsDir = __DIR__ . '/../logs';
    if (!is_dir($logsDir)) {
      mkdir($logsDir, 0755, true);
    }

    // Add processors for extra information
    $logger->pushProcessor(new IntrospectionProcessor());
    $logger->pushProcessor(new WebProcessor());

    // Create a formatter
    $dateFormat = "Y-m-d H:i:s";
    $output = "[%datetime%] [%level_name%] %channel%: %message% %context% %extra%\n";
    $formatter = new LineFormatter($output, $dateFormat);

    // Daily rotating file handler (keeps 7 days of logs)
    $rotatingHandler = new RotatingFileHandler(
      $logsDir . '/' . $channel . '.log',
      7,
      Logger::DEBUG
    );
    $rotatingHandler->setFormatter($formatter);
    $logger->pushHandler($rotatingHandler);

    // Add a separate handler for errors
    if ($channel === 'app') {
      $errorHandler = new StreamHandler(
        $logsDir . '/error.log',
        Logger::ERROR
      );
      $errorHandler->setFormatter($formatter);
      $logger->pushHandler($errorHandler);
    }

    return $logger;
  }
}
