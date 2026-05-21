<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$message_type = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $message = 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed.';
        $message_type = 'danger';
    } elseif ($file['size'] > $max_size) {
        $message = 'File size exceeds 5MB limit.';
        $message_type = 'danger';
    } elseif ($file['error'] === UPLOAD_ERR_OK) {
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $filepath = UPLOADS_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            createThumbnail($filepath);
            $message = 'Image uploaded successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to upload image. Check directory permissions.';
            $message_type = 'danger';
        }
    }
}

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_image'])) {
    $image_name = basename($_POST['delete_image']);
    $image_path = UPLOADS_DIR . $image_name;
    $thumb_path = UPLOADS_DIR . 'thumbs/' . $image_name;
    
    if (file_exists($image_path) && unlink($image_path)) {
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
        $message = 'Image deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Failed to delete image.';
        $message_type = 'danger';
    }
}

// Get all images
$images = [];
if (is_dir(UPLOADS_DIR)) {
    $files = scandir(UPLOADS_DIR);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $filepath = UPLOADS_DIR . $file;
            $images[] = [
                'name' => $file,
                'size' => filesize($filepath),
                'date' => filemtime($filepath),
                'url' => '../assets/uploads/' . $file
            ];
        }
    }
}

// Sort by date (newest first)
usort($images, function($a, $b) {
    return $b['date'] - $a['date'];
});

function createThumbnail($imagePath) {
    $thumbDir = UPLOADS_DIR . 'thumbs/';
    if (!is_dir($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }
    
    $filename = basename($imagePath);
    $thumbPath = $thumbDir . $filename;
    
    if (extension_loaded('gd')) {
        $image = imagecreatefromstring(file_get_contents($imagePath));
        if ($image) {
            $width = imagesx($image);
            $height = imagesy($image);
            $thumb_width = 200;
            $thumb_height = 200;
            
            $scale = min($thumb_width / $width, $thumb_height / $height);
            $new_width = $width * $scale;
            $new_height = $height * $scale;
            
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            $white = imagecolorallocate($thumb, 255, 255, 255);
            imagefill($thumb, 0, 0, $white);
            
            $x = ($thumb_width - $new_width) / 2;
            $y = ($thumb_height - $new_height) / 2;
            
            imagecopyresampled($thumb, $image, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($thumb, $thumbPath, 85);
            
            imagedestroy($image);
            imagedestroy($thumb);
        }
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images - <?php echo ADMIN_TITLE; ?></title>
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
                <a href="dashboard.php" class="nav-link" data-page="dashboard">
                    <span class="icon">📊</span>
                    Dashboard
                </a>
                <a href="manage-images.php" class="nav-link active" data-page="images">
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
                <h1>🖼️ Manage Images</h1>
                <div class="top-bar-right">
                    <span class="time" id="current-time"></span>
                </div>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" style="margin: 30px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Upload Section -->
            <div class="form-container" id="upload">
                <h2>📤 Upload New Image</h2>
                <p style="color: #7f8c8d; margin-bottom: 20px;">Maximum file size: 5 MB | Supported formats: JPG, PNG, GIF, WEBP</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-dropzone" id="dropzone">
                        <div class="upload-icon">📁</div>
                        <p><strong>Drag and drop your image here</strong></p>
                        <p style="font-size: 12px;">or click to select</p>
                        <input type="file" id="imageInput" name="image" accept="image/*">
                    </div>
                    
                    <div id="uploadProgress" style="display: none;">
                        <p>Uploading...</p>
                        <div class="progress">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="margin-top: 20px; width: 100%;">
                        📤 Upload Image
                    </button>
                </form>
            </div>
            
            <!-- Stats Section -->
            <div style="padding: 0 30px; margin-top: 30px;">
                <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 0;">
                    <div class="stat-card" style="flex-direction: column;">
                        <div style="text-align: center;">
                            <div style="font-size: 32px; margin-bottom: 10px;">🎯</div>
                            <h3 style="margin-bottom: 5px; color: #7f8c8d;">Total Images</h3>
                            <p class="stat-number" style="margin: 0;"><?php echo count($images); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card" style="flex-direction: column;">
                        <div style="text-align: center;">
                            <div style="font-size: 32px; margin-bottom: 10px;">💾</div>
                            <h3 style="margin-bottom: 5px; color: #7f8c8d;">Storage Used</h3>
                            <p class="stat-number" style="margin: 0; font-size: 20px;">
                                <?php 
                                    $total_size = 0;
                                    foreach ($images as $img) {
                                        $total_size += $img['size'];
                                    }
                                    echo formatFileSize($total_size);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Images Gallery -->
            <div style="padding: 30px;">
                <h2>📸 Your Images</h2>
                
                <?php if (empty($images)): ?>
                    <div class="no-data">
                        <div class="no-data-icon">📭</div>
                        <p><strong>No images uploaded yet</strong></p>
                        <p style="font-size: 13px;">Upload your first image using the form above</p>
                    </div>
                <?php else: ?>
                    <div class="gallery-grid">
                        <?php foreach ($images as $image): ?>
                            <div class="gallery-item">
                                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['name']; ?>" class="gallery-item-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-family=%22sans-serif%22 font-size=%2214%22 fill=%22%23999%22%3EImage Not Found%3C/text%3E%3C/svg%3E'">
                                <div class="gallery-item-info">
                                    <div class="gallery-item-name" title="<?php echo htmlspecialchars($image['name']); ?>">
                                        <?php echo htmlspecialchars(substr($image['name'], 0, 25)) . (strlen($image['name']) > 25 ? '...' : ''); ?>
                                    </div>
                                    <div class="gallery-item-size">
                                        📦 <?php echo formatFileSize($image['size']); ?>
                                        <br>
                                        🕐 <?php echo date('M d, Y', $image['date']); ?>
                                    </div>
                                    <div class="gallery-item-actions">
                                        <button type="button" class="btn-view" onclick="viewImage('<?php echo htmlspecialchars($image['url']); ?>', '<?php echo htmlspecialchars($image['name']); ?>')">
                                            👁️ View
                                        </button>
                                        <form method="POST" style="flex: 1; margin: 0;">
                                            <input type="hidden" name="delete_image" value="<?php echo htmlspecialchars($image['name']); ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Delete this image?');">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Image Viewer Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <button class="modal-close" onclick="closeModal()">✕</button>
            <div style="margin-bottom: 20px;">
                <img id="modalImage" src="" alt="" style="width: 100%; border-radius: 8px; max-height: 500px; object-fit: contain;">
            </div>
            <div id="modalImageName" style="color: #2c3e50; font-weight: 600; margin-bottom: 10px;"></div>
            <button onclick="closeModal()" class="btn btn-primary" style="width: 100%;">Close</button>
        </div>
    </div>
    
    <script src="js/admin.js"></script>
    <script>
        // Dropzone functionality
        const dropzone = document.getElementById('dropzone');
        const imageInput = document.getElementById('imageInput');
        
        dropzone.addEventListener('click', () => imageInput.click());
        
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });
        
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            imageInput.files = e.dataTransfer.files;
            imageInput.closest('form').submit();
        });
        
        // View image modal
        function viewImage(src, name) {
            document.getElementById('imageModal').classList.add('active');
            document.getElementById('modalImage').src = src;
            document.getElementById('modalImageName').textContent = name;
        }
        
        function closeModal() {
            document.getElementById('imageModal').classList.remove('active');
        }
        
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
