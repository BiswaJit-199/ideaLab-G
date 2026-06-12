<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

// Set active page
$activePage = isset($activePage) ? $activePage : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? ADMIN_TITLE) ?></title>
    <link rel="stylesheet" href="css/admin-style.css">
    <!-- Load Tailwind CDN inside Admin for beautiful layout & responsiveness -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Re-apply some side-bar spacing because of tailwind box-sizing */
        .sidebar {
            z-index: 100;
        }
        .main-content {
            margin-left: 250px;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
            }
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside id="admin-sidebar" class="sidebar fixed inset-y-0 left-0 w-[250px] bg-slate-900 text-white flex flex-col justify-between shadow-2xl transition-transform duration-300 md:translate-x-0">
            <div>
                <div class="sidebar-header p-6 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">ideaLab Admin</h2>
                        <p class="user-badge text-xs text-slate-400 mt-1">👤 <?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?></p>
                    </div>
                    <button class="md:hidden text-slate-400 hover:text-white" onclick="toggleSidebar()">✕</button>
                </div>
                
                <nav class="sidebar-nav py-6">
                    <a href="dashboard.php" class="nav-link flex items-center px-6 py-3.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-all border-l-4 <?= $activePage == 'dashboard' ? 'bg-indigo-900/40 text-indigo-400 border-indigo-500 font-semibold' : 'border-transparent' ?>">
                        <span class="icon mr-3 text-lg">📊</span>
                        Dashboard
                    </a>
                    <a href="manage-text.php" class="nav-link flex items-center px-6 py-3.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-all border-l-4 <?= $activePage == 'homepage' ? 'bg-indigo-900/40 text-indigo-400 border-indigo-500 font-semibold' : 'border-transparent' ?>">
                        <span class="icon mr-3 text-lg">🏠</span>
                        Homepage Editor
                    </a>
                    <a href="manage-gallery.php" class="nav-link flex items-center px-6 py-3.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-all border-l-4 <?= $activePage == 'gallery' ? 'bg-indigo-900/40 text-indigo-400 border-indigo-500 font-semibold' : 'border-transparent' ?>">
                        <span class="icon mr-3 text-lg">📸</span>
                        Gallery content
                    </a>
                    <a href="manage-assets.php" class="nav-link flex items-center px-6 py-3.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-all border-l-4 <?= $activePage == 'assets' ? 'bg-indigo-900/40 text-indigo-400 border-indigo-500 font-semibold' : 'border-transparent' ?>">
                        <span class="icon mr-3 text-lg">📁</span>
                        Asset Manager
                    </a>
                    <a href="logs-view.php" class="nav-link flex items-center px-6 py-3.5 text-slate-300 hover:bg-slate-800 hover:text-white transition-all border-l-4 <?= $activePage == 'logs' ? 'bg-indigo-900/40 text-indigo-400 border-indigo-500 font-semibold' : 'border-transparent' ?>">
                        <span class="icon mr-3 text-lg">📜</span>
                        Action Logs
                    </a>
                </nav>
            </div>
            
            <div class="sidebar-footer p-6 border-t border-slate-800 bg-slate-950/50">
                <form method="POST" action="dashboard.php" style="width: 100%;">
                    <button type="submit" name="logout" class="logout-btn w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all">
                        <span class="icon">🚪</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-content flex-1 min-h-screen flex flex-col md:pl-0">
            <!-- Top Nav -->
            <header class="top-bar bg-white px-8 py-5 border-b border-slate-200 flex items-center justify-between shadow-sm sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-slate-600 hover:text-indigo-600" onclick="toggleSidebar()">
                        <span class="text-2xl">☰</span>
                    </button>
                    <h1 class="text-xl font-bold text-slate-800 capitalize"><?= htmlspecialchars(str_replace('-', ' ', $activePage)) ?></h1>
                </div>
                <div class="top-bar-right flex items-center gap-6">
                    <span class="time text-sm font-semibold text-slate-500 bg-slate-100 py-1.5 px-3 rounded-full" id="current-time"></span>
                    <a href="../index.php" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                        🖥️ View Website &rarr;
                    </a>
                </div>
            </header>
            
            <!-- Dynamic Content Area -->
            <main class="p-8 flex-grow">
