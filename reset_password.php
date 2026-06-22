<?php
// reset_password.php - Admin password reset to 'admin123'

// Option 1: If using JSON Storage (users.json)
if (file_exists('users.json')) {
    $data = json_decode(file_get_contents('users.json'), true);
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    foreach ($data['users'] as &$user) {
        if ($user['username'] === 'admin' || $user['email'] === 'admin@admin.com') {
            $user['password'] = $newHash;
            file_put_contents('users.json', json_encode($data, JSON_PRETTY_PRINT));
            echo "✅ Password reset successful!<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
            echo "<a href='admin.php'>Go to Admin Panel</a>";
            exit;
        }
    }
    echo "❌ Admin user not found in users.json!";
    exit;
}

// Option 2: If using MySQL Database
require_once 'config.php';

if (isset($pdo)) {
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin' OR email = 'admin@admin.com'");
    
    if ($stmt->execute([$newHash])) {
        echo "✅ Password reset successful!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "<a href='admin.php'>Go to Admin Panel</a>";
    } else {
        echo "❌ Failed to reset password!";
    }
} else {
    echo "❌ No database connection found!";
}
?>
