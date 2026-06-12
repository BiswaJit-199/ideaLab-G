<?php
/**
 * SECURE GALLERY ALBUMS MANAGER (CRUD)
 * 
 * Includes dynamic asset selector checklist, input sanitization, and CSRF protection.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

$pageTitle = "Gallery Content Manager - " . ADMIN_TITLE;
$activePage = 'gallery';

$galleryFile = dirname(__DIR__) . '/data/gallery.json';

// Initialize data structure
$galleryData = [];
if (file_exists($galleryFile)) {
    $galleryData = json_decode(file_get_contents($galleryFile), true) ?? [];
}

$error = '';
$success = '';

// Helper to save gallery JSON
function saveGallery($data) {
    global $galleryFile;
    return file_put_contents($galleryFile, json_encode($data, JSON_PRETTY_PRINT));
}

// ---------------------------------------------------------------------
// CSRF Token Validation
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die('CSRF token validation failed. Unauthorized operation.');
    }
}

// ---------------------------------------------------------------------
// 1. Handle ADD or EDIT Gallery Item
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_item'])) {
    $item_id = isset($_POST['item_id']) ? sanitizeInput($_POST['item_id']) : '';
    // Restrict group key to safe alpha-numeric characters only
    $group_key = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($_POST['group_key'])));
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $tags_raw = sanitizeInput($_POST['tags']);
    $date = sanitizeInput($_POST['date']);
    $images_raw = sanitizeInput($_POST['images']); // Comma-separated paths
    
    // Parse tags & images arrays safely
    $tags = array_filter(array_map('trim', explode(',', $tags_raw)));
    $images = array_filter(array_map('trim', explode(',', $images_raw)));
    
    // Secure image paths (must not perform path traversal)
    foreach ($images as &$img) {
        $img = trim($img);
        // Standardize path representation, remove directory traversal characters
        $img = str_replace(['../', '..\\'], '', $img);
    }
    unset($img); // Break reference
    
    if ($group_key === '' || $title === '' || empty($images)) {
        $error = "Group Key, Title, and at least one Image path are required.";
    } else {
        // Initialize group array if not exists
        if (!isset($galleryData[$group_key]) || !is_array($galleryData[$group_key])) {
            $galleryData[$group_key] = [];
        }
        
        if ($item_id !== '') {
            // EDIT MODE
            $found = false;
            foreach ($galleryData as $gKey => $groupItems) {
                foreach ($groupItems as $idx => $item) {
                    if (isset($item['id']) && $item['id'] == $item_id) {
                        // Remove from old group if group key changed
                        unset($galleryData[$gKey][$idx]);
                        $galleryData[$gKey] = array_values($galleryData[$gKey]); // Reindex
                        if (empty($galleryData[$gKey])) {
                            unset($galleryData[$gKey]);
                        }
                        
                        // Insert into new/current group
                        $updatedItem = [
                            'id' => (int)$item_id,
                            'title' => $title,
                            'description' => $description,
                            'tags' => array_values($tags),
                            'date' => $date !== '' ? $date : date('c'),
                            'images' => array_values($images)
                        ];
                        
                        $galleryData[$group_key][] = $updatedItem;
                        $found = true;
                        break 2;
                    }
                }
            }
            
            if ($found) {
                saveGallery($galleryData);
                $success = "Gallery item successfully updated!";
                addLog('Updated Gallery Item', 'Updated item: "' . $title . '" (ID: ' . $item_id . ') under group: "' . $group_key . '".');
            } else {
                $error = "Gallery item with ID " . htmlspecialchars($item_id) . " not found.";
            }
        } else {
            // ADD MODE
            // Calculate next ID
            $maxId = 0;
            foreach ($galleryData as $groupItems) {
                foreach ($groupItems as $item) {
                    if (isset($item['id']) && $item['id'] > $maxId) {
                        $maxId = $item['id'];
                    }
                }
            }
            $newId = $maxId + 1;
            
            $newItem = [
                'id' => $newId,
                'title' => $title,
                'description' => $description,
                'tags' => array_values($tags),
                'date' => $date !== '' ? $date : date('c'),
                'images' => array_values($images)
            ];
            
            $galleryData[$group_key][] = $newItem;
            saveGallery($galleryData);
            $success = "New gallery item added successfully!";
            addLog('Added Gallery Item', 'Created new item: "' . $title . '" (ID: ' . $newId . ') under group: "' . $group_key . '".');
        }
    }
}

// ---------------------------------------------------------------------
// 2. Handle DELETE Gallery Item
// ---------------------------------------------------------------------
if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $deleted = false;
    
    foreach ($galleryData as $gKey => $groupItems) {
        foreach ($groupItems as $idx => $item) {
            if (isset($item['id']) && $item['id'] === $delId) {
                $itemTitle = $item['title'];
                unset($galleryData[$gKey][$idx]);
                $galleryData[$gKey] = array_values($galleryData[$gKey]); // Reindex
                
                // If group is now empty, remove group key
                if (empty($galleryData[$gKey])) {
                    unset($galleryData[$gKey]);
                }
                
                saveGallery($galleryData);
                $deleted = true;
                $success = "Gallery item deleted successfully.";
                addLog('Deleted Gallery Item', 'Deleted item: "' . $itemTitle . '" (ID: ' . $delId . ') from group: "' . $gKey . '".');
                break 2;
            }
        }
    }
    
    if (!$deleted) {
        $error = "Item not found or could not be deleted.";
    }
}

// Fetch all available media assets from server folder for checkbox checklist
$availableAssets = [];
if (is_dir(UPLOADS_DIR)) {
    $files = scandir(UPLOADS_DIR);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..'])) continue;
        $filePath = UPLOADS_DIR . $file;
        if (is_file($filePath)) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                $availableAssets[] = 'assets/' . $file;
            }
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="space-y-8">
    <!-- Alerts -->
    <?php if ($success !== ''): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm text-sm" role="alert">
            <span class="font-bold">Success:</span> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm text-sm" role="alert">
            <span class="font-bold">Error:</span> <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Header Page -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Gallery Items Manager</h2>
            <p class="text-sm text-slate-500 mt-1">Manage dynamic albums, edit descriptions, tags, and select images which display on the front-end Gallery page.</p>
        </div>
        
        <button onclick="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-xl text-sm transition-all shadow-md shrink-0 flex items-center gap-2">
            ➕ Add Gallery Item
        </button>
    </div>

    <!-- Album grid -->
    <div class="space-y-8">
        <?php if (empty($galleryData)): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <span class="text-4xl block mb-3">📸</span>
                <p class="font-bold">No gallery collections or items found.</p>
                <p class="text-xs text-slate-400 mt-1">Click "Add Gallery Item" to create your first dynamic album.</p>
            </div>
        <?php else: ?>
            <?php foreach ($galleryData as $groupKey => $items): ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-bold text-xs uppercase tracking-wider">
                                Album: <?= htmlspecialchars($groupKey) ?>
                            </span>
                            <span class="text-xs text-slate-400 font-semibold">(<?= count($items) ?> items)</span>
                        </div>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        <?php foreach ($items as $item): ?>
                            <div class="py-5 first:pt-0 last:pb-0 flex flex-col md:flex-row gap-6 items-start justify-between">
                                <div class="space-y-3 flex-grow max-w-4xl">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-bold text-slate-800"><?= htmlspecialchars($item['title']) ?></h4>
                                        <span class="text-xs font-semibold text-slate-400">ID: <?= $item['id'] ?></span>
                                        <span class="text-xs text-slate-400 font-medium">| Date: <?= htmlspecialchars(date('F j, Y', strtotime($item['date']))) ?></span>
                                    </div>
                                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($item['description']) ?></p>
                                    
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        <?php if (isset($item['tags'])): ?>
                                            <?php foreach ($item['tags'] as $tag): ?>
                                                <span class="px-2.5 py-0.5 rounded bg-slate-100 text-xs text-slate-500 font-semibold border border-slate-200">
                                                    <?= htmlspecialchars($tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex flex-wrap gap-3 mt-3">
                                        <?php if (isset($item['images'])): ?>
                                            <?php foreach ($item['images'] as $img): ?>
                                                <div class="h-20 w-28 rounded-lg overflow-hidden border border-slate-200 relative group/img shrink-0 bg-slate-100">
                                                    <img src="../<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover" alt="Gallery photo">
                                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center">
                                                        <a href="../<?= htmlspecialchars($img) ?>" target="_blank" class="text-[10px] text-white font-bold underline">Zoom</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex md:flex-col gap-2 shrink-0 w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100">
                                    <button onclick='openEditModal(<?= json_encode($item) ?>, "<?= htmlspecialchars($groupKey) ?>")' class="flex-1 md:flex-initial bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold py-2 px-4 rounded-xl text-xs transition text-center">
                                        ✏️ Edit Item
                                    </button>
                                    <a href="?delete_id=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this gallery item? This will remove it from the dynamic web page.')" class="flex-1 md:flex-initial bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2 px-4 rounded-xl text-xs transition text-center block">
                                        🗑️ Delete
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Component -->
<div id="gallery-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-6 hidden flex">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-lg">✕</button>
        <h3 id="modal-heading" class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Add Gallery Album/Item</h3>
        
        <form method="POST" class="mt-6 space-y-5">
            <input type="hidden" name="item_id" id="form_item_id">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Group Key (e.g. inaguration, workshop)</label>
                    <input type="text" name="group_key" id="form_group_key" required placeholder="inaguration" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Album Date</label>
                    <input type="datetime-local" name="date" id="form_date" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Album Title</label>
                <input type="text" name="title" id="form_title" required placeholder="Inauguration of IDEA LAB - MAKERS SPACE" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Detailed Album Description</label>
                <textarea name="description" id="form_description" required placeholder="Describe the event, participants, and goals..." class="w-full border border-slate-200 p-3 rounded-xl text-sm h-32 focus:border-indigo-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Album Tags (comma separated)</label>
                <input type="text" name="tags" id="form_tags" placeholder="Program, Inaguration" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Selected Image Paths (comma separated)</label>
                <input type="text" name="images" id="form_images" required placeholder="assets/inaguration/2.jpeg" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-mono text-indigo-600 focus:border-indigo-500 focus:outline-none">
                
                <div class="mt-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-2">Quick Image Picker (Click images to select/deselect):</p>
                    <?php if (empty($availableAssets)): ?>
                        <p class="text-xs text-slate-400">No assets available. <a href="manage-assets.php" target="_blank" class="text-indigo-600 underline">Upload some assets first</a>.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-1 bg-white rounded-lg border border-slate-100">
                            <?php foreach ($availableAssets as $assetPath): ?>
                                <button type="button" onclick="toggleAssetSelection('<?= htmlspecialchars($assetPath) ?>')" data-asset-path="<?= htmlspecialchars($assetPath) ?>" class="picker-btn h-12 w-16 border-2 border-slate-200 rounded overflow-hidden shrink-0 transition relative group">
                                    <img src="../<?= htmlspecialchars($assetPath) ?>" class="w-full h-full object-cover" alt="thumbnail">
                                    <div class="absolute inset-0 bg-indigo-600/30 opacity-0 transition flex items-center justify-center picker-selected-indicator">
                                        <span class="text-white font-bold text-[10px]">&check;</span>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 px-4 rounded-lg text-xs transition">Cancel</button>
                <button type="submit" name="save_item" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow animate-duration-150">Save Album Item</button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedImages = [];

function openAddModal() {
    selectedImages = [];
    document.getElementById('form_item_id').value = '';
    document.getElementById('form_group_key').value = '';
    document.getElementById('form_date').value = '';
    document.getElementById('form_title').value = '';
    document.getElementById('form_description').value = '';
    document.getElementById('form_tags').value = '';
    document.getElementById('form_images').value = '';
    
    document.getElementById('modal-heading').textContent = "Add Gallery Album/Item";
    document.getElementById('gallery-modal').classList.remove('hidden');
    updatePickerState();
}

function openEditModal(item, groupKey) {
    selectedImages = item.images || [];
    document.getElementById('form_item_id').value = item.id;
    document.getElementById('form_group_key').value = groupKey;
    
    if (item.date) {
        const dateObj = new Date(item.date);
        const localISO = new Date(dateObj.getTime() - (dateObj.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
        document.getElementById('form_date').value = localISO;
    } else {
        document.getElementById('form_date').value = '';
    }
    
    document.getElementById('form_title').value = item.title;
    document.getElementById('form_description').value = item.description;
    document.getElementById('form_tags').value = (item.tags || []).join(', ');
    document.getElementById('form_images').value = selectedImages.join(', ');
    
    document.getElementById('modal-heading').textContent = "Edit Gallery Album/Item (ID: " + item.id + ")";
    document.getElementById('gallery-modal').classList.remove('hidden');
    updatePickerState();
}

function closeModal() {
    document.getElementById('gallery-modal').classList.add('hidden');
}

function toggleAssetSelection(assetPath) {
    const idx = selectedImages.indexOf(assetPath);
    if (idx === -1) {
        selectedImages.push(assetPath);
    } else {
        selectedImages.splice(idx, 1);
    }
    document.getElementById('form_images').value = selectedImages.join(', ');
    updatePickerState();
}

function updatePickerState() {
    document.querySelectorAll('.picker-btn').forEach(btn => {
        btn.classList.remove('border-indigo-600');
        btn.classList.add('border-slate-200');
        btn.querySelector('.picker-selected-indicator').classList.add('opacity-0');
        btn.querySelector('.picker-selected-indicator').classList.remove('opacity-100');
        
        const path = btn.getAttribute('data-asset-path');
        if (selectedImages.includes(path)) {
            btn.classList.remove('border-slate-200');
            btn.classList.add('border-indigo-600');
            btn.querySelector('.picker-selected-indicator').classList.remove('opacity-0');
            btn.querySelector('.picker-selected-indicator').classList.add('opacity-100');
        }
    });
}
</script>

<?php include __DIR__ . '/footer.php'; ?>