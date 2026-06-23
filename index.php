<?php
// index.php - Main Video Engine Page
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CYBER ARBAB - AI Script-to-Video Engine v3.2</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { background-color: #090d16; }
        .glass-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.6) 0%, rgba(30, 41, 59, 0.4) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .video-card:hover .video-overlay { opacity: 1; }
        .glowing-text { text-shadow: 0 0 20px rgba(34, 211, 238, 0.3); }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(12px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        .animate-fade-in { animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }
        .credit-badge {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(251, 191, 36, 0.05));
            border: 1px solid rgba(251, 191, 36, 0.2);
        }
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            max-width: 450px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
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
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22d3ee, #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #090d16;
            font-size: 14px;
        }
        .tag-badge {
            display: inline-block;
            font-size: 9px;
            background: #0f172a;
            padding: 2px 8px;
            border-radius: 6px;
            color: #22d3ee;
            font-family: monospace;
            border: 1px solid rgba(34, 211, 238, 0.2);
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 2px;
        }
        .tag-more {
            display: inline-block;
            font-size: 9px;
            background: #0f172a;
            padding: 2px 8px;
            border-radius: 6px;
            color: #64748b;
            font-family: monospace;
            border: 1px solid rgba(71, 85, 105, 0.3);
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 2px;
            cursor: help;
        }
    </style>
</head>
<body class="text-slate-100 min-h-screen font-sans antialiased selection:bg-cyan-500 selection:text-slate-900">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[400px] bg-gradient-to-r from-cyan-500/10 via-indigo-500/5 to-fuchsia-500/10 blur-[120px] pointer-events-none rounded-full"></div>
    
    <div class="max-w-7xl mx-auto px-6 py-8 relative z-10">
        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎬</span>
                <span class="text-xs font-bold text-cyan-400 tracking-widest">CYBER ARBAB</span>
            </div>
            <div class="flex items-center gap-3">
                <?php if ($isLoggedIn): ?>
                    <div class="credit-badge px-4 py-2 rounded-full text-xs font-bold text-yellow-400 flex items-center gap-2">
                        ⭐ <span id="creditDisplay"><?php echo $userCredits; ?></span> Credits
                        <button onclick="openModal('buyCreditsModal')" class="text-[10px] bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 px-2 py-0.5 rounded-full transition-colors">
                            ➕ Buy
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                        <span class="text-sm text-slate-300 hidden sm:inline"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <a href="logout.php" class="text-xs text-red-400 hover:text-red-300 transition-colors">Logout</a>
                <?php else: ?>
                    <button onclick="openModal('loginModal')" class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors">Login</button>
                    <button onclick="openModal('registerModal')" class="text-xs bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-400 px-4 py-2 rounded-full transition-colors border border-cyan-500/20">Register</button>
                <?php endif; ?>
            </div>
        </div>

        <header class="mb-12 text-center">
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-950 to-slate-900 border border-cyan-500/30 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest text-cyan-400 mb-6 shadow-[0_0_15px_rgba(6,182,212,0.15)] uppercase">
                <span class="inline-block w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Upgraded by CYBER ARBAB
            </div>
            <h1 class="text-4xl font-black tracking-tight sm:text-6xl bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent mb-4">
                Cinematic Script-to-Video Engine
            </h1>
            <p class="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base font-light leading-relaxed">
                Advanced AI-powered asset pipeline extracting contextual semantic tags to match 100% premium video tracks.
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 space-y-6">
                <div class="glass-panel p-6 rounded-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
                    
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-base font-bold text-slate-200 flex items-center gap-2 tracking-wide uppercase text-xs">
                            🎬 Input Workspace
                        </h2>
                        <span class="text-[10px] font-mono tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-0.5 rounded-md font-semibold">PURE VIDEO NODES ONLINE</span>
                    </div>

                    <textarea id="scriptInput" rows="11" placeholder="Paste your script blocks here..." class="w-full bg-slate-950/70 border border-slate-800 rounded-xl p-4 text-sm focus:outline-none focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all resize-y text-slate-200 placeholder-slate-600 leading-relaxed font-light">
The future of artificial intelligence is transforming everyday work.
Cyberpunk cities are glowing with neon lights under the rain.
Take a deep breath and connect with the calm, morning nature.
                    </textarea>

                    <button id="processBtn" class="mt-5 w-full bg-gradient-to-r from-cyan-400 via-indigo-500 to-fuchsia-500 text-slate-950 font-extrabold py-4 px-4 rounded-xl transition-all shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] flex justify-center items-center gap-2 cursor-pointer tracking-wider text-xs uppercase group">
                        🤖 AI Generate Match Pipeline (6 Unique Videos)
                    </button>
                    
                    <?php if ($isLoggedIn): ?>
                        <div class="mt-3 text-center text-xs text-slate-500">
                            ⚡ <span id="creditWarning" class="text-yellow-400"><?php echo $userCredits; ?></span> credits remaining per generation
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="bg-slate-900/30 p-4 rounded-xl border border-slate-900 text-center">
                    <p class="text-xs text-slate-500 font-light">
                        🛡️ Safe parsing enabled. Rate-limiting overrides active to protect API connections.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                <div class="glass-panel p-6 rounded-2xl shadow-2xl min-h-[515px]">
                    <div class="flex justify-between items-center mb-6 border-b border-slate-800 pb-4">
                        <h2 class="text-lg font-bold text-slate-200 tracking-tight flex items-center gap-2">
                            🎬 AI Production Storyboard
                        </h2>
                        <span id="statusBadge" class="hidden px-3 py-1 bg-cyan-500/10 text-cyan-400 text-xs font-bold rounded-full border border-cyan-500/20 tracking-wide uppercase">Ready</span>
                    </div>

                    <div id="loader" class="hidden flex-col items-center justify-center py-32 space-y-5">
                        <div class="relative w-12 h-12">
                            <div class="absolute inset-0 rounded-full border-2 border-slate-800"></div>
                            <div class="absolute inset-0 rounded-full border-t-2 border-cyan-400 animate-spin"></div>
                        </div>
                        <p id="loaderText" class="text-slate-400 text-xs tracking-widest uppercase font-semibold animate-pulse glowing-text">
                            Analyzing Script Semantics...
                        </p>
                    </div>

                    <div id="resultsContainer" class="space-y-8">
                        <div class="text-center py-28 text-slate-600 flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full border border-dashed border-slate-800 flex items-center justify-center text-slate-500 mb-4 text-lg">📁</div>
                            <p class="text-sm font-medium text-slate-400">Interactive timeline is empty.</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs">Write or modify your script sequence on the left, then trigger AI generation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-16 text-center border-t border-slate-900 pt-8 pb-4">
            <p class="text-xs text-slate-600 tracking-widest uppercase font-medium">
                Automated Media Matching System • Architecture by <span class="text-slate-400 font-bold hover:text-cyan-400 transition-colors">CYBER ARBAB</span>
            </p>
        </footer>
    </div>

    <!-- Admin Link -->
    <a href="admin.php" class="admin-link bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-bold px-4 py-3 rounded-full border border-cyan-500/30 shadow-lg transition-all hover:scale-105 flex items-center gap-2">
        ⚙️ Admin Panel
    </a>

    <!-- ===== LOGIN MODAL ===== -->
    <div id="loginModal" class="modal">
        <div class="modal-content glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
            <button onclick="closeModal('loginModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">✕</button>
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-500/20 to-indigo-500/20 flex items-center justify-center text-3xl mx-auto border border-cyan-500/20">
                    🔐
                </div>
                <h2 class="text-xl font-bold text-slate-200 mt-4">Login</h2>
                <p class="text-xs text-slate-500">Stay logged in forever with persistent cookies</p>
            </div>

            <div id="loginError" class="hidden mb-4 p-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm"></div>

            <form onsubmit="loginUser(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" id="loginEmail" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Enter your email" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" id="loginPassword" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Enter your password" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="rememberMe" checked class="w-4 h-4 accent-cyan-500">
                    <label for="rememberMe" class="text-xs text-slate-400">Remember me forever (1 year)</label>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-cyan-400 to-indigo-500 text-slate-950 font-bold py-3 rounded-xl hover:opacity-90 transition">
                    🚀 Login
                </button>
                <p class="text-center text-xs text-slate-500">
                    Don't have account? <span class="text-cyan-400 cursor-pointer hover:underline" onclick="closeModal('loginModal');openModal('registerModal')">Register</span>
                </p>
            </form>
        </div>
    </div>

    <!-- ===== REGISTER MODAL ===== -->
    <div id="registerModal" class="modal">
        <div class="modal-content glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 via-indigo-500 to-fuchsia-500"></div>
            <button onclick="closeModal('registerModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">✕</button>
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-3xl mx-auto border border-emerald-500/20">
                    📝
                </div>
                <h2 class="text-xl font-bold text-slate-200 mt-4">Create Account</h2>
                <p class="text-xs text-slate-500">Get 10 free credits on signup!</p>
            </div>

            <div id="registerError" class="hidden mb-4 p-3 rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 text-sm"></div>
            <div id="registerSuccess" class="hidden mb-4 p-3 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 text-sm"></div>

            <form onsubmit="registerUser(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Username</label>
                    <input type="text" id="registerUsername" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Choose a username" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" id="registerEmail" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Enter your email" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" id="registerPassword" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Min 6 characters" required minlength="6">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 font-bold py-3 rounded-xl hover:opacity-90 transition">
                    🎉 Register
                </button>
                <p class="text-center text-xs text-slate-500">
                    Already have account? <span class="text-cyan-400 cursor-pointer hover:underline" onclick="closeModal('registerModal');openModal('loginModal')">Login</span>
                </p>
            </form>
        </div>
    </div>

    <!-- ===== BUY CREDITS MODAL ===== -->
    <div id="buyCreditsModal" class="modal">
        <div class="modal-content glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden text-center">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500"></div>
            <button onclick="closeModal('buyCreditsModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">✕</button>
            
            <div class="text-6xl mb-4">💰</div>
            <h2 class="text-2xl font-bold text-slate-200 mb-2">Buy Credits</h2>
            <p class="text-slate-400 text-sm mb-6">Purchase credits to generate more videos</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <div class="text-2xl font-bold text-yellow-400">10</div>
                    <div class="text-xs text-slate-500">Credits</div>
                    <div class="text-sm text-slate-400 mt-1">$5</div>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <div class="text-2xl font-bold text-yellow-400">25</div>
                    <div class="text-xs text-slate-500">Credits</div>
                    <div class="text-sm text-slate-400 mt-1">$10</div>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <div class="text-2xl font-bold text-yellow-400">50</div>
                    <div class="text-xs text-slate-500">Credits</div>
                    <div class="text-sm text-slate-400 mt-1">$15</div>
                </div>
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800">
                    <div class="text-2xl font-bold text-yellow-400">100</div>
                    <div class="text-xs text-slate-500">Credits</div>
                    <div class="text-sm text-slate-400 mt-1">$25</div>
                </div>
            </div>

            <a href="https://wa.me/923291810710?text=I want to buy credits for CYBER ARBAB Video Engine" target="_blank" class="block w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold py-4 rounded-xl hover:opacity-90 transition text-center">
                📱 Buy via WhatsApp
            </a>
            <p class="text-xs text-slate-500 mt-4">Click to chat on WhatsApp • +92 329 1810710</p>
        </div>
    </div>

    <script>
        // ===== MODAL FUNCTIONS =====
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // ===== LOGIN =====
        async function loginUser(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberMe').checked;
            const errorEl = document.getElementById('loginError');
            
            errorEl.classList.add('hidden');
            
            try {
                const res = await fetch('login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, remember })
                });
                const data = await res.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    errorEl.textContent = data.message;
                    errorEl.classList.remove('hidden');
                }
            } catch (e) {
                errorEl.textContent = 'Login failed. Please try again.';
                errorEl.classList.remove('hidden');
            }
        }

        // ===== REGISTER =====
        async function registerUser(e) {
            e.preventDefault();
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const errorEl = document.getElementById('registerError');
            const successEl = document.getElementById('registerSuccess');
            
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');
            
            try {
                const res = await fetch('register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, password })
                });
                const data = await res.json();
                
                if (data.success) {
                    successEl.textContent = '✅ Account created! Please login.';
                    successEl.classList.remove('hidden');
                    setTimeout(() => {
                        closeModal('registerModal');
                        openModal('loginModal');
                    }, 1500);
                } else {
                    errorEl.textContent = data.message;
                    errorEl.classList.remove('hidden');
                }
            } catch (e) {
                errorEl.textContent = 'Registration failed. Please try again.';
                errorEl.classList.remove('hidden');
            }
        }

        // ===== MAIN ENGINE =====
        <?php if ($isLoggedIn): ?>
        const API_CONFIG = {
            PEXELS_API_KEY: "<?php echo addslashes($config['pexels_api_key']); ?>",
            PIXABAY_API_KEY: "<?php echo addslashes($config['pixabay_api_key']); ?>"
        };
        const USER_ID = <?php echo $userId; ?>;
        <?php else: ?>
        const API_CONFIG = {
            PEXELS_API_KEY: "<?php echo addslashes($config['pexels_api_key']); ?>",
            PIXABAY_API_KEY: "<?php echo addslashes($config['pixabay_api_key']); ?>"
        };
        <?php endif; ?>

        // Premium Video Pool
        const premiumVideoPool = [
            { tags: ['ai', 'tech', 'future', 'data', 'work'], url: 'https://assets.mixkit.co/videos/preview/mixkit-man-holding-a-smartphone-with-a-blue-screen-40176-large.mp4', thumb: 'https://images.pexels.com/photos/6153354/pexels-photo-6153354.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Artgrid Stream Node' },
            { tags: ['cyberpunk', 'city', 'night', 'rain', 'neon'], url: 'https://assets.mixkit.co/videos/preview/mixkit-time-lapse-of-a-city-at-night-4158-large.mp4', thumb: 'https://images.pexels.com/photos/1612513/pexels-photo-1612513.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Coverr Premium Video' },
            { tags: ['nature', 'forest', 'calm', 'trees', 'morning'], url: 'https://assets.mixkit.co/videos/preview/mixkit-aerial-view-of-a-dense-forest-2280-large.mp4', thumb: 'https://images.pexels.com/photos/3225517/pexels-photo-3225517.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Mixkit Video Node' },
            { tags: ['code', 'work', 'office',
