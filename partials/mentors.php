<?php
/**
 * PARTIAL: MENTORS & LEADERSHIP GRID
 * 
 * Shows key academic coordinators, chief mentors, and tech gurus.
 */
?>
<section class="bg-slate-50 py-24 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Titles -->
        <div class="max-w-2xl">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('mentors_header.title', 'Mentors & Leadership')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('mentors_header.subtitle', 'Guiding innovation with academic excellence and industry experience.')) ?>
            </p>
        </div>

        <!-- Mentors profiles grid -->
        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <?php 
            $mentors = getHData('mentors', []);
            foreach ($mentors as $mentor): 
            ?>
                <div class="rounded-2xl bg-white p-6 text-center shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between h-[300px]">
                    <div class="space-y-4">
                        <img loading="lazy" src="<?= htmlspecialchars($mentor['image']) ?>" alt="Profile Portrait: <?= htmlspecialchars($mentor['name']) ?>" class="mx-auto h-28 w-28 rounded-full object-cover border-2 border-indigo-50 shadow-sm" width="112" height="112" />
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                <?= htmlspecialchars($mentor['name']) ?>
                            </h3>
                            <p class="text-xs font-semibold text-indigo-600 mt-1 uppercase tracking-wider">
                                <?= htmlspecialchars($mentor['role']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-50">
                        <a href="<?= htmlspecialchars($mentor['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition flex items-center justify-center gap-1.5">
                            LinkedIn &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>