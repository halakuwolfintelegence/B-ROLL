<?php
// logout.php - Clear all sessions and cookies
session_start();
session_destroy();

// Clear all cookies
setcookie('user_session', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');

header('Location: index.php');
exit;
?>
