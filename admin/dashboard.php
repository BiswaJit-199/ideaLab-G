<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

// Handle Logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    addLog('Admin Logout', 'Admin user manually logged out.');
    logout();
}

$pageTitle = "Dashboard - " . ADMIN_TITLE;
$activePage = 'dashboard';

// Fetch statistics
$imageCount = 0;
$videoCount = 0;
$galleryCount = 0;
$totalStorage = 0;

if (is_dir(UPLOADS_DIR)) {
    $files = scandir(UPLOADS_DIR);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..'])) continue;
        $filePath = UPLOADS_DIR . $file;
        if (is_file($filePath)) {
            $totalStorage += filesize($filePath);
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                $imageCount++;
            } elseif (in_array($ext, ['mp4', 'mov', 'webm', 'ogg'])) {
                $videoCount++;
            }
        }
    }
}

// Fetch Gallery Count
$galleryFile = dirname(__DIR__) . '/data/gallery.json';
if (file_exists($galleryFile)) {
    $galleryData = json_decode(file_get_contents($galleryFile), true);
    if (isset($galleryData['inaguration'])) {
        $galleryCount = count($galleryData['inaguration']);
    }
}

// Fetch Logs
$logsFile = dirname(__DIR__) . '/data/logs.json';
$recentLogs = [];
if (file_exists($logsFile)) {
    $recentLogs = array_slice(json_decode(file_get_contents($logsFile), true) ?? [], 0, 5);
}

include __DIR__ . '/header.php';
?>

<div class="space-y-8">
    <!-- Header Summary Card -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-800 text-white rounded-2xl p-8 shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h2 class="text-3xl font-extrabold">Welcome back, Administrator!</h2>
            <p class="text-indigo-200 mt-2 text-sm md:text-base">This panel lets you control the Hero visual, Homepage sections, edit the dynamic Gallery, manage image & video assets, and track activity logs.</p>
        </div>
        <div class="flex gap-4">
            <a href="manage-text.php" class="bg-white text-indigo-900 font-bold px-5 py-3 rounded-xl hover:bg-slate-100 transition shadow-md">
                ✏️ Edit Homepage
            </a>
            <a href="manage-assets.php" class="bg-indigo-700/60 text-white font-bold px-5 py-3 rounded-xl hover:bg-indigo-600/75 transition border border-indigo-500/40">
                📁 Upload Assets
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl text-indigo-600 shrink-0">🖼️</div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Images on Server</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $imageCount ?></h3>
                <a href="manage-assets.php" class="text-xs text-indigo-600 hover:underline mt-1 block">Manage Files &rarr;</a>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl text-emerald-600 shrink-0">📹</div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Videos on Server</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $videoCount ?></h3>
                <a href="manage-assets.php" class="text-xs text-emerald-600 hover:underline mt-1 block">Manage Files &rarr;</a>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center text-2xl text-pink-600 shrink-0">📸</div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Gallery Collections</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $galleryCount ?></h3>
                <a href="manage-gallery.php" class="text-xs text-pink-600 hover:underline mt-1 block">Manage Gallery &rarr;</a>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl text-amber-600 shrink-0">⚙️</div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Storage Usage</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= round($totalStorage / 1024 / 1024, 2) ?> MB</h3>
                <span class="text-xs text-slate-400 mt-1 block">Assets Directory</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Logs -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Recent Admin Activities</h3>
                <a href="logs-view.php" class="text-sm font-semibold text-indigo-600 hover:underline">View All Logs &rarr;</a>
            </div>
            
            <div class="divide-y divide-slate-100 flex-grow">
                <?php if (empty($recentLogs)): ?>
                    <p class="text-center text-slate-400 py-12">No recent system activities found.</p>
                <?php else: ?>
                    <?php foreach ($recentLogs as $log): ?>
                        <div class="p-5 hover:bg-slate-50/50 transition flex justify-between items-start gap-4">
                            <div>
                                <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-xs font-bold text-slate-600 uppercase mb-2">
                                    <?= htmlspecialchars($log['user']) ?>
                                </span>
                                <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($log['action']) ?></h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed"><?= htmlspecialchars($log['details']) ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-medium text-slate-400 block"><?= htmlspecialchars($log['timestamp']) ?></span>
                                <span class="text-[10px] text-slate-400 mt-1 block">IP: <?= htmlspecialchars($log['ip']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick System Access Checklist -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <h3 class="text-lg font-bold text-slate-800">Quick System Shortcuts</h3>
            
            <div class="space-y-4">
                <a href="manage-text.php#hero" class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/20 transition group">
                    <span class="text-xl">🚀</span>
                    <div class="flex-grow">
                        <h4 class="font-bold text-sm text-slate-800 group-hover:text-indigo-900 transition">Update Hero Area</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Edit title, subtitle, video, buttons</p>
                    </div>
                </a>

                <a href="manage-text.php#projects" class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/20 transition group">
                    <span class="text-xl">💡</span>
                    <div class="flex-grow">
                        <h4 class="font-bold text-sm text-slate-800 group-hover:text-indigo-900 transition">Manage Projects</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Add, Edit, Delete active project cards</p>
                    </div>
                </a>

                <a href="manage-gallery.php" class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/20 transition group">
                    <span class="text-xl">📸</span>
                    <div class="flex-grow">
                        <h4 class="font-bold text-sm text-slate-800 group-hover:text-indigo-900 transition">Gallery Collections</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Upload, Edit descriptive tags and images</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>