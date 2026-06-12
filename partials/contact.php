<?php
/**
 * PARTIAL: CONTACT INFORMATION & GEO LOCATION MAPS
 * 
 * Lists address coordinates, support hotlines, and maps out the lab location.
 */
?>
<section id="contact" class="bg-slate-50 py-24 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16">
        <!-- Contact details column -->
        <div class="flex flex-col justify-between">
            <div>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                    <?= htmlspecialchars(getHData('contact.title', 'Contact IDEA Lab')) ?>
                </h2>
                <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                    <?= htmlspecialchars(getHData('contact.subtitle', 'Reach out to collaborate, mentor, or innovate with us.')) ?>
                </p>

                <div class="mt-12 space-y-6 text-slate-700 text-sm md:text-base leading-relaxed">
                    <p>
                        <strong class="text-slate-950 font-bold block mb-1">Address:</strong>
                        <?= htmlspecialchars(getHData('contact.address', 'Gandhi Institute for Technology(GIFT), Bhubaneswar, Odisha')) ?>
                    </p>
                    <p>
                        <strong class="text-slate-950 font-bold block mb-1">Email Coordinates:</strong>
                        <a href="mailto:<?= htmlspecialchars(getHData('contact.coordinator_email', 'amanohar@gift.edu.in')) ?>" class="text-blue-900 hover:underline font-semibold">Coordinator</a> / 
                        <a href="mailto:<?= htmlspecialchars(getHData('contact.cocoordinator_email', 'sprout@gift.edu.in')) ?>" class="text-blue-900 hover:underline font-semibold">Co-coordinator</a>
                    </p>
                    <p>
                        <strong class="text-slate-950 font-bold block mb-1">Support Contacts:</strong>
                        <a href="tel:<?= htmlspecialchars(getHData('contact.coordinator_phone', '+917873008162')) ?>" class="text-blue-900 hover:underline font-semibold"><?= htmlspecialchars(getHData('contact.coordinator_phone', '+91-7873008162')) ?></a> / 
                        <a href="tel:<?= htmlspecialchars(getHData('contact.cocoordinator_phone', '+919937839943')) ?>" class="text-blue-900 hover:underline font-semibold"><?= htmlspecialchars(getHData('contact.cocoordinator_phone', '+91-9937839943')) ?></a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Maps container column -->
        <div class="rounded-2xl overflow-hidden shadow-lg h-96 border border-slate-200">
            <iframe src="<?= htmlspecialchars(getHData('contact.map_embed', 'https://www.google.com/maps/embed...')) ?>"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="GIFT Autonomous Campus Location Map"></iframe>
        </div>
    </div>
</section>