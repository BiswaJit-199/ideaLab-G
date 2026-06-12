<?php
/**
 * PARTIAL: CALL TO ACTION SECTION & MINI-FORM
 * 
 * Provides an actionable checkpoint layout and instant applicant response layout.
 */
?>
<section id="apply" class="relative overflow-hidden bg-gradient-to-br from-blue-950 to-blue-900 py-24 text-white">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
        <!-- Explanatory checkpoints content -->
        <div>
            <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight">
                <?= htmlspecialchars(getHData('cta.title', 'Ready to Build, Innovate & Launch?')) ?>
            </h2>
            <p class="mt-6 text-lg text-blue-200 leading-relaxed">
                <?= htmlspecialchars(getHData('cta.desc', 'Join the IDEA Lab and transform your ideas into impactful innovations.')) ?>
            </p>

            <!-- Checkpoints list -->
            <ul class="mt-8 space-y-4">
                <?php 
                $bullets = getHData('cta.bullets', []);
                foreach ($bullets as $bullet): 
                ?>
                    <li class="flex items-center gap-3 text-blue-100 font-semibold">
                        <span class="text-emerald-400 font-bold" aria-hidden="true">&check;</span> <?= htmlspecialchars($bullet) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Application Form Column -->
        <div class="rounded-2xl bg-white p-8 text-slate-900 shadow-2xl border border-slate-100">
            <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Apply to IDEA Lab</h3>
            <p class="mt-2 text-sm text-slate-500">Kickstart your innovation journey.</p>

            <form class="mt-8 space-y-4" onsubmit="event.preventDefault(); alert('Application submitted successfully!');">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Full Name</label>
                    <input type="text" required placeholder="John Doe" class="mt-2 w-full rounded-lg border border-slate-200 p-3 text-sm focus:border-blue-900 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Email Address</label>
                    <input type="email" required placeholder="name@domain.com" class="mt-2 w-full rounded-lg border border-slate-200 p-3 text-sm focus:border-blue-900 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Applicant Type</label>
                    <select class="mt-2 w-full rounded-lg border border-slate-200 p-3 text-sm focus:border-blue-900 focus:outline-none bg-white">
                        <option>Student</option>
                        <option>Faculty</option>
                        <option>Startup</option>
                    </select>
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-900 py-4 font-bold text-white hover:bg-blue-800 transition shadow-lg shadow-blue-900/10">
                    Submit Application
                </button>
            </form>
        </div>
    </div>
</section>