<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      margin: 40px auto;
      max-width: 800px;
      padding: 0 20px;
      background: #f5f5f5;
    }

    .error-container {
      background: white;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      color: #e74c3c;
      margin-bottom: 20px;
    }

    .error-message {
      color: #666;
      margin-bottom: 30px;
    }

    .home-link {
      display: inline-block;
      padding: 10px 20px;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .home-link:hover {
      background: #2980b9;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <h1>Oops! Something went wrong</h1>
    <div class="error-message">
      <p>We encountered an unexpected error while processing your request.</p>
      <p><?php echo $errorMessage; ?>.</p>
    </div>
    <a href="/" class="home-link">Return to Homepage</a>
  </div>
</body>

</html>