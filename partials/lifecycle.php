<?php
/**
 * PARTIAL: INNOVATION LIFECYCLE
 * 
 * Step-by-step methodology explaining the pathway of ideas into real-world products.
 */
?>
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Center Headings -->
        <div class="max-w-2xl text-center mx-auto">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('lifecycle_header.title', 'Innovation Lifecycle')) ?>
            </h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                <?= htmlspecialchars(getHData('lifecycle_header.subtitle', 'A structured journey that transforms ideas into impactful solutions.')) ?>
            </p>
        </div>

        <!-- Sequential Steps Grid -->
        <div class="mt-16 grid gap-8 md:grid-cols-5 relative">
            <?php 
            $lifecycleSteps = getHData('lifecycle', []);
            foreach ($lifecycleSteps as $step): 
            ?>
                <div class="relative text-center group">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-xl font-bold text-blue-900 border-2 border-blue-100 group-hover:scale-105 transition">
                        <?= htmlspecialchars($step['step']) ?>
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-slate-900">
                        <?= htmlspecialchars($step['title']) ?>
                    </h3>
                    <p class="mt-2 text-sm text-slate-500 px-4 leading-relaxed">
                        <?= htmlspecialchars($step['desc']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>