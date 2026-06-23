<?php
// logout.php - Clear session and cookie
session_start();
session_destroy();

// Clear cookie
setcookie('user_session', '', time() - 3600, '/');

header('Location: index.php');
exit;
?>
