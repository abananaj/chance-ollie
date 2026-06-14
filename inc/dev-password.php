<?php
function custom_password_protect() {
  // Skip if user is logged in as admin
  if (current_user_can('administrator')) return;

  // Check for password cookie
  if (isset($_COOKIE['site_access_granted'])) return;

  // Handle password submission
  if (isset($_POST['site_password']) && $_POST['site_password'] === 'dionysus-5522') {
      // Set cookie for 1 hour (3600 seconds)
      setcookie('site_access_granted', '1', time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
      wp_redirect($_SERVER['REQUEST_URI']);
      exit;
  }

  // Show password form
  ?>
  <!DOCTYPE html>
  <html>
  <head>
      <title>Protected Site</title>
  </head>
  <body>
      <h1>Enter Password to Access</h1>
      <form method="post">
          <input type="password" name="site_password" required>
          <input type="submit" value="Submit">
      </form>
  </body>
  </html>
  <?php
  exit;
}
add_action('template_redirect', 'custom_password_protect');