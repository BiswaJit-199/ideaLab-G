<?php
/**
 * DYNAMIC MODULAR HOMEPAGE (SEO & PERFORMANCE OPTIMIZED)
 * 
 * Architecture: Clean, modular partials included dynamically. 
 * Performance: Deferred rendering-blocking Javascript, lightweight structured templates.
 * SEO: Open Graph integration, semantic HTML, schema structure.
 */
$homepageFile = __DIR__ . '/data/homepage.json';
if (file_exists($homepageFile)) {
    $homepageData = json_decode(file_get_contents($homepageFile), true);
} else {
    $homepageData = [];
}

// SEO Meta Variables (will be parsed inside header.php)
$pageTitle = "IdeaLab | GIFT - AICTE Approved Innovation Ecosystem";
$metaDescription = "Explore our AICTE Approved IDEA Lab at Gandhi Institute for Technology (GIFT) Bhubaneswar. Discover high-end prototyping tools, IoT kits, 3D printing, and student startups.";
$canonical = "https://idealab.gift.edu.in/";

include "./header.php";

// Safe dynamic JSON path extractor
function getHData($path, $default = '') {
    global $homepageData;
    $keys = explode('.', $path);
    $current = $homepageData;
    foreach ($keys as $key) {
        if (isset($current[$key])) {
            $current = $current[$key];
        } else {
            return $default;
        }
    }
    return $current;
}
?>

<main id="main-content" class="min-h-screen">
    <?php
    // Include modular sections sequentially
    include './partials/hero.php';
    include './partials/partners.php';
    include './partials/about.php';
    include './partials/facilities.php';
    include './partials/lifecycle.php';
    include './partials/programs.php';
    include './partials/projects.php';
    include './partials/mentors.php';
    include './partials/cta.php';
    include './partials/outcomes.php';
    include './partials/contact.php';
    ?>
</main>

<!-- ACCESSIBILITY-COMPLIANT MODAL COMPONENT (FOR INFRASTRUCTURE CARDS) -->
<div id="facility-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-6 backdrop-blur-sm transition-all" aria-hidden="true" role="dialog">
    <div class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl transition">
        <button onclick="closeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Close details dialog">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <h3 id="modal-title" class="text-2xl font-bold text-slate-900 tracking-tight">Facility Detail</h3>
        <p id="modal-content" class="mt-6 text-slate-600 leading-relaxed"></p>
        <button onclick="closeModal()" class="mt-8 w-full rounded-lg bg-blue-900 py-3.5 font-bold text-white hover:bg-blue-800 transition">
            Close View
        </button>
    </div>
</div>

<!-- PERFORMANCE BEST PRACTICE: Deferred Scripts to allow instantaneous rendering (Maximize FCP and LCP scores) -->
<script src="./navbar.js" defer></script>
<script src="./main.js" defer></script>
<script src="https://unpkg.com/lucide@latest" defer onload="lucide.createIcons();"></script>

<?php include "./footer.php"; ?>