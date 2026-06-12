<?php
/**
 * PARTIAL: PROGRAMS & ACTIVITIES
 * 
 * Lists hackathons, workshops, faculty skilling, and development courses.
 */
?>
<section id="programs" class="bg-slate-50 py-24 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Main Headings -->
        <div class="max-w-2xl">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('programs_header.title', 'Programs & Activities')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('programs_header.subtitle', 'Continuous learning initiatives fostering innovation and skills.')) ?>
            </p>
        </div>

        <!-- Cards List Grid -->
        <div class="mt-16 grid gap-8 md:grid-cols-2">
            <?php 
            $programs = getHData('programs', []);
            foreach ($programs as $prog): 
            ?>
                <div class="flex gap-6 rounded-2xl bg-white p-8 shadow-sm border border-slate-100 hover:shadow-md transition">
                    <span class="text-4xl font-extrabold text-blue-900/10 shrink-0 self-start">
                        <?= htmlspecialchars($prog['num']) ?>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            <?= htmlspecialchars($prog['title']) ?>
                        </h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            <?= htmlspecialchars($prog['desc']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>