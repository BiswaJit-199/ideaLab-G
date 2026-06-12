<?php
/**
 * PARTIAL: ABOUT SECTION
 * 
 * Explains what the IDEA Lab represents and lists the three main core pillars.
 */
?>
<section id="about" class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
        <!-- Process Graphics -->
        <div class="w-full">
            <img loading="lazy" src="<?= htmlspecialchars(getHData('about.image', 'assets/heroImage2.png')) ?>" alt="Structured Innovation Process Flowchart" class="rounded-2xl shadow-lg w-full object-cover" width="600" height="400" />
        </div>

        <!-- Description Content -->
        <div>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars(getHData('about.title', 'What is an IDEA Lab?')) ?>
            </h2>

            <p class="mt-6 text-lg text-slate-600 whitespace-pre-line leading-relaxed">
                <?= htmlspecialchars(getHData('about.description', 'An AICTE-recognized IDEA Lab is a multidisciplinary innovation space.')) ?>
            </p>

            <!-- Three Core Pillars Grid -->
            <div class="mt-12 grid gap-8 sm:grid-cols-3">
                <?php 
                $pillars = getHData('about.pillars', []);
                foreach ($pillars as $pillar): 
                ?>
                    <div class="group border-t border-slate-100 pt-4">
                        <span class="text-3xl font-extrabold text-blue-900/20 group-hover:text-blue-900/40 transition">
                            <?= htmlspecialchars($pillar['num']) ?>
                        </span>
                        <h3 class="mt-3 text-lg font-bold text-slate-900">
                            <?= htmlspecialchars($pillar['title']) ?>
                        </h3>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            <?= htmlspecialchars($pillar['desc']) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>