<?php
/**
 * PARTIAL: OUTCOMES & IMPACT
 * 
 * Showcases the specific results, goals, and targets that the IDEA Lab strives to hit.
 */
?>
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Titles -->
        <div class="max-w-2xl">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('outcomes_header.title', 'Impact & Outcomes')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('outcomes_header.subtitle', 'Building a strong foundation for innovation, skills, and entrepreneurial thinking.')) ?>
            </p>
        </div>

        <!-- Outcome Cards Grid -->
        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <?php 
            $outcomes = getHData('outcomes', []);
            foreach ($outcomes as $outcome): 
            ?>
                <div class="border-t-4 border-blue-900 bg-slate-50 p-6 rounded-b-2xl shadow-sm border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">
                        <?= htmlspecialchars($outcome['title']) ?>
                    </h3>
                    <p class="mt-4 text-sm text-slate-600 leading-relaxed">
                        <?= htmlspecialchars($outcome['desc']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footnote Highlight -->
        <div class="mt-12 rounded-2xl bg-blue-50/50 p-8 border border-blue-100">
            <p class="text-sm text-slate-600 leading-relaxed max-w-4xl">
                <?= htmlspecialchars(getHData('outcomes_footer', 'These outcomes represent the early impact of the IDEA Lab.')) ?>
            </p>
        </div>
    </div>
</section>