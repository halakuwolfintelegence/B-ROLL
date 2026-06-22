<?php
// admin.php - Admin Panel with Login System
session_start();

// ===== LOGIN CONFIGURATION =====
// Change these credentials
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD = 'cyber123'; // Change karo!

// Check if already logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $isLoggedIn = true;
        $loginMessage = '✅ Login successful!';
        $loginMessageType = 'success';
    } else {
        $loginMessage = '❌ Invalid username or password!';
        $loginMessageType = 'error';
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ===== CONFIG LOAD =====
require_once 'config.php';

$message = '';
$messageType = '';

// Handle form submission (only if logged in)
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_config'])) {
    $pexelsKey = isset($_POST['pexels_api_key']) ? trim($_POST['pexels_api_key']) : '';
    $pixabayKey = isset($_POST['pixabay_api_key']) ? trim($_POST['pixabay_api_key']) : '';
    
    if (saveConfig($pexelsKey, $pixabayKey)) {
        $message = '✅ API keys updated successfully!';
        $messageType = 'success';
        $config['pexels_api_key'] = $pexelsKey;
        $config['pixabay_api_key'] = $pixabayKey;
    } else {
        $message = '❌ Failed to save configuration. Please check file permissions.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - CYBER ARBAB Video Engine</title>
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
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.1);
            outline: none;
        }
        .login-box {
            max-width: 400px;
            margin: 0 auto;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
            background: transparent;
            border: none;
            font-size: 14px;
        }
        .password-toggle:hover {
            color: #94a3b8;
        }
        .input-wrapper {
            position: relative;
        }
    </style>
</head>
<body class="text-slate-100 min-h-screen font-sans antialiased">
    <div class="max-w-4xl mx-auto px-6 py-12">
        
        <!-- Back Button -->
        <div class="mb-8">
            <a href="index.php" class="text-cyan-400 hover:text-cyan-300 text-sm font-medium flex items-center gap-2 transition-colors">
                ← Back to Video Engine
            </a>
        </div>

        <!-- ===== LOGIN FORM ===== -->
        <?php if (!$isLoggedIn): ?>
        <div class="glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden login-box">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
            
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500/20 to-indigo-500/20 flex items-center justify-center text-3xl mx-auto border border-cyan-500/20">
                    🔐
                </div>
                <h1 class="text-2xl font-bold text-slate-200 mt-4">Admin Login</h1>
                <p class="text-sm text-slate-500 mt-1">Enter your credentials to continue</p>
            </div>

            <?php if (isset($loginMessage)): ?>
                <div class="mb-6 p-4 rounded-xl border <?php echo $loginMessageType === 'success' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' : 'border-red-500/30 bg-red-500/10 text-red-400'; ?>">
                    <?php echo $loginMessage; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-300 mb-2">
                        👤 Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="input-dark w-full px-4 py-3 rounded-xl text-sm"
                        placeholder="Enter username"
                        required
                        autofocus
                    />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                        🔑 Password
                    </label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="input-dark w-full px-4 py-3 rounded-xl text-sm pr-12"
                            placeholder="Enter password"
                            required
                        />
                        <button type="button" onclick="togglePassword()" class="password-toggle">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="w-full bg-gradient-to-r from-cyan-400 via-indigo-500 to-fuchsia-500 text-slate-950 font-extrabold py-4 rounded-xl transition-all shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] tracking-wider text-sm uppercase">
                    🚀 Login
                </button>
            </form>

            <div class="mt-6 p-4 bg-slate-900/30 rounded-xl border border-slate-800/50">
                <p class="text-xs text-slate-500 text-center">
                    🔒 Default: <span class="text-cyan-400 font-mono">admin / cyber123</span><br>
                    <span class="text-[10px] text-slate-600">Change credentials in admin.php file</span>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== ADMIN PANEL (Logged In) ===== -->
        <?php if ($isLoggedIn): ?>
        <div class="glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-indigo-500/20 flex items-center justify-center text-2xl border border-cyan-500/20">
                        ⚙️
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-200">Admin Control Panel</h1>
                        <p class="text-sm text-slate-500">Manage API keys and system configuration</p>
                    </div>
                </div>
                <a href="?logout=1" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-colors border border-red-500/20">
                    🚪 Logout
                </a>
            </div>

            <!-- Status Messages -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-xl border <?php echo $messageType === 'success' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' : 'border-red-500/30 bg-red-500/10 text-red-400'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Config Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="pexels_api_key" class="block text-sm font-medium text-slate-300 mb-2">
                        🔑 Pexels API Key
                    </label>
                    <input 
                        type="text" 
                        id="pexels_api_key" 
                        name="pexels_api_key" 
                        value="<?php echo htmlspecialchars($config['pexels_api_key']); ?>" 
                        class="input-dark w-full px-4 py-3 rounded-xl text-sm font-mono"
                        placeholder="Enter your Pexels API key"
                    />
                    <p class="text-xs text-slate-500 mt-2">
                        Get your key from <a href="https://www.pexels.com/api/" target="_blank" class="text-cyan-400 hover:underline">pexels.com/api</a>
                    </p>
                </div>

                <div>
                    <label for="pixabay_api_key" class="block text-sm font-medium text-slate-300 mb-2">
                        🔑 Pixabay API Key
                    </label>
                    <input 
                        type="text" 
                        id="pixabay_api_key" 
                        name="pixabay_api_key" 
                        value="<?php echo htmlspecialchars($config['pixabay_api_key']); ?>" 
                        class="input-dark w-full px-4 py-3 rounded-xl text-sm font-mono"
                        placeholder="Enter your Pixabay API key"
                    />
                    <p class="text-xs text-slate-500 mt-2">
                        Get your key from <a href="https://pixabay.com/api/docs/" target="_blank" class="text-cyan-400 hover:underline">pixabay.com/api/docs</a>
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-800/50 flex gap-4">
                    <button type="submit" name="save_config" class="flex-1 bg-gradient-to-r from-cyan-400 via-indigo-500 to-fuchsia-500 text-slate-950 font-extrabold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] tracking-wider text-sm uppercase">
                        💾 Save Configuration
                    </button>
                    <a href="index.php" class="px-6 py-4 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition-all text-sm font-medium flex items-center gap-2 border border-slate-700/50">
                        🏠 Return to Engine
                    </a>
                </div>
            </form>

            <!-- Security Info -->
            <div class="mt-8 p-4 bg-slate-900/30 rounded-xl border border-slate-800/50">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">🔒 Security Notes</h3>
                <ul class="text-xs text-slate-500 space-y-1 list-disc list-inside">
                    <li>API keys are stored locally in <code class="bg-slate-800 px-1.5 py-0.5 rounded text-cyan-400">config_data.json</code></li>
                    <li>Never share your API keys publicly</li>
                    <li>Keys are used only for client-side requests</li>
                    <li>Session expires when browser is closed</li>
                </ul>
            </div>

            <!-- Session Info -->
            <div class="mt-4 text-right">
                <span class="text-[10px] text-slate-600">
                    Logged in as: <span class="text-cyan-400 font-mono"><?php echo htmlspecialchars($ADMIN_USERNAME); ?></span>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <footer class="mt-12 text-center border-t border-slate-900 pt-6">
            <p class="text-xs text-slate-600 tracking-widest uppercase font-medium">
                Automated Media Matching System • Architecture by <span class="text-slate-400 font-bold">CYBER ARBAB</span>
            </p>
        </footer>
    </div>

    <script>
        // Password Toggle
        function togglePassword() {
            const input = document.getElementById('password');
            const button = document.querySelector('.password-toggle');
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
