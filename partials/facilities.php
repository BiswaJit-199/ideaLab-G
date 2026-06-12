<?php
/**
 * PARTIAL: LAB FACILITIES & INFRASTRUCTURE
 * 
 * Displays the key state-of-the-art technological labs available for students.
 */
?>
<section id="facilities" class="bg-slate-50 py-24 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Title and Subtitle -->
        <div class="max-w-2xl">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('facilities_header.title', 'Facilities & Infrastructure')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('facilities_header.subtitle', 'State-of-the-art labs empowering innovation from concept to realization.')) ?>
            </p>
        </div>

        <!-- Labs Grid -->
        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php 
            $facilities = getHData('facilities', []);
            foreach ($facilities as $facility): 
            ?>
                <div onclick="openModal('<?= htmlspecialchars($facility['key']) ?>')" class="group relative overflow-hidden rounded-2xl bg-white p-8 shadow-sm border border-slate-100 transition hover:-translate-y-1 hover:shadow-xl cursor-pointer">
                    <div class="absolute top-0 right-0 h-16 w-16 rounded-bl-3xl bg-blue-900/5 transition group-hover:bg-blue-900/10"></div>
                    <h3 class="text-xl font-bold text-slate-900 transition group-hover:text-blue-950">
                        <?= htmlspecialchars($facility['title']) ?>
                    </h3>
                    <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                        <?= htmlspecialchars($facility['subtitle']) ?>
                    </p>
                    <div class="mt-6 flex items-center gap-2 text-sm font-bold text-blue-900">
                        Learn more <span class="transition group-hover:translate-x-1">&rarr;</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>