<?php
/**
 * PARTIAL: HERO SECTION
 * 
 * Renders the headline, paragraph, Call-to-Actions, and supports 
 * both lazy-loaded images and autoplaying looping MP4 videos.
 */
?>
<section id="home" class="relative overflow-hidden bg-gradient-to-tl from-blue-900 to-blue-700">
    <div class="max-w-7xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-16 items-center">
        <!-- Content Column -->
        <div class="text-white">
            <span class="inline-block mb-4 rounded-full bg-white/10 px-4 py-1 text-sm font-semibold uppercase tracking-wider">
                <?= htmlspecialchars(getHData('hero.badge', 'AICTE Approved IDEA Lab')) ?>
            </span>

            <h1 class="text-4xl lg:text-6xl font-extrabold leading-tight">
                <?= getHData('hero.title', 'Building India’s <br /> Next Generation of Innovators') ?>
            </h1>

            <p class="mt-6 text-lg text-blue-100 max-w-xl whitespace-pre-line leading-relaxed">
                <?= htmlspecialchars(getHData('hero.subtitle', 'A national innovation ecosystem empowering students to ideate, prototype, and transform ideas into real-world solutions.')) ?>
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="<?= htmlspecialchars(getHData('hero.cta_primary_link', '#contact')) ?>"
                    class="rounded-full bg-white px-8 py-4 text-blue-900 font-bold hover:bg-blue-100 transition shadow-lg shadow-blue-950/20 hover:-translate-y-0.5">
                    <?= htmlspecialchars(getHData('hero.cta_primary_text', 'Apply to IDEA Lab')) ?>
                </a>
                <a href="<?= htmlspecialchars(getHData('hero.cta_secondary_link', '#facilities')) ?>"
                    class="rounded-full border border-white/40 px-8 py-4 text-white hover:bg-white/10 transition font-bold hover:-translate-y-0.5">
                    <?= htmlspecialchars(getHData('hero.cta_secondary_text', 'Explore Facilities')) ?>
                </a>
            </div>
        </div>

        <!-- Visual Column (Image or Video) -->
        <div class="relative flex justify-center items-center w-full">
            <?php if (getHData('hero.visual_type', 'image') === 'video'): ?>
                <video autoplay loop muted playsinline class="rounded-2xl shadow-2xl w-full object-cover max-h-[450px]" aria-label="IDEA Lab Video Demonstration">
                    <source src="<?= htmlspecialchars(getHData('hero.visual_url', 'assets/heroVideo.mp4')) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <img loading="lazy" src="<?= htmlspecialchars(getHData('hero.visual_url', 'assets/heroImage.png')) ?>" alt="Illustration of IDEA Lab innovators" class="rounded-2xl shadow-2xl max-h-[450px] w-full object-cover" width="600" height="400" />
            <?php endif; ?>
        </div>
    </div>
</section>