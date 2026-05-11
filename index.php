<?php
require_once 'includes/db.php';
session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Fetch dynamic data
$stakeholders = $pdo->query("SELECT * FROM stakeholders")->fetchAll();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

$pendingUsers = [];
if ($isAdmin) {
    $pendingUsers = $pdo->query("SELECT * FROM users WHERE status = 'pending' AND is_admin = 0")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Redbucks Finance | Premium Gateway</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; -webkit-tap-highlight-color: transparent; }
        [x-cloak] { display: none !important; }
        
        /* Responsive scroll track sizes */
        .banner-track { display: flex; width: calc(150px * 12); animation: scroll 40s linear infinite; }
        @media (min-width: 768px) { .banner-track { width: calc(250px * 12); } }
        
        @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(calc(-250px * 6)); } }
        
        #portalModal.active { display: flex !important; }
        body.modal-open { overflow: hidden; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1a325c; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#fcfcfd]" x-data="{ showLogin: false, showRegister: false, adminMode: false }">

    <?php if (!$isLoggedIn): ?>
    <div class="min-h-screen flex flex-col overflow-x-hidden">
        <nav class="w-full flex-shrink-0 px-4 md:px-8 py-4 bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <img src="assets/logos/file_00000000fa58722fbc346086d4f1692b.png" alt="Logo" class="h-12 md:h-20 w-auto object-contain">
                <div class="flex items-center space-x-3 md:space-x-6">
                    <button @click="showLogin = true" class="text-xs md:text-sm font-bold text-slate-600">Staff Portal</button>
                    <button @click="showRegister = true" class="bg-[#1a325c] text-white px-4 py-2 md:px-6 md:py-3 rounded-xl text-xs md:text-sm font-bold shadow-lg">Join</button>
                </div>
            </div>
        </nav>

        <main class="flex-1 flex items-center px-6 py-12 md:py-0">
            <div class="max-w-7xl mx-auto w-full grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-[#1a325c] leading-tight">
                        Your One-Stop Partner for <span class="text-[#e51723]">Business Solutions</span> & Agency Banking.
                    </h1>
                    <p class="text-slate-500 text-base md:text-lg max-w-md mx-auto lg:mx-0">Secure inter-bank settlements and innovative agency tools for the global landscape.</p>
                    <button @click="showRegister = true" class="bg-[#1a325c] text-white px-8 py-4 md:px-10 md:py-5 rounded-2xl font-bold text-lg shadow-2xl">Get Started Now</button>
                </div>
                <div class="relative h-[35vh] md:h-[55vh] w-full">
                    <div class="swiper myHeroSwiper shadow-2xl border-[6px] md:border-[10px] border-white rounded-[2rem]">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="assets\logos\Agency_Banking_Digital_Ecosystems.webp" class="w-full h-full object-cover"></div>
                            <div class="swiper-slide"><img src="assets\logos\agency-banking-in-africa.jpg" class="w-full h-full object-cover"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-slate-100 py-6 overflow-hidden">
            <div class="banner-track">
                <?php foreach(array_merge($stakeholders, $stakeholders) as $s): ?>
                <div class="w-[180px] md:w-[250px] flex flex-col items-center px-6 md:px-10">
                    <img src="<?= htmlspecialchars($s['logo_path'] ?? 'assets/logos/default.png') ?>" class="h-4 md:h-6 w-auto mb-2"> 
                    <span class="text-[6px] md:text-[8px] font-bold text-slate-400 uppercase text-center"><?= htmlspecialchars($s['name']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </footer>
    </div>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
    <div class="flex flex-col md:flex-row h-screen overflow-hidden">
        <aside class="hidden md:flex w-72 bg-[#1a325c] text-white flex-col p-8 flex-shrink-0 shadow-2xl">
            <div class="mb-12">
                <img src="assets/logos/file_00000000fa58722fbc346086d4f1692b.png" class="w-full h-auto object-contain">
            </div>
            <nav class="space-y-4 flex-1">
                <a href="index.php" class="block p-4 rounded-xl bg-white/10 font-bold border border-white/20">Stakeholder Grid</a>
            </nav>
            <?php if ($isAdmin): ?>
            <div class="pt-6 border-t border-white/10 flex justify-between items-center">
                <span class="text-xs font-bold uppercase text-white/40">Admin Mode</span>
                <button @click="adminMode = !adminMode" :class="adminMode ? 'bg-blue-400' : 'bg-slate-600'" class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                    <span :class="adminMode ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 bg-white rounded-full transition"></span>
                </button>
            </div>
            <?php endif; ?>
        </aside>

        <header class="md:hidden bg-[#1a325c] px-6 py-4 flex justify-between items-center shadow-lg z-50">
            <img src="assets/logos/file_00000000fa58722fbc346086d4f1692b.png" class="h-10 w-auto">
            <a href="?logout=1" class="text-rose-400 font-bold text-xs uppercase">Logout</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar bg-slate-50 pb-24 md:pb-12">
            <header class="hidden md:flex justify-between items-center mb-12">
                <h2 class="text-3xl font-black text-[#1a325c]" x-text="adminMode ? 'System Approvals' : 'Stakeholder Portals'"></h2>
                <div class="flex items-center space-x-4">
                    <span class="font-bold text-slate-600"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <a href="?logout=1" class="text-rose-500 font-bold text-sm">Logout</a>
                </div>
            </header>

            <div x-show="!adminMode">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 pb-12">
                    <?php foreach ($stakeholders as $s): ?>
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden group">
                        <div class="h-32 md:h-44 bg-slate-50 flex items-center justify-center p-6 md:p-8 border-b border-slate-50">
                            <img src="<?= htmlspecialchars($s['logo_path'] ?? 'assets/placeholder.png') ?>" class="max-h-full max-w-full object-contain transition duration-500 md:group-hover:scale-110">
                        </div>
                        <div class="p-6 md:p-8 text-center md:text-left">
                            <h3 class="text-lg md:text-xl font-bold text-[#1a325c] mb-2"><?= htmlspecialchars($s['name']) ?></h3>
                            <p class="text-slate-500 text-xs md:text-sm mb-6 leading-relaxed line-clamp-2"><?= htmlspecialchars($s['description']) ?></p>
                            <button onclick="openPortal('<?= $s['url'] ?>', '<?= addslashes($s['name']) ?>')" 
                                    class="w-full py-3 md:py-4 bg-slate-100 text-[#1a325c] rounded-xl md:rounded-2xl font-bold hover:bg-[#1a325c] hover:text-white transition active:scale-95">
                                Launch Portal
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($isAdmin): ?>
            <div x-show="adminMode" x-cloak>
                <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[500px]">
                            <thead class="bg-slate-50 text-[10px] md:text-xs font-bold uppercase text-slate-400">
                                <tr><th class="p-4 md:p-6">Name</th><th class="p-4 md:p-6">Agency</th><th class="p-4 md:p-6 text-right">Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingUsers as $u): ?>
                                <tr class="border-t border-slate-50 text-sm">
                                    <td class="p-4 md:p-6 font-bold"><?= htmlspecialchars($u['name']) ?></td>
                                    <td class="p-4 md:p-6 text-slate-500"><?= htmlspecialchars($u['company_name']) ?></td>
                                    <td class="p-4 md:p-6 text-right">
                                        <form action="includes/actions.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button name="action" value="approve" class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-lg font-bold">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>

        <?php if ($isAdmin): ?>
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 flex justify-around items-center p-3 z-50">
            <button @click="adminMode = false" :class="!adminMode ? 'text-[#1a325c]' : 'text-slate-400'" class="flex flex-col items-center">
                <span class="text-[10px] font-bold uppercase">Portals</span>
            </button>
            <button @click="adminMode = true" :class="adminMode ? 'text-[#1a325c]' : 'text-slate-400'" class="flex flex-col items-center">
                <span class="text-[10px] font-bold uppercase">Approvals</span>
            </button>
        </nav>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div id="portalModal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-[#1a325c]/95 md:p-10">
        <div class="bg-white w-full h-full md:rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
                <h2 id="modalTitle" class="text-sm md:text-xl font-black text-[#1a325c] truncate">Portal</h2>
                <button onclick="closePortal()" class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500">✕</button>
            </div>
            <div class="flex-1 relative bg-white">
                <div id="loadingSpinner" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
                    <div class="w-8 h-8 border-4 border-[#1a325c]/10 border-t-[#1a325c] rounded-full animate-spin mb-4"></div>
                </div>
                <iframe id="portalIframe" class="w-full h-full border-none" onload="document.getElementById('loadingSpinner').style.display='none'"></iframe>
            </div>
        </div>
    </div>

    <div x-show="showLogin" x-cloak class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-[#1a325c]/60 backdrop-blur-sm p-0 md:p-6">
        <div @click.away="showLogin = false" class="bg-white w-full max-w-md p-8 md:p-10 rounded-t-[2.5rem] md:rounded-[3rem] shadow-2xl">
            <h2 class="text-xl md:text-2xl font-black mb-6 text-[#1a325c]">Staff Portal</h2>
            <form action="includes/actions.php" method="POST" class="space-y-4">
                <input type="email" name="email" placeholder="Email" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <input type="password" name="password" placeholder="Password" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <button type="submit" name="login" class="w-full py-4 bg-[#1a325c] text-white rounded-xl md:rounded-2xl font-bold">Sign In</button>
            </form>
        </div>
    </div>

    <div x-show="showRegister" x-cloak class="fixed inset-0 z-[100] flex items-end md:items-center justify-center bg-[#1a325c]/60 backdrop-blur-sm p-0 md:p-6">
        <div @click.away="showRegister = false" class="bg-white w-full max-w-md p-8 md:p-10 rounded-t-[2.5rem] md:rounded-[3rem] shadow-2xl">
            <h2 class="text-xl md:text-2xl font-black mb-2 text-[#1a325c]">Join Network</h2>
            <p class="text-slate-400 text-xs mb-6 uppercase font-bold tracking-wider">Partner Application</p>
            <form action="includes/actions.php" method="POST" class="space-y-3 md:space-y-4">
                <input type="text" name="name" placeholder="Full Name" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <input type="text" name="company" placeholder="Agency Name" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <input type="email" name="email" placeholder="Email Address" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <input type="password" name="password" placeholder="Create Password" required class="w-full px-5 py-3 md:px-6 md:py-4 bg-slate-50 border border-slate-100 rounded-xl md:rounded-2xl outline-none">
                <button type="submit" name="register" class="w-full py-4 bg-[#1a325c] text-white rounded-xl md:rounded-2xl font-bold shadow-xl">Submit Application</button>
            </form>
        </div>
    </div>

    <script>
        function openPortal(url, name) {
            const modal = document.getElementById('portalModal');
            const iframe = document.getElementById('portalIframe');
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('loadingSpinner').style.display = 'flex';
            iframe.src = url;
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }
        function closePortal() {
            const modal = document.getElementById('portalModal');
            modal.classList.remove('active');
            document.getElementById('portalIframe').src = '';
            document.body.classList.remove('modal-open');
        }
        document.addEventListener('DOMContentLoaded', () => {
            new Swiper(".myHeroSwiper", { effect: "fade", loop: true, autoplay: { delay: 4000 } });
        });
    </script>
</body>
</html>