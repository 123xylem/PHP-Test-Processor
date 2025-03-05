<?php

class ErrorComponent
{
  public static function render($title, $message, $homeLink = true)
  {
    ob_start();
?>
    <div class="error-container">
      <h1><?= htmlspecialchars($title) ?></h1>
      <div class="error-message">
        <p><?= htmlspecialchars($message) ?></p>
      </div>
      <?php if ($homeLink): ?>
        <a href="/" class="home-link">Return to Homepage</a>
      <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
  }
}
