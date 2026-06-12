<?php
/**
 * SECURE MEDIA ASSETS MANAGER
 * 
 * Implements strict CSRF validation, path traversal protection (basename), 
 * file upload type restrictions, and prevents raw code injection (e.g. php code in images).
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

$pageTitle = "Asset Manager - " . ADMIN_TITLE;
$activePage = 'assets';

$uploadError = '';
$uploadSuccess = '';

// Check if uploads folder exists
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

// ---------------------------------------------------------------------
// CSRF Token Validation for all POST operations
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die('CSRF token validation failed. Unauthorized operation.');
    }
}

// ---------------------------------------------------------------------
// 1. Handle File Upload
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_asset'])) {
    if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['new_file']['tmp_name'];
        // Strip out malicious characters from filenames
        $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $_FILES['new_file']['name']);
        $fileSize = $_FILES['new_file']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'mov', 'ogg'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            
            // SECURITY BEST PRACTICE: Read the file content and verify there is no PHP or script code injected!
            $fileContent = file_get_contents($fileTmpPath);
            if (
                preg_match('/<\?php/i', $fileContent) || 
                preg_match('/<script/i', $fileContent) || 
                preg_match('/<\?=/i', $fileContent)
            ) {
                $uploadError = "Security Alert: Malicious script tags or PHP markers detected inside the file content.";
                addLog('Security Alert', 'Malicious file upload attempt detected and blocked for file: ' . sanitizeInput($fileName));
            } else {
                // Ensure unique name on disk
                $destination = UPLOADS_DIR . $fileName;
                if (file_exists($destination)) {
                    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileName = $baseName . '_' . time() . '.' . $fileExtension;
                    $destination = UPLOADS_DIR . $fileName;
                }
                
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $uploadSuccess = "Asset uploaded successfully as <strong>" . htmlspecialchars($fileName) . "</strong>!";
                    addLog('Asset Uploaded', 'Uploaded file: "' . $fileName . '" (' . round($fileSize / 1024, 2) . ' KB).');
                } else {
                    $uploadError = "There was an error saving the uploaded file on the server.";
                }
            }
        } else {
            $uploadError = "Invalid file extension. Allowed extensions are: " . implode(', ', $allowedExtensions);
        }
    } else {
        $uploadError = "No file selected or an upload error occurred.";
    }
}

// ---------------------------------------------------------------------
// 2. Handle File Deletion (Path Traversal Protected using basename)
// ---------------------------------------------------------------------
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']); // Mitigates path traversal like ?delete=../../index.php
    $filePath = UPLOADS_DIR . $fileToDelete;
    
    if (file_exists($filePath) && is_file($filePath)) {
        if (unlink($filePath)) {
            $uploadSuccess = "Asset <strong>" . htmlspecialchars($fileToDelete) . "</strong> was deleted successfully.";
            addLog('Asset Deleted', 'Deleted file from server: "' . $fileToDelete . '".');
        } else {
            $uploadError = "Failed to delete file from the server.";
        }
    } else {
        $uploadError = "File does not exist or cannot be deleted.";
    }
}

// ---------------------------------------------------------------------
// 3. Handle File Renaming (Path Traversal Protected)
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rename_asset'])) {
    $oldName = basename($_POST['old_name']);
    $newNameInput = trim($_POST['new_name']);
    $fileExtension = strtolower(pathinfo($oldName, PATHINFO_EXTENSION));
    
    // Clean and validate new name
    $newBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($newNameInput, PATHINFO_FILENAME));
    $newName = $newBaseName . '.' . $fileExtension;
    
    $oldPath = UPLOADS_DIR . $oldName;
    $newPath = UPLOADS_DIR . $newName;
    
    if ($oldName !== '' && $newNameInput !== '') {
        if (file_exists($oldPath) && is_file($oldPath)) {
            if (file_exists($newPath)) {
                $uploadError = "A file named '" . htmlspecialchars($newName) . "' already exists.";
            } else {
                if (rename($oldPath, $newPath)) {
                    $uploadSuccess = "Asset successfully renamed to <strong>" . htmlspecialchars($newName) . "</strong>!";
                    addLog('Asset Renamed', 'Renamed file from "' . $oldName . '" to "' . $newName . '".');
                } else {
                    $uploadError = "Could not rename the file on server.";
                }
            }
        } else {
            $uploadError = "Source file does not exist.";
        }
    } else {
        $uploadError = "Invalid file names provided.";
    }
}

// Read assets recursively if needed, or flat directory
$assetsList = [];
if (is_dir(UPLOADS_DIR)) {
    $files = scandir(UPLOADS_DIR);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..', '.git', '.htaccess'])) continue;
        
        $filePath = UPLOADS_DIR . $file;
        if (is_file($filePath)) {
            $size = filesize($filePath);
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
            $is_video = in_array($ext, ['mp4', 'mov', 'webm', 'ogg']);
            
            $assetsList[] = [
                'name' => $file,
                'path' => 'assets/' . $file,
                'size' => $size,
                'extension' => $ext,
                'is_image' => $is_image,
                'is_video' => $is_video,
                'date' => filemtime($filePath)
            ];
        }
    }
}

// Sort assets by date descending
usort($assetsList, function($a, $b) {
    return $b['date'] - $a['date'];
});

include __DIR__ . '/header.php';
?>

<div class="space-y-8">
    <!-- Feedback Alerts -->
    <?php if ($uploadSuccess !== ''): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm text-sm" role="alert">
            <span class="font-bold">Success:</span> <?= $uploadSuccess ?>
        </div>
    <?php endif; ?>
    <?php if ($uploadError !== ''): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm text-sm" role="alert">
            <span class="font-bold">Error:</span> <?= $uploadError ?>
        </div>
    <?php endif; ?>

    <!-- Header Page -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Media Asset Manager</h2>
            <p class="text-sm text-slate-500 mt-1">Upload and manage image or video assets securely. PHP files and malicious scripts are automatically blocked.</p>
        </div>
        
        <button onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-xl text-sm transition-all shadow-md flex items-center gap-2 shrink-0">
            📤 Upload New Asset
        </button>
    </div>

    <!-- Assets Grid View -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if (empty($assetsList)): ?>
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <span class="text-4xl block mb-3">📂</span>
                <p class="font-bold">No assets found inside the /assets folder.</p>
            </div>
        <?php else: ?>
            <?php foreach ($assetsList as $asset): ?>
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                    <!-- Preview -->
                    <div class="relative bg-slate-900 h-44 flex items-center justify-center overflow-hidden border-b border-slate-100">
                        <?php if ($asset['is_image']): ?>
                            <img src="../<?= htmlspecialchars($asset['path']) ?>" alt="Thumbnail of <?= htmlspecialchars($asset['name']) ?>" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                        <?php elseif ($asset['is_video']): ?>
                            <video muted controls class="w-full h-full object-cover">
                                <source src="../<?= htmlspecialchars($asset['path']) ?>" type="video/<?= htmlspecialchars($asset['extension']) ?>">
                            </video>
                        <?php else: ?>
                            <span class="text-3xl font-bold text-indigo-400 uppercase"><?= htmlspecialchars($asset['extension']) ?></span>
                        <?php endif; ?>
                        
                        <div class="absolute top-2 left-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-black/60 text-[10px] font-bold text-white uppercase backdrop-blur-sm">
                                <?= htmlspecialchars($asset['extension']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm break-all" title="<?= htmlspecialchars($asset['name']) ?>">
                                <?= htmlspecialchars($asset['name']) ?>
                            </h4>
                            <p class="text-xs text-slate-400 mt-1">Size: <?= round($asset['size'] / 1024, 2) ?> KB</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-50 space-y-2.5">
                            <div class="flex items-center gap-1.5 bg-slate-50 p-1.5 rounded-lg border border-slate-100">
                                <input id="path-<?= md5($asset['name']) ?>" type="text" readonly value="assets/<?= htmlspecialchars($asset['name']) ?>" class="bg-transparent border-none text-[11px] font-mono text-slate-500 w-full focus:outline-none focus:ring-0 p-0 select-all">
                                <button onclick="copyPath('path-<?= md5($asset['name']) ?>')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded p-1 text-[10px] font-bold shrink-0 transition" title="Copy Path">Copy</button>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="openRenameModal('<?= htmlspecialchars($asset['name']) ?>')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-1.5 px-3 rounded-lg text-xs transition">
                                    Rename
                                </button>
                                <a href="?delete=<?= urlencode($asset['name']) ?>" onclick="return confirm('Are you sure you want to delete this asset? This cannot be undone.')" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold py-1.5 px-3 rounded-lg text-xs transition text-center block">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-6 hidden flex">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl p-6 relative">
        <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Upload New Media Asset</h3>
        
        <form method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <div class="border-2 border-dashed border-indigo-200 rounded-2xl bg-indigo-50/20 p-8 text-center flex flex-col items-center">
                <span class="text-4xl block mb-2">📁</span>
                <p class="text-sm font-semibold text-slate-700">Choose file to upload</p>
                <p class="text-xs text-slate-400 mt-1">Allowed: JPG, JPEG, PNG, GIF, WEBP, SVG, MP4, WEBM</p>
                
                <input type="file" name="new_file" id="new_file" required class="hidden" onchange="updateFileName(this)">
                <label for="new_file" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-xs mt-4 inline-block cursor-pointer transition">
                    Browse File
                </label>
                <span id="selected-file-name" class="text-xs font-semibold text-indigo-600 mt-3 hidden"></span>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded-lg text-xs transition">Cancel</button>
                <button type="submit" name="upload_asset" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow animate-duration-150">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Modal -->
<div id="rename-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-6 hidden flex">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 relative">
        <button onclick="document.getElementById('rename-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Rename Media Asset</h3>
        
        <form method="POST" class="mt-4 space-y-4">
            <input type="hidden" name="old_name" id="rename_old_name">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Asset Name</label>
                <input type="text" id="rename_display_old" readonly class="w-full bg-slate-50 border border-slate-100 p-3 rounded-xl text-sm text-slate-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">New Asset Name</label>
                <input type="text" name="new_name" id="rename_new_name" required placeholder="new_file_name" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('rename-modal').classList.add('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded-lg text-xs transition">Cancel</button>
                <button type="submit" name="rename_asset" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow">Rename Asset</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('selected-file-name');
    if (input.files && input.files[0]) {
        label.textContent = "Selected: " + input.files[0].name;
        label.classList.remove('hidden');
    } else {
        label.classList.add('hidden');
    }
}

function copyPath(inputId) {
    const copyText = document.getElementById(inputId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(() => {
        alert("Path copied to clipboard: " + copyText.value);
    });
}

function openRenameModal(fileName) {
    document.getElementById('rename_old_name').value = fileName;
    document.getElementById('rename_display_old').value = fileName;
    const baseName = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
    document.getElementById('rename_new_name').value = baseName;
    document.getElementById('rename-modal').classList.remove('hidden');
}
</script>

<?php include __DIR__ . '/footer.php'; ?>