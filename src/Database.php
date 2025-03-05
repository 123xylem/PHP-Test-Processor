<?php
// src/Database.php

// require_once __DIR__ . '/../vendor/autoload.php';

// // Load environment variables
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
// $dotenv->load();

class Database
{
  private static $instance = null;
  private $pdo;

  private function __construct()
  {
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $charset = $_ENV['DB_CHARSET'];

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
      $this->pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
      throw new Exception("Database connection failed: " . $e->getMessage());
    }
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getPdo()
  {
    return $this->pdo;
  }

  public function query($sql, $params = [])
  {

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  public function fetchAll($sql, $params = [])
  {
    return $this->query($sql, $params)->fetchAll();
  }

  public function fetch($sql, $params = [])
  {
    return $this->query($sql, $params)->fetch();
  }

  public function lastInsertId()
  {
    return $this->pdo->lastInsertId();
  }
}
