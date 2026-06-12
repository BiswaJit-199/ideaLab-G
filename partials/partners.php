<?php
/**
 * PARTIAL: PARTNERS & AFFILIATIONS SECTION
 * 
 * Lists national credentials and trust logos (AICTE, GoI, Startup India, etc.).
 */
?>
<section class="bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <h2 class="sr-only">Our Approvals and Affiliations</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 place-items-center">
            <?php 
            $partners = getHData('partners', []);
            foreach ($partners as $partner): 
            ?>
                <div class="flex items-center justify-center w-40 h-20 sm:w-44 sm:h-20 md:w-48 md:h-24">
                    <img loading="lazy" src="<?= htmlspecialchars($partner['logo']) ?>" alt="Affiliation: <?= htmlspecialchars($partner['name']) ?>" class="max-w-full max-h-full object-contain filter grayscale hover:grayscale-0 transition duration-300" width="180" height="90" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>