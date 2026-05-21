<?php
require_once 'config.php';
requireAdminLogin();

// Count images and gallery items
$imageCount = 0;
$galleryCount = 0;

if (is_dir(UPLOADS_DIR)) {
    $images = array_filter(scandir(UPLOADS_DIR), function($file) {
        return !in_array($file, ['.', '..']) && 
               preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    $imageCount = count($images);
}

// Count gallery items from data
if (file_exists(DATA_DIR . 'gallery.json')) {
    $galleryData = json_decode(file_get_contents(DATA_DIR . 'gallery.json'), true);
    $galleryCount = count($galleryData ?? []);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo ADMIN_TITLE; ?></title>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>ideaLab Admin</h2>
                <p class="user-badge">👤 Admin</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active" data-page="dashboard">
                    <span class="icon">📊</span>
                    Dashboard
                </a>
                <a href="manage-images.php" class="nav-link" data-page="images">
                    <span class="icon">🖼️</span>
                    Manage Images
                </a>
                <a href="manage-gallery.php" class="nav-link" data-page="gallery">
                    <span class="icon">📸</span>
                    Gallery Content
                </a>
                <a href="manage-text.php" class="nav-link" data-page="text">
                    <span class="icon">📝</span>
                    Text Content
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <form method="POST" style="width: 100%;">
                    <button type="submit" name="logout" class="logout-btn">
                        <span class="icon">🚪</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <h1>Dashboard</h1>
                <div class="top-bar-right">
                    <span class="time" id="current-time"></span>
                    <button class="help-btn" title="Help">?</button>
                </div>
            </div>
            
            <!-- Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Stats Cards -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        🖼️
                    </div>
                    <div class="stat-content">
                        <h3>Total Images</h3>
                        <p class="stat-number"><?php echo $imageCount; ?></p>
                        <a href="manage-images.php" class="stat-link">View Images →</a>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        📸
                    </div>
                    <div class="stat-content">
                        <h3>Gallery Items</h3>
                        <p class="stat-number"><?php echo $galleryCount; ?></p>
                        <a href="manage-gallery.php" class="stat-link">Manage Gallery →</a>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        📝
                    </div>
                    <div class="stat-content">
                        <h3>Text Content</h3>
                        <p class="stat-number">5</p>
                        <a href="manage-text.php" class="stat-link">Edit Content →</a>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        ⚙️
                    </div>
                    <div class="stat-content">
                        <h3>Storage Used</h3>
                        <p class="stat-number"><?php 
                            $size = 0;
                            if (is_dir(UPLOADS_DIR)) {
                                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UPLOADS_DIR)) as $file) {
                                    if ($file->isFile()) $size += $file->getSize();
                                }
                            }
                            echo round($size / 1024 / 1024, 2) . ' MB';
                        ?></p>
                        <a href="manage-images.php" class="stat-link">Manage Space →</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="manage-images.php#upload" class="action-btn primary">
                        <span>📤</span> Upload Image
                    </a>
                    <a href="manage-gallery.php#add" class="action-btn">
                        <span>➕</span> Add Gallery Item
                    </a>
                    <a href="manage-text.php" class="action-btn">
                        <span>✏️</span> Edit Content
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    logout();
}
?>
