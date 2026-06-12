<?php
/**
 * HOMEPAGE SECTIONS & BLOCKS MANAGER (SECURITY ENHANCED)
 * 
 * Includes strict CSRF validation, output sanitization, and responsive admin tabs.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logs_helper.php';
requireAdminLogin();

$pageTitle = "Homepage Section Editor - " . ADMIN_TITLE;
$activePage = 'homepage';

$homepageFile = dirname(__DIR__) . '/data/homepage.json';

// Load active homepage dataset
$homepageData = [];
if (file_exists($homepageFile)) {
    $homepageData = json_decode(file_get_contents($homepageFile), true) ?? [];
}

$success = '';
$error = '';

// Helper to save JSON
function saveHomepage($data) {
    global $homepageFile;
    return file_put_contents($homepageFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Check which tab is active
$activeTab = isset($_GET['tab']) ? sanitizeInput($_GET['tab']) : 'hero';

// ---------------------------------------------------------------------
// 1. CSRF Token Validation
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die('CSRF token validation failed. Unauthorized operation.');
    }
}

// ---------------------------------------------------------------------
// 2. POST Request Handler
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_section'])) {
    $section = isset($_POST['section_type']) ? sanitizeInput($_POST['section_type']) : '';
    
    if ($section === 'hero') {
        $homepageData['hero']['badge'] = sanitizeInput($_POST['badge']);
        $homepageData['hero']['title'] = trim($_POST['title']); // Allow raw tags like <br />
        $homepageData['hero']['subtitle'] = sanitizeInput($_POST['subtitle']);
        $homepageData['hero']['cta_primary_text'] = sanitizeInput($_POST['cta_primary_text']);
        $homepageData['hero']['cta_primary_link'] = sanitizeInput($_POST['cta_primary_link']);
        $homepageData['hero']['cta_secondary_text'] = sanitizeInput($_POST['cta_secondary_text']);
        $homepageData['hero']['cta_secondary_link'] = sanitizeInput($_POST['cta_secondary_link']);
        $homepageData['hero']['visual_type'] = sanitizeInput($_POST['visual_type']); // "image" or "video"
        $homepageData['hero']['visual_url'] = sanitizeInput($_POST['visual_url']);
        
        saveHomepage($homepageData);
        $success = "Hero section saved successfully!";
        addLog('Updated Homepage Hero', 'Visual: ' . $homepageData['hero']['visual_type'] . ', URL: ' . $homepageData['hero']['visual_url']);
        
    } elseif ($section === 'about') {
        $homepageData['about']['title'] = sanitizeInput($_POST['about_title']);
        $homepageData['about']['description'] = sanitizeInput($_POST['about_desc']);
        $homepageData['about']['image'] = sanitizeInput($_POST['about_image']);
        
        // 3 Pillars
        $homepageData['about']['pillars'] = [
            [
                'num' => sanitizeInput($_POST['p1_num']),
                'title' => sanitizeInput($_POST['p1_title']),
                'desc' => sanitizeInput($_POST['p1_desc'])
            ],
            [
                'num' => sanitizeInput($_POST['p2_num']),
                'title' => sanitizeInput($_POST['p2_title']),
                'desc' => sanitizeInput($_POST['p2_desc'])
            ],
            [
                'num' => sanitizeInput($_POST['p3_num']),
                'title' => sanitizeInput($_POST['p3_title']),
                'desc' => sanitizeInput($_POST['p3_desc'])
            ]
        ];
        
        saveHomepage($homepageData);
        $success = "About section and pillars saved successfully!";
        addLog('Updated About Section', 'Edited description and key pillars.');
        
    } elseif ($section === 'facilities_lifecycle') {
        // Facilities headers
        $homepageData['facilities_header']['title'] = sanitizeInput($_POST['fac_head_title']);
        $homepageData['facilities_header']['subtitle'] = sanitizeInput($_POST['fac_head_subtitle']);
        
        // Facilities entries (6 items)
        for ($i = 0; $i < 6; $i++) {
            $key = sanitizeInput($_POST["fac_key_$i"]);
            $homepageData['facilities'][$i] = [
                'key' => $key,
                'title' => sanitizeInput($_POST["fac_title_$i"]),
                'subtitle' => sanitizeInput($_POST["fac_subtitle_$i"])
            ];
        }
        
        // Lifecycle headers
        $homepageData['lifecycle_header']['title'] = sanitizeInput($_POST['life_head_title']);
        $homepageData['lifecycle_header']['subtitle'] = sanitizeInput($_POST['life_head_subtitle']);
        
        // Lifecycle entries (5 items)
        for ($i = 0; $i < 5; $i++) {
            $homepageData['lifecycle'][$i] = [
                'step' => sanitizeInput($_POST["life_step_$i"]),
                'title' => sanitizeInput($_POST["life_title_$i"]),
                'desc' => sanitizeInput($_POST["life_desc_$i"])
            ];
        }
        
        saveHomepage($homepageData);
        $success = "Facilities and Innovation Lifecycle saved successfully!";
        addLog('Updated Facilities/Lifecycle', 'Saved facilities and lifecycle cards configuration.');
        
    } elseif ($section === 'programs_outcomes') {
        // Programs headers
        $homepageData['programs_header']['title'] = sanitizeInput($_POST['prog_head_title']);
        $homepageData['programs_header']['subtitle'] = sanitizeInput($_POST['prog_head_subtitle']);
        
        // Programs entries (5 items)
        for ($i = 0; $i < 5; $i++) {
            $homepageData['programs'][$i] = [
                'num' => sanitizeInput($_POST["prog_num_$i"]),
                'title' => sanitizeInput($_POST["prog_title_$i"]),
                'desc' => sanitizeInput($_POST["prog_desc_$i"])
            ];
        }
        
        // Outcomes headers
        $homepageData['outcomes_header']['title'] = sanitizeInput($_POST['out_head_title']);
        $homepageData['outcomes_header']['subtitle'] = sanitizeInput($_POST['out_head_subtitle']);
        
        // Outcomes entries (4 items)
        for ($i = 0; $i < 4; $i++) {
            $homepageData['outcomes'][$i] = [
                'title' => sanitizeInput($_POST["out_title_$i"]),
                'desc' => sanitizeInput($_POST["out_desc_$i"])
            ];
        }
        
        $homepageData['outcomes_footer'] = sanitizeInput($_POST['out_footer']);
        
        saveHomepage($homepageData);
        $success = "Programs and outcomes saved successfully!";
        addLog('Updated Programs/Outcomes', 'Updated training programs and outcomes configuration.');
        
    } elseif ($section === 'contact') {
        $homepageData['contact']['title'] = sanitizeInput($_POST['contact_title']);
        $homepageData['contact']['subtitle'] = sanitizeInput($_POST['contact_subtitle']);
        $homepageData['contact']['address'] = sanitizeInput($_POST['contact_address']);
        $homepageData['contact']['coordinator_email'] = sanitizeInput($_POST['coordinator_email']);
        $homepageData['contact']['cocoordinator_email'] = sanitizeInput($_POST['cocoordinator_email']);
        $homepageData['contact']['coordinator_phone'] = sanitizeInput($_POST['coordinator_phone']);
        $homepageData['contact']['cocoordinator_phone'] = sanitizeInput($_POST['cocoordinator_phone']);
        $homepageData['contact']['map_embed'] = trim($_POST['map_embed']); // Keep map URLs intact
        
        saveHomepage($homepageData);
        $success = "Contact and map embed details saved successfully!";
        addLog('Updated Contact Info', 'Address/emails/phones details saved.');
    }
}

// ---------------------------------------------------------------------
// 3. PROJECT SUB-ACTIONS (Add/Edit/Delete active projects)
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_project'])) {
    $project_id = sanitizeInput($_POST['project_id']);
    $category = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($_POST['category'])));
    $category_label = sanitizeInput($_POST['category_label']);
    $title = sanitizeInput($_POST['project_title']);
    $desc = sanitizeInput($_POST['project_desc']);
    $image = sanitizeInput($_POST['project_image']);
    
    if (!isset($homepageData['projects']) || !is_array($homepageData['projects'])) {
        $homepageData['projects'] = [];
    }
    
    if ($project_id !== '') {
        // Edit active project
        $found = false;
        foreach ($homepageData['projects'] as &$proj) {
            if ($proj['id'] === $project_id) {
                $proj['category'] = $category;
                $proj['category_label'] = $category_label;
                $proj['title'] = $title;
                $proj['desc'] = $desc;
                $proj['image'] = $image;
                $found = true;
                break;
            }
        }
        if ($found) {
            saveHomepage($homepageData);
            $success = "Project card updated successfully!";
            addLog('Updated Project', 'Edited project details for: "' . $title . '".');
        } else {
            $error = "Project ID not found.";
        }
    } else {
        // Add new project card
        $new_id = 'proj-' . time();
        $newProj = [
            'id' => $new_id,
            'category' => $category,
            'category_label' => $category_label,
            'title' => $title,
            'desc' => $desc,
            'image' => $image
        ];
        $homepageData['projects'][] = $newProj;
        saveHomepage($homepageData);
        $success = "New project card added successfully!";
        addLog('Added Project Card', 'Created project card: "' . $title . '".');
    }
}

if (isset($_GET['delete_project'])) {
    $projDelId = sanitizeInput($_GET['delete_project']);
    $found = false;
    if (isset($homepageData['projects']) && is_array($homepageData['projects'])) {
        foreach ($homepageData['projects'] as $idx => $proj) {
            if ($proj['id'] === $projDelId) {
                $pTitle = $proj['title'];
                unset($homepageData['projects'][$idx]);
                $homepageData['projects'] = array_values($homepageData['projects']);
                saveHomepage($homepageData);
                $found = true;
                $success = "Project card removed successfully.";
                addLog('Deleted Project Card', 'Removed project card: "' . $pTitle . '".');
                break;
            }
        }
    }
    if (!$found) $error = "Project card not found or already deleted.";
}

// ---------------------------------------------------------------------
// 4. MENTOR SUB-ACTIONS (Add/Edit/Delete mentors)
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_mentor'])) {
    $mentor_name_orig = sanitizeInput($_POST['mentor_orig_name']);
    $name = sanitizeInput($_POST['mentor_name']);
    $role = sanitizeInput($_POST['mentor_role']);
    $image = sanitizeInput($_POST['mentor_image']);
    $linkedin = sanitizeInput($_POST['mentor_linkedin']);
    
    if (!isset($homepageData['mentors']) || !is_array($homepageData['mentors'])) {
        $homepageData['mentors'] = [];
    }
    
    if ($mentor_name_orig !== '') {
        // Edit active mentor
        $found = false;
        foreach ($homepageData['mentors'] as &$men) {
            if ($men['name'] === $mentor_name_orig) {
                $men['name'] = $name;
                $men['role'] = $role;
                $men['image'] = $image;
                $men['linkedin'] = $linkedin;
                $found = true;
                break;
            }
        }
        if ($found) {
            saveHomepage($homepageData);
            $success = "Mentor record updated successfully!";
            addLog('Updated Mentor', 'Edited profile for: "' . $name . '".');
        } else {
            $error = "Mentor records matching the original name not found.";
        }
    } else {
        // Add new mentor card
        $newMen = [
            'name' => $name,
            'role' => $role,
            'image' => $image,
            'linkedin' => $linkedin
        ];
        $homepageData['mentors'][] = $newMen;
        saveHomepage($homepageData);
        $success = "New mentor card added successfully!";
        addLog('Added Mentor', 'Created profile for: "' . $name . '".');
    }
}

if (isset($_GET['delete_mentor'])) {
    $menDelName = sanitizeInput($_GET['delete_mentor']);
    $found = false;
    if (isset($homepageData['mentors']) && is_array($homepageData['mentors'])) {
        foreach ($homepageData['mentors'] as $idx => $men) {
            if ($men['name'] === $menDelName) {
                unset($homepageData['mentors'][$idx]);
                $homepageData['mentors'] = array_values($homepageData['mentors']);
                saveHomepage($homepageData);
                $found = true;
                $success = "Mentor card removed successfully.";
                addLog('Deleted Mentor', 'Removed profile for: "' . $menDelName . '".');
                break;
            }
        }
    }
    if (!$found) $error = "Mentor record not found or already deleted.";
}

// Fetch images recursively to populate select helpers
$availableAssets = [];
if (is_dir(UPLOADS_DIR)) {
    $files = scandir(UPLOADS_DIR);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..'])) continue;
        $filePath = UPLOADS_DIR . $file;
        if (is_file($filePath)) {
            $availableAssets[] = 'assets/' . $file;
        }
    }
}

include __DIR__ . '/header.php';
?>

<div class="space-y-8">
    <!-- Feedback Alerts -->
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

    <!-- Horizontal Tabs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap gap-2">
        <a href="?tab=hero" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'hero' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            🚀 Hero & Partners
        </a>
        <a href="?tab=about" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'about' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            💡 About & Pillars
        </a>
        <a href="?tab=projects" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'projects' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            🛠️ Project Cards
        </a>
        <a href="?tab=facilities" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'facilities' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            🏗️ Facilities & Stages
        </a>
        <a href="?tab=mentors" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'mentors' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            👥 Leadership & Mentors
        </a>
        <a href="?tab=programs" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'programs' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            🎓 Programs & Outcomes
        </a>
        <a href="?tab=contact" class="px-5 py-2.5 rounded-xl text-sm font-bold transition <?= $activeTab === 'contact' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' ?>">
            📞 Contact Details
        </a>
    </div>

    <!-- TAB 1: HERO & PARTNERS -->
    <?php if ($activeTab === 'hero'): ?>
        <form method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
            <input type="hidden" name="section_type" value="hero">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Edit Hero Section Visuals & Text</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Badge Subtitle Text</label>
                    <input type="text" name="badge" value="<?= htmlspecialchars($homepageData['hero']['badge'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Primary CTA Text</label>
                    <input type="text" name="cta_primary_text" value="<?= htmlspecialchars($homepageData['hero']['cta_primary_text'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Primary CTA Link</label>
                    <input type="text" name="cta_primary_link" value="<?= htmlspecialchars($homepageData['hero']['cta_primary_link'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Secondary CTA Text</label>
                    <input type="text" name="cta_secondary_text" value="<?= htmlspecialchars($homepageData['hero']['cta_secondary_text'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Secondary CTA Link</label>
                    <input type="text" name="cta_secondary_link" value="<?= htmlspecialchars($homepageData['hero']['cta_secondary_link'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Hero Title (HTML tags like &lt;br /&gt; allowed)</label>
                <input type="text" name="title" value="<?= htmlspecialchars($homepageData['hero']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-semibold focus:border-indigo-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Hero Paragraph Subtext</label>
                <textarea name="subtitle" class="w-full border border-slate-200 p-3 rounded-xl text-sm h-24 focus:border-indigo-500 focus:outline-none"><?= htmlspecialchars($homepageData['hero']['subtitle'] ?? '') ?></textarea>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-6">
                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">Hero Visual Manager</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Hero Visual Type</label>
                        <select name="visual_type" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="image" <?= (isset($homepageData['hero']['visual_type']) && $homepageData['hero']['visual_type'] === 'image') ? 'selected' : '' ?>>Image</option>
                            <option value="video" <?= (isset($homepageData['hero']['visual_type']) && $homepageData['hero']['visual_type'] === 'video') ? 'selected' : '' ?>>Video (Autoplay loop)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Visual Source Link (relative to site root, e.g. assets/heroImage.png)</label>
                        <input type="text" name="visual_url" id="hero_visual_url" value="<?= htmlspecialchars($homepageData['hero']['visual_url'] ?? '') ?>" placeholder="assets/heroImage.png" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-mono focus:border-indigo-500 focus:outline-none">
                        
                        <?php if (!empty($availableAssets)): ?>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="text-[10px] text-slate-400 font-bold uppercase self-center mr-1">Quick Select:</span>
                                <?php foreach (array_slice($availableAssets, 0, 8) as $asset): ?>
                                    <button type="button" onclick="document.getElementById('hero_visual_url').value='<?= $asset ?>'" class="px-2 py-0.5 bg-slate-100 hover:bg-indigo-100 rounded text-[10px] text-slate-500 font-mono transition border border-slate-200"><?= basename($asset) ?></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" name="save_section" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-md">
                    Save Hero Changes
                </button>
            </div>
        </form>
    <?php endif; ?>

    <!-- TAB 2: ABOUT & PILLARS -->
    <?php if ($activeTab === 'about'): ?>
        <form method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
            <input type="hidden" name="section_type" value="about">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Edit About Section & Main Ideals</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Heading</label>
                    <input type="text" name="about_title" value="<?= htmlspecialchars($homepageData['about']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Illustration Image</label>
                    <input type="text" name="about_image" id="about_image_url" value="<?= htmlspecialchars($homepageData['about']['image'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-mono focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Detailed Text</label>
                <textarea name="about_desc" class="w-full border border-slate-200 p-3 rounded-xl text-sm h-28 focus:border-indigo-500 focus:outline-none"><?= htmlspecialchars($homepageData['about']['description'] ?? '') ?></textarea>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4">Edit Pillars</h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Pillar 1 -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="p1_num" value="<?= htmlspecialchars($homepageData['about']['pillars'][0]['num'] ?? '01') ?>" class="w-16 border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                            <input type="text" name="p1_title" value="<?= htmlspecialchars($homepageData['about']['pillars'][0]['title'] ?? '') ?>" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                        </div>
                        <textarea name="p1_desc" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs h-16"><?= htmlspecialchars($homepageData['about']['pillars'][0]['desc'] ?? '') ?></textarea>
                    </div>

                    <!-- Pillar 2 -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="p2_num" value="<?= htmlspecialchars($homepageData['about']['pillars'][1]['num'] ?? '02') ?>" class="w-16 border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                            <input type="text" name="p2_title" value="<?= htmlspecialchars($homepageData['about']['pillars'][1]['title'] ?? '') ?>" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                        </div>
                        <textarea name="p2_desc" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs h-16"><?= htmlspecialchars($homepageData['about']['pillars'][1]['desc'] ?? '') ?></textarea>
                    </div>

                    <!-- Pillar 3 -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                        <div class="flex gap-2">
                            <input type="text" name="p3_num" value="<?= htmlspecialchars($homepageData['about']['pillars'][2]['num'] ?? '03') ?>" class="w-16 border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                            <input type="text" name="p3_title" value="<?= htmlspecialchars($homepageData['about']['pillars'][2]['title'] ?? '') ?>" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs font-bold">
                        </div>
                        <textarea name="p3_desc" class="w-full border border-slate-200 p-2.5 rounded-lg text-xs h-16"><?= htmlspecialchars($homepageData['about']['pillars'][2]['desc'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" name="save_section" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-md">
                    Save About Changes
                </button>
            </div>
        </form>
    <?php endif; ?>

    <!-- TAB 3: PROJECT CARDS -->
    <?php if ($activeTab === 'projects'): ?>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Projects & Innovations Cards</h3>
                    <p class="text-sm text-slate-500 mt-1">Manage active project cards displayed on the index page.</p>
                </div>
                
                <button onclick="openProjectModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow-md shrink-0">
                    ➕ Add Project Card
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                $projectsList = $homepageData['projects'] ?? [];
                if (empty($projectsList)): 
                ?>
                    <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                        <span class="text-3xl block mb-2">💡</span>
                        <p class="font-bold">No project cards configured.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projectsList as $proj): ?>
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between group">
                            <div class="h-40 bg-slate-100 relative overflow-hidden border-b border-slate-100">
                                <img src="../<?= htmlspecialchars($proj['image']) ?>" class="w-full h-full object-cover">
                                <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-full bg-black/60 text-[10px] font-bold text-white uppercase backdrop-blur-sm">
                                    <?= htmlspecialchars($proj['category_label'] ?? ucwords($proj['category'])) ?>
                                </span>
                            </div>

                            <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-base line-clamp-1"><?= htmlspecialchars($proj['title']) ?></h4>
                                    <p class="text-xs text-slate-400 mt-0.5">Category: <span class="font-bold"><?= htmlspecialchars($proj['category']) ?></span></p>
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-3"><?= htmlspecialchars($proj['desc']) ?></p>
                                </div>

                                <div class="flex gap-2 pt-3 border-t border-slate-50">
                                    <button onclick='openEditProjectModal(<?= json_encode($proj) ?>)' class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold py-2 rounded-lg text-xs transition text-center">
                                        ✏️ Edit
                                    </button>
                                    <a href="?tab=projects&delete_project=<?= urlencode($proj['id']) ?>" onclick="return confirm('Are you sure you want to remove this project card?')" class="flex-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2 rounded-lg text-xs transition text-center block">
                                        🗑️ Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Project Modal -->
        <div id="project-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-6 hidden flex">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl p-6 relative">
                <button onclick="closeProjectModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
                <h3 id="project-modal-heading" class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Add Project Card</h3>
                
                <form method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="project_id" id="proj_form_id">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Filter Key (e.g. iot)</label>
                            <input type="text" name="category" id="proj_form_category" required class="w-full border border-slate-200 p-2.5 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Filter Badge Label</label>
                            <input type="text" name="category_label" id="proj_form_label" required class="w-full border border-slate-200 p-2.5 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Project Title</label>
                        <input type="text" name="project_title" id="proj_form_title" required class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Project Description</label>
                        <textarea name="project_desc" id="proj_form_desc" required class="w-full border border-slate-200 p-3 rounded-xl text-sm h-24 focus:border-indigo-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Project Cover Image Link</label>
                        <input type="text" name="project_image" id="proj_form_image" required class="w-full border border-slate-200 p-3 rounded-xl text-sm font-mono text-indigo-600 focus:border-indigo-500 focus:outline-none">
                        
                        <?php if (!empty($availableAssets)): ?>
                            <div class="mt-2 flex flex-wrap gap-1">
                                <?php foreach (array_slice($availableAssets, 0, 5) as $asset): ?>
                                    <button type="button" onclick="document.getElementById('proj_form_image').value='<?= $asset ?>'" class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-mono border text-slate-500 hover:bg-indigo-50"><?= basename($asset) ?></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" onclick="closeProjectModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 px-4 rounded-lg text-xs transition">Cancel</button>
                        <button type="submit" name="save_project" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow">Save Project Card</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openProjectModal() {
            document.getElementById('proj_form_id').value = '';
            document.getElementById('proj_form_category').value = '';
            document.getElementById('proj_form_label').value = '';
            document.getElementById('proj_form_title').value = '';
            document.getElementById('proj_form_desc').value = '';
            document.getElementById('proj_form_image').value = '';
            
            document.getElementById('project-modal-heading').textContent = "Add Project Card";
            document.getElementById('project-modal').classList.remove('hidden');
        }

        function openEditProjectModal(proj) {
            document.getElementById('proj_form_id').value = proj.id;
            document.getElementById('proj_form_category').value = proj.category;
            document.getElementById('proj_form_label').value = proj.category_label || '';
            document.getElementById('proj_form_title').value = proj.title;
            document.getElementById('proj_form_desc').value = proj.desc;
            document.getElementById('proj_form_image').value = proj.image;
            
            document.getElementById('project-modal-heading').textContent = "Edit Project Card";
            document.getElementById('project-modal').classList.remove('hidden');
        }

        function closeProjectModal() {
            document.getElementById('project-modal').classList.add('hidden');
        }
        </script>
    <?php endif; ?>

    <!-- TAB 4: FACILITIES & LIFECYCLES -->
    <?php if ($activeTab === 'facilities'): ?>
        <form method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">
            <input type="hidden" name="section_type" value="facilities_lifecycle">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Facilities Header</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="fac_head_title" value="<?= htmlspecialchars($homepageData['facilities_header']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Subtitle</label>
                        <input type="text" name="fac_head_subtitle" value="<?= htmlspecialchars($homepageData['facilities_header']['subtitle'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-6">
                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4">Edit Infrastructure Cards (exactly 6)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php 
                    $facilities = $homepageData['facilities'] ?? [];
                    for ($i = 0; $i < 6; $i++):
                        $fac = $facilities[$i] ?? ['key' => '', 'title' => '', 'subtitle' => ''];
                    ?>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl space-y-3">
                            <span class="text-xs font-bold text-slate-400">Card #<?= $i + 1 ?></span>
                            <div>
                                <input type="hidden" name="fac_key_<?= $i ?>" value="<?= htmlspecialchars($fac['key']) ?>">
                                <input type="text" name="fac_title_<?= $i ?>" value="<?= htmlspecialchars($fac['title']) ?>" class="w-full border border-slate-200 p-2 rounded-lg text-xs font-bold">
                            </div>
                            <textarea name="fac_subtitle_<?= $i ?>" class="w-full border border-slate-200 p-2 rounded-lg text-xs h-16"><?= htmlspecialchars($fac['subtitle']) ?></textarea>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="space-y-6 border-t border-slate-100 pt-6">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Innovation Lifecycle Config</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="life_head_title" value="<?= htmlspecialchars($homepageData['lifecycle_header']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Subtitle</label>
                        <input type="text" name="life_head_subtitle" value="<?= htmlspecialchars($homepageData['lifecycle_header']['subtitle'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <?php 
                    $lifecycle = $homepageData['lifecycle'] ?? [];
                    for ($i = 0; $i < 5; $i++):
                        $life = $lifecycle[$i] ?? ['step' => ($i + 1), 'title' => '', 'desc' => ''];
                    ?>
                        <div class="bg-indigo-50/20 border border-indigo-100 p-3 rounded-xl space-y-2">
                            <input type="hidden" name="life_step_<?= $i ?>" value="<?= htmlspecialchars($life['step']) ?>">
                            <input type="text" name="life_title_<?= $i ?>" value="<?= htmlspecialchars($life['title']) ?>" class="w-full border border-slate-200 p-2 rounded-lg text-xs font-bold">
                            <textarea name="life_desc_<?= $i ?>" class="w-full border border-slate-200 p-2 rounded-lg text-[10px] h-16 leading-tight"><?= htmlspecialchars($life['desc']) ?></textarea>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" name="save_section" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-md">
                    Save Facilities & Lifecycle Config
                </button>
            </div>
        </form>
    <?php endif; ?>

    <!-- TAB 5: LEADERSHIP & MENTORS -->
    <?php if ($activeTab === 'mentors'): ?>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Guiding Leadership & Tech Mentors</h3>
                    <p class="text-sm text-slate-500 mt-1">Manage leadership mentors showing on the landing page.</p>
                </div>
                
                <button onclick="openMentorModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow-md shrink-0">
                    ➕ Add Mentor Profile
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php 
                $mentorsList = $homepageData['mentors'] ?? [];
                if (empty($mentorsList)):
                ?>
                    <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                        <span class="text-3xl block mb-2">👥</span>
                        <p class="font-bold">No mentors profile configured.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($mentorsList as $men): ?>
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 text-center shadow-sm hover:shadow-md transition flex flex-col justify-between h-80 group">
                            <div class="space-y-3">
                                <img src="../<?= htmlspecialchars($men['image']) ?>" class="mx-auto h-24 w-24 rounded-full object-cover border-2 border-indigo-100 bg-slate-50">
                                <h4 class="font-bold text-slate-800 text-base line-clamp-1"><?= htmlspecialchars($men['name']) ?></h4>
                                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider"><?= htmlspecialchars($men['role']) ?></p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-50 space-y-2">
                                <a href="<?= htmlspecialchars($men['linkedin']) ?>" target="_blank" class="text-xs text-indigo-600 hover:underline block font-semibold">LinkedIn Profile &rarr;</a>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <button onclick='openEditMentorModal(<?= json_encode($men) ?>)' class="bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-1.5 rounded-lg text-[10px] transition">Edit</button>
                                    <a href="?tab=mentors&delete_mentor=<?= urlencode($men['name']) ?>" onclick="return confirm('Are you sure you want to remove this mentor profile?')" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-1.5 rounded-lg text-[10px] transition text-center block">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mentor Modal -->
        <div id="mentor-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-6 hidden flex">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 relative">
                <button onclick="closeMentorModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
                <h3 id="mentor-modal-heading" class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Add Mentor Profile</h3>
                
                <form method="POST" class="mt-4 space-y-4">
                    <input type="hidden" name="mentor_orig_name" id="men_form_orig_name">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Mentor Name</label>
                        <input type="text" name="mentor_name" id="men_form_name" required class="w-full border border-slate-200 p-2.5 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Role/Title</label>
                        <input type="text" name="mentor_role" id="men_form_role" required class="w-full border border-slate-200 p-2.5 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">LinkedIn URL</label>
                        <input type="text" name="mentor_linkedin" id="men_form_linkedin" class="w-full border border-slate-200 p-2.5 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Profile Image Link</label>
                        <input type="text" name="mentor_image" id="men_form_image" required class="w-full border border-slate-200 p-2.5 rounded-xl text-sm font-mono text-indigo-600 focus:border-indigo-500 focus:outline-none">
                        
                        <?php if (!empty($availableAssets)): ?>
                            <div class="mt-2 flex flex-wrap gap-1">
                                <?php foreach (array_slice($availableAssets, 0, 5) as $asset): ?>
                                    <button type="button" onclick="document.getElementById('men_form_image').value='<?= $asset ?>'" class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-mono border text-slate-500 hover:bg-indigo-50"><?= basename($asset) ?></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" onclick="closeMentorModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 px-4 rounded-lg text-xs transition">Cancel</button>
                        <button type="submit" name="save_mentor" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition shadow">Save Mentor Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openMentorModal() {
            document.getElementById('men_form_orig_name').value = '';
            document.getElementById('men_form_name').value = '';
            document.getElementById('men_form_role').value = '';
            document.getElementById('men_form_linkedin').value = '#';
            document.getElementById('men_form_image').value = '';
            
            document.getElementById('mentor-modal-heading').textContent = "Add Mentor Profile";
            document.getElementById('mentor-modal').classList.remove('hidden');
        }

        function openEditMentorModal(men) {
            document.getElementById('men_form_orig_name').value = men.name;
            document.getElementById('men_form_name').value = men.name;
            document.getElementById('men_form_role').value = men.role;
            document.getElementById('men_form_linkedin').value = men.linkedin || '#';
            document.getElementById('men_form_image').value = men.image;
            
            document.getElementById('mentor-modal-heading').textContent = "Edit Mentor Profile";
            document.getElementById('mentor-modal').classList.remove('hidden');
        }

        function closeMentorModal() {
            document.getElementById('mentor-modal').classList.add('hidden');
        }
        </script>
    <?php endif; ?>

    <!-- TAB 6: PROGRAMS & OUTCOMES -->
    <?php if ($activeTab === 'programs'): ?>
        <form method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">
            <input type="hidden" name="section_type" value="programs_outcomes">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Programs Headers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="prog_head_title" value="<?= htmlspecialchars($homepageData['programs_header']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Subtitle</label>
                        <input type="text" name="prog_head_subtitle" value="<?= htmlspecialchars($homepageData['programs_header']['subtitle'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-4">Edit Continuous Learning Cards (exactly 5)</h4>
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    <?php 
                    $programs = $homepageData['programs'] ?? [];
                    for ($i = 0; $i < 5; $i++):
                        $prog = $programs[$i] ?? ['num' => ($i + 1), 'title' => '', 'desc' => ''];
                    ?>
                        <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl space-y-2">
                            <span class="text-xs font-bold text-slate-400">Card #<?= $i + 1 ?></span>
                            <input type="hidden" name="prog_num_<?= $i ?>" value="<?= htmlspecialchars($prog['num']) ?>">
                            <input type="text" name="prog_title_<?= $i ?>" value="<?= htmlspecialchars($prog['title']) ?>" class="w-full border border-slate-200 p-2 rounded-lg text-xs font-bold">
                            <textarea name="prog_desc_<?= $i ?>" class="w-full border border-slate-200 p-2 rounded-lg text-[10px] h-16 leading-tight"><?= htmlspecialchars($prog['desc']) ?></textarea>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Outcomes & Impact Config</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="out_head_title" value="<?= htmlspecialchars($homepageData['outcomes_header']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Subtitle</label>
                        <input type="text" name="out_head_subtitle" value="<?= htmlspecialchars($homepageData['outcomes_header']['subtitle'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 pt-4">
                <?php 
                $outcomes = $homepageData['outcomes'] ?? [];
                for ($i = 0; $i < 4; $i++):
                    $out = $outcomes[$i] ?? ['title' => '', 'desc' => ''];
                ?>
                    <div class="bg-indigo-50/20 border border-indigo-100 p-3 rounded-xl space-y-2">
                        <span class="text-xs font-bold text-indigo-500">Outcome Card #<?= $i + 1 ?></span>
                        <input type="text" name="out_title_<?= $i ?>" value="<?= htmlspecialchars($out['title']) ?>" class="w-full border border-slate-200 p-2 rounded-lg text-xs font-bold">
                        <textarea name="out_desc_<?= $i ?>" class="w-full border border-slate-200 p-2 rounded-lg text-[10px] h-16 leading-tight"><?= htmlspecialchars($out['desc']) ?></textarea>
                    </div>
                <?php endfor; ?>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Impact Note Footer Statement</label>
                <textarea name="out_footer" class="w-full border border-slate-200 p-3 rounded-xl text-sm h-20 focus:border-indigo-500 focus:outline-none"><?= htmlspecialchars($homepageData['outcomes_footer'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" name="save_section" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-md">
                    Save Programs & Outcomes
                </button>
            </div>
        </form>
    <?php endif; ?>

    <!-- TAB 7: CONTACT DETAILS -->
    <?php if ($activeTab === 'contact'): ?>
        <form method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-6">
            <input type="hidden" name="section_type" value="contact">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Edit Contact Section, Phone, Emails & Map Embeds</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Heading Title</label>
                    <input type="text" name="contact_title" value="<?= htmlspecialchars($homepageData['contact']['title'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section Heading Subtitle</label>
                    <input type="text" name="contact_subtitle" value="<?= htmlspecialchars($homepageData['contact']['subtitle'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Physical Address Details</label>
                <input type="text" name="contact_address" value="<?= htmlspecialchars($homepageData['contact']['address'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div>
                    <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-3">Email Configuration</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Coordinator Email</label>
                            <input type="email" name="coordinator_email" value="<?= htmlspecialchars($homepageData['contact']['coordinator_email'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Co-coordinator Email</label>
                            <input type="email" name="cocoordinator_email" value="<?= htmlspecialchars($homepageData['contact']['cocoordinator_email'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-3">Phone Numbers</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Coordinator Phone No.</label>
                            <input type="text" name="coordinator_phone" value="<?= htmlspecialchars($homepageData['contact']['coordinator_phone'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 mb-1.5">Co-coordinator Phone No.</label>
                            <input type="text" name="cocoordinator_phone" value="<?= htmlspecialchars($homepageData['contact']['cocoordinator_phone'] ?? '') ?>" class="w-full border border-slate-200 p-3 rounded-xl text-sm focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Google Map Embed Link (iframe src="..." URL)</label>
                <textarea name="map_embed" class="w-full border border-slate-200 p-3 rounded-xl text-sm font-mono text-indigo-600 h-24 focus:border-indigo-500 focus:outline-none"><?= htmlspecialchars($homepageData['contact']['map_embed'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" name="save_section" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-md">
                    Save Contact Details
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>