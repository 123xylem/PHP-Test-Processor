<?php
// public/setup_database.php

require_once '../src/bootstrap.php';

// require_once '../src/Database.php';
try {
  $db = Database::getInstance();
  $pdo = $db->getPdo();

  // Create events table
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id VARCHAR(10) NOT NULL,
            name VARCHAR(255) NOT NULL,
            date DATE NOT NULL,
            venue VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

  // Create signals table
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS signals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id VARCHAR(10) NOT NULL,
            timestamp DATETIME NOT NULL,
            device_id VARCHAR(50) NOT NULL,
            signal_strength INT NOT NULL,
            zone VARCHAR(50) NOT NULL,
            x FLOAT NOT NULL,
            y FLOAT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id)
        )
    ");

  // Create analytics table
  $pdo->exec("
        CREATE TABLE IF NOT EXISTS analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id VARCHAR(10) NOT NULL,
            report_type VARCHAR(50) NOT NULL,
            report_data JSON NOT NULL,
            generated_at DATETIME NOT NULL,
            FOREIGN KEY (event_id) REFERENCES events(id)
        )
    ");

  echo "<h1>Database Setup Complete</h1>";
  echo "<p>The following tables were created:</p>";
  echo "<ul>";
  echo "<li>events</li>";
  echo "<li>signals</li>";
  echo "<li>analytics</li>";
  echo "</ul>";
} catch (Exception $e) {
  echo "<h1>Error</h1>";
  echo "<p>{$e->getMessage()}</p>";
}
