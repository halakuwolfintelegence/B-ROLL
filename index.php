<?php
// index.php - Main Video Engine Page
require_once 'config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userCredits = 0;
$username = '';

if ($isLoggedIn) {
    $userCredits = getUserCredits($_SESSION['user_id']);
    $user = getUserByEmail($_SESSION['email']);
    $username = $user['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="google-site-verification" content="1JqJl6ZluFSjwzGu8DJlZh7xP6klEzwwdQGa4-BzMs8" />
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
                    </div>
                    <span class="text-sm text-slate-400">👤 <?php echo htmlspecialchars($username); ?></span>
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
                        🛡️ Safe parsing enabled. Rate-limiting overrides active to protect API connections
                                                       (1 SEARCH = 1 CREDIT
                                      SEARCH CAN BE A WORD, PARAGRAPH, PAGE OR ENTIRE SCRIPT 
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

    <style>
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
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
    </style>

    <script>
        // Modal Functions
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        // Close modal on outside click
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
            const errorEl = document.getElementById('loginError');
            
            errorEl.classList.add('hidden');
            
            try {
                const res = await fetch('login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
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
                    successEl.textContent = '✅ Account created! Redirecting...';
                    successEl.classList.remove('hidden');
                    setTimeout(() => location.reload(), 1500);
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
        // Pass PHP config to JavaScript
        const API_CONFIG = {
            PEXELS_API_KEY: "<?php echo addslashes($config['pexels_api_key']); ?>",
            PIXABAY_API_KEY: "<?php echo addslashes($config['pixabay_api_key']); ?>"
        };
        const USER_ID = <?php echo $_SESSION['user_id']; ?>;
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
            { tags: ['code', 'work', 'office', 'software', 'ai'], url: 'https://assets.mixkit.co/videos/preview/mixkit-coding-on-a-computer-screen-with-a-neon-light-42217-large.mp4', thumb: 'https://images.pexels.com/photos/546814/pexels-photo-546814.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Coverr Core Video' },
            { tags: ['business', 'team', 'meeting', 'work'], url: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-man-typing-on-a-laptop-4173-large.mp4', thumb: 'https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Storyblocks Video Node' },
            { tags: ['ocean', 'water', 'beach', 'waves', 'nature'], url: 'https://assets.mixkit.co/videos/preview/mixkit-waves-coming-to-the-beach-5016-large.mp4', thumb: 'https://images.pexels.com/photos/1001682/pexels-photo-1001682.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Envato Elements Video' },
            { tags: ['space', 'stars', 'galaxy', 'universe', 'future'], url: 'https://assets.mixkit.co/videos/preview/mixkit-stars-in-space-1610-large.mp4', thumb: 'https://images.pexels.com/photos/116975/pexels-photo-116975.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Videezy Pro Video' },
            { tags: ['cyberpunk', 'neon', 'city', 'night'], url: 'https://assets.mixkit.co/videos/preview/mixkit-neon-light-from-a-building-signage-at-night-42220-large.mp4', thumb: 'https://images.pexels.com/photos/2387873/pexels-photo-2387873.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Cyber Matrix Node' },
            { tags: ['nature', 'calm', 'morning', 'water'], url: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-filtering-through-trees-near-a-river-43034-large.mp4', thumb: 'https://images.pexels.com/photos/1424971/pexels-photo-1424971.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Vidsplay HD Node' },
            { tags: ['ai', 'tech', 'code', 'data'], url: 'https://assets.mixkit.co/videos/preview/mixkit-abstract-laser-lights-background-41855-large.mp4', thumb: 'https://images.pexels.com/photos/2582937/pexels-photo-2582937.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'MotionArray Core' }
        ];

        document.getElementById('processBtn').addEventListener('click', async () => {
            <?php if (!$isLoggedIn): ?>
                alert('Please login or register first to generate videos!');
                openModal('loginModal');
                return;
            <?php endif; ?>

            const scriptText = document.getElementById('scriptInput').value.trim();
            if (!scriptText) { alert('Please enter your script parameters into the workspace.'); return; }

            // Check credits
            const credits = parseInt(document.getElementById('creditDisplay').textContent);
            if (credits < 1) {
                alert('⚠️ You have 0 credits! Please buy more credits to continue.');
                openModal('buyCreditsModal');
                return;
            }

            const container = document.getElementById('resultsContainer');
            const loader = document.getElementById('loader');
            const loaderText = document.getElementById('loaderText');
            const status = document.getElementById('statusBadge');
            
            container.innerHTML = '';
            loader.classList.remove('hidden');
            status.classList.remove('hidden');
            status.textContent = 'Mapping Scenes...';

            // Deduct credit
            try {
                await fetch('deduct_credit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: USER_ID })
                });
                // Update credit display
                const newCredits = credits - 1;
                document.getElementById('creditDisplay').textContent = newCredits;
                document.getElementById('creditWarning').textContent = newCredits;
            } catch (e) {
                console.error('Credit deduction failed');
            }

            const lines = scriptText.split(/\n+|(?<=[.!?])\s+(?=[A-Z])/).map(l => l.trim()).filter(l => l.length > 5);

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                loaderText.textContent = `AI Processing Sequence #${i+1}...`;
                
                const keywords = extractKeywords(line);
                const displayKeywords = keywords.length > 0 ? keywords : ['abstract', 'motion'];

                const sceneElement = document.createElement('div');
                sceneElement.className = "bg-slate-900/40 p-5 rounded-2xl border border-slate-800/50 space-y-4 shadow-sm animate-fade-in";
                sceneElement.innerHTML = `
                    <div class="flex flex-wrap justify-between items-start gap-3 border-b border-slate-800/60 pb-3">
                        <p class="text-xs sm:text-sm font-medium text-slate-300 max-w-xl leading-relaxed">
                            <span class="text-cyan-400 font-bold font-mono mr-1">Scene #${i+1}</span> "${line}"
                        </p>
                        <div class="flex gap-1 text-[10px] bg-slate-950 px-2.5 py-0.5 rounded-md text-cyan-400 font-mono tracking-wider border border-cyan-900/30 uppercase font-bold">
                            Tags: ${displayKeywords.join(', ')}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 video-grid-target">
                        <div class="col-span-full text-center py-4 text-xs text-slate-600 animate-pulse font-light tracking-wide">Querying remote stock assets...</div>
                    </div>
                `;
                container.appendChild(sceneElement);

                await dispatchMediaRequests(displayKeywords, sceneElement.querySelector('.video-grid-target'));
                
                if (i < lines.length - 1) {
                    await new Promise(resolve => setTimeout(resolve, 1200));
                }
            }

            loader.classList.add('hidden');
            status.textContent = `Completed (${lines.length} Sequences Mixed)`;
        });

        function extractKeywords(text) {
            const stopWords = new Set(['i','me','my','the','and','but','if','or','because','as','until','while','of','at','by','for','with','about','in','on','at','under','underneath','this','that','these','those','then']);
            return text.toLowerCase().replace(/[^a-zA-Z\s]/g, '').split(/\s+/).filter(word => word.length > 2 && !stopWords.has(word)).slice(0, 3);
        }

        async function dispatchMediaRequests(keywords, gridTarget) {
            const query = encodeURIComponent(keywords.join(' '));
            let collectedPool = [];
            let seenUrls = new Set();

            if (API_CONFIG.PEXELS_API_KEY && API_CONFIG.PEXELS_API_KEY !== "YOUR_PEXELS_API_KEY_HERE") {
                try {
                    const res = await fetch(`https://api.pexels.com/videos/search?query=${query}&per_page=8&orientation=landscape`, { 
                        headers: { Authorization: API_CONFIG.PEXELS_API_KEY }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.videos) {
                            data.videos.forEach(v => {
                                const file = v.video_files.find(f => f.quality === 'hd' || f.width >= 1280) || v.video_files[0];
                                if (file && !seenUrls.has(file.link)) {
                                    seenUrls.add(file.link);
                                    collectedPool.push({ source: 'Pexels Video', videoUrl: file.link, previewImg: v.image });
                                }
                            });
                        }
                    }
                } catch (e) { console.error("Pexels lookup fault", e); }
            }

            if (API_CONFIG.PIXABAY_API_KEY && API_CONFIG.PIXABAY_API_KEY !== "YOUR_PIXABAY_API_KEY_HERE" && collectedPool.length < 6) {
                try {
                    const res = await fetch(`https://pixabay.com/api/videos/?key=${API_CONFIG.PIXABAY_API_KEY}&q=${query}&per_page=8&orientation=landscape`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.hits) {
                            data.hits.forEach(v => {
                                const file = v.videos.medium || v.videos.small;
                                if (file && !seenUrls.has(file.url)) {
                                    seenUrls.add(file.url);
                                    collectedPool.push({ source: 'Pixabay Video', videoUrl: file.url, previewImg: `https://i.vimeocdn.com/video/${v.picture_id}_640x360.jpg` });
                                }
                            });
                        }
                    }
                } catch (e) { console.error("Pixabay lookup fault", e); }
            }

            if (collectedPool.length < 6) {
                let matchedFallbacks = premiumVideoPool.filter(item => item.tags.some(tag => keywords.includes(tag)));
                let allFallbacks = [...premiumVideoPool].sort(() => 0.5 - Math.random());
                let combinedFallbacks = [...matchedFallbacks, ...allFallbacks];

                for (let item of combinedFallbacks) {
                    if (collectedPool.length >= 6) break;
                    if (!seenUrls.has(item.url)) {
                        seenUrls.add(item.url);
                        collectedPool.push({ source: item.engine, videoUrl: item.url, previewImg: item.thumb });
                    }
                }
            }

            collectedPool = collectedPool.slice(0, 6);

            gridTarget.innerHTML = '';
            collectedPool.forEach((vid) => {
                const card = document.createElement('div');
                card.className = "relative group aspect-video bg-slate-950 rounded-xl overflow-hidden border border-slate-800/40 video-card shadow-lg transition-all duration-300 hover:border-cyan-500/30 hover:shadow-[0_4px_20px_rgba(6,182,212,0.1)]";
                card.innerHTML = `
                    <img src="${vid.previewImg}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.src='https://images.pexels.com/photos/3165335/pexels-photo-3165335.jpeg?auto=compress&cs=tinysrgb&w=300'">
                    <div class="absolute inset-0 bg-slate-950/90 opacity-0 video-overlay transition-opacity duration-200 flex flex-col justify-between p-3.5">
                        <span class="self-start text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded bg-slate-900 border border-slate-800/80 text-cyan-400">
                            ${vid.source}
                        </span>
                        <div class="space-y-2">
                            <a href="${vid.videoUrl}" target="_blank" download class="w-full text-center bg-gradient-to-r from-cyan-400 to-indigo-500 hover:opacity-95 text-slate-950 text-xs font-black py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-1.5 tracking-wider uppercase cursor-pointer shadow-md">
                                📥 Download Video
                            </a>
                            <a href="${vid.videoUrl}" target="_blank" class="block text-center bg-slate-900/80 hover:bg-slate-800 text-slate-400 text-[10px] py-1.5 px-2 rounded-lg border border-slate-800/60 transition-colors tracking-wide">
                                Preview Video
                            </a>
                        </div>
                    </div>
                `;
                gridTarget.appendChild(card);
            });
        }
    </script>
</body>
</html>
