<?php
/**
 * PARTIAL: ACTIVE PROJECTS & INNOVATIONS
 * 
 * Supports dynamic filtering. Filters are compiled on-the-fly depending 
 * on categories entered inside the active projects list.
 */
?>
<section id="projects" class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Header -->
        <div class="max-w-2xl">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('projects_header.title', 'Projects & Innovations')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('projects_header.subtitle', 'Student-driven projects solving real-world challenges.')) ?>
            </p>
        </div>

        <!-- Dynamic Filter Categories (extracted on-the-fly for absolute performance/SEO) -->
        <div class="mt-12 flex flex-wrap gap-4">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php
            $projectsList = getHData('projects', []);
            $categories = [];
            foreach ($projectsList as $proj) {
                $cat = strtolower($proj['category']);
                if (!isset($categories[$cat])) {
                    $categories[$cat] = $proj['category_label'] ?? ucwords($cat);
                }
            }
            foreach ($categories as $catKey => $catLabel) {
                echo '<button class="filter-btn" data-filter="' . htmlspecialchars($catKey) . '">' . htmlspecialchars($catLabel) . '</button>';
            }
            ?>
        </div>

        <!-- Projects Cards Grid with lazy-loaded images -->
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($projectsList as $proj): ?>
                <article class="project-card" data-category="<?= htmlspecialchars(strtolower($proj['category'])) ?>">
                    <img loading="lazy" src="<?= htmlspecialchars($proj['image']) ?>" alt="Project Cover: <?= htmlspecialchars($proj['title']) ?>" class="h-48 w-full object-cover transition hover:scale-[1.02] duration-300" width="300" height="200" />
                    <div class="p-6 border-x border-b border-slate-100 rounded-b-2xl">
                        <span class="inline-block rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-950 uppercase tracking-wide">
                            <?= htmlspecialchars($proj['category_label'] ?? ucwords($proj['category'])) ?>
                        </span>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">
                            <?= htmlspecialchars($proj['title']) ?>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            <?= htmlspecialchars($proj['desc']) ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>