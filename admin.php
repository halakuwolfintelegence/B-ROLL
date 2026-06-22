<?php
// admin.php - Admin Panel with User Management
session_start();

// Admin login check
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password: admin123

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle Admin Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $ADMIN_USERNAME && password_verify($password, $ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $isLoggedIn = true;
    } else {
        $error = 'Invalid credentials!';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ===== CONFIG LOAD =====
require_once 'config.php';

$message = '';
$messageType = '';

// Handle API config save
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_config'])) {
    $pexelsKey = trim($_POST['pexels_api_key'] ?? '');
    $pixabayKey = trim($_POST['pixabay_api_key'] ?? '');
    
    if (saveConfig($pexelsKey, $pixabayKey)) {
        $message = '✅ API keys updated successfully!';
        $messageType = 'success';
        $config['pexels_api_key'] = $pexelsKey;
        $config['pixabay_api_key'] = $pixabayKey;
    } else {
        $message = '❌ Failed to save configuration.';
        $messageType = 'error';
    }
}

// Handle Add Credits
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_credits'])) {
    $userId = (int)$_POST['user_id'];
    $credits = (int)$_POST['credits'];
    
    if ($userId > 0 && $credits > 0) {
        if (updateUserCredits($userId, $credits)) {
            $message = "✅ Added $credits credits to user!";
            $messageType = 'success';
        } else {
            $message = '❌ Failed to add credits';
            $messageType = 'error';
        }
    }
}

// Get all users
$users = [];
if ($isLoggedIn) {
    $stmt = $pdo->query("SELECT id, username, email, credits, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - CYBER ARBAB</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { background-color: #090d16; }
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .input-dark {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(71, 85, 105, 0.4);
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        .input-dark:focus {
            border-color: rgba(34, 211, 238, 0.5);
            outline: none;
        }
        .tab-btn {
            padding: 8px 20px;
            border-radius: 10px;
            border: 1px solid rgba(71, 85, 105, 0.3);
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            background: rgba(34, 211, 238, 0.15);
            border-color: rgba(34, 211, 238, 0.3);
            color: #22d3ee;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="text-slate-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="index.php" class="text-cyan-400 hover:text-cyan-300 text-sm">← Back to Engine</a>
        </div>

        <?php if (!$isLoggedIn): ?>
        <!-- Admin Login -->
        <div class="max-w-md mx-auto glass-panel p-8 rounded-2xl">
            <h1 class="text-2xl font-bold text-center mb-6">🔐 Admin Login</h1>
            <?php if (isset($error)): ?>
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg text-sm"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" class="input-dark w-full px-4 py-3 rounded-xl mb-4" required>
                <input type="password" name="password" placeholder="Password" class="input-dark w-full px-4 py-3 rounded-xl mb-4" required>
                <button type="submit" name="admin_login" class="w-full bg-gradient-to-r from-cyan-400 to-indigo-500 text-slate-950 font-bold py-3 rounded-xl">Login</button>
            </form>
            <p class="text-center text-xs text-slate-500 mt-4">Default: admin / admin123</p>
        </div>
        <?php else: ?>

        <!-- Admin Panel -->
        <div class="glass-panel p-8 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-200">⚙️ Admin Panel</h1>
                    <p class="text-sm text-slate-500">Manage users, credits & API keys</p>
                </div>
                <a href="?logout=1" class="text-red-400 hover:text-red-300 text-sm">🚪 Logout</a>
            </div>

            <!-- Tabs -->
            <div class="flex gap-2 mb-6 border-b border-slate-800/50 pb-4">
                <button class="tab-btn active" onclick="switchTab('users')">👥 Users</button>
                <button class="tab-btn" onclick="switchTab('config')">🔑 API Keys</button>
            </div>

            <!-- Message -->
            <?php if ($message): ?>
                <div class="mb-4 p-3 rounded-lg border <?php echo $messageType === 'success' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' : 'border-red-500/30 bg-red-500/10 text-red-400'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- ===== USERS TAB ===== -->
            <div id="tab-users" class="tab-content active">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-slate-800">
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Username</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4 text-center">Credits</th>
                                <th class="py-3 px-4">Joined</th>
                                <th class="py-3 px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr class="border-b border-slate-800/50 hover:bg-slate-900/30 transition">
                                <td class="py-3 px-4 text-slate-400">#<?php echo $user['id']; ?></td>
                                <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="py-3 px-4 text-slate-400"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-yellow-400 font-bold"><?php echo $user['credits']; ?></span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 text-xs"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="openAddCredits(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" 
                                            class="text-xs bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 px-3 py-1 rounded-full border border-emerald-500/20 transition">
                                        ➕ Add Credits
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">No users yet</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===== CONFIG TAB ===== -->
            <div id="tab-config" class="tab-content">
                <form method="POST" class="space-y-4 max-w-2xl">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">🔑 Pexels API Key</label>
                        <input type="text" name="pexels_api_key" value="<?php echo htmlspecialchars($config['pexels_api_key']); ?>" 
                               class="input-dark w-full px-4 py-3 rounded-xl text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">🔑 Pixabay API Key</label>
                        <input type="text" name="pixabay_api_key" value="<?php echo htmlspecialchars($config['pixabay_api_key']); ?>" 
                               class="input-dark w-full px-4 py-3 rounded-xl text-sm font-mono">
                    </div>
                    <button type="submit" name="save_config" class="bg-gradient-to-r from-cyan-400 to-indigo-500 text-slate-950 font-bold px-6 py-3 rounded-xl hover:opacity-90 transition">
                        💾 Save API Keys
                    </button>
                </form>
            </div>

            <!-- Add Credits Modal -->
            <div id="creditModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
                <div class="glass-panel p-6 rounded-2xl max-w-md w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-slate-200">➕ Add Credits</h3>
                        <button onclick="closeCreditModal()" class="text-slate-400 hover:text-white text-xl">✕</button>
                    </div>
                    <p class="text-sm text-slate-400 mb-4">User: <span id="creditUsername" class="text-cyan-400"></span></p>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="user_id" id="creditUserId">
                        <div>
                            <label class="block text-sm text-slate-300 mb-1">Number of Credits</label>
                            <input type="number" name="credits" id="creditAmount" class="input-dark w-full px-4 py-3 rounded-xl" min="1" value="10" required>
                        </div>
                        <button type="submit" name="add_credits" class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 font-bold py-3 rounded-xl hover:opacity-90 transition">
                            ✅ Add Credits
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
        }

        function openAddCredits(userId, username) {
            document.getElementById('creditUserId').value = userId;
            document.getElementById('creditUsername').textContent = username;
            document.getElementById('creditModal').classList.remove('hidden');
            document.getElementById('creditModal').style.display = 'flex';
        }

        function closeCreditModal() {
            document.getElementById('creditModal').style.display = 'none';
        }

        // Close modal on outside click
        document.getElementById('creditModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</body>
</html>
