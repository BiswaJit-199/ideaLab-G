<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- PRIMARY SEO OPTIMIZATION -->
	<title><?= htmlspecialchars($pageTitle ?? 'IdeaLab | GIFT - Hub of Innovation & Prototyping') ?></title>
	<meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Explore AICTE IDEA Lab at Gandhi Institute for Technology (GIFT) Bhubaneswar. Fostering design thinking, IoT, digital manufacturing, 3D printing and student-driven prototyping.') ?>">
	<link rel="canonical" href="<?= htmlspecialchars($canonical ?? 'https://idealab.gift.edu.in/') ?>">
	<meta name="robots" content="index, follow">

	<!-- OPEN GRAPH META TAGS (Social Sharing - Facebook, LinkedIn) -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="<?= htmlspecialchars($canonical ?? 'https://idealab.gift.edu.in/') ?>">
	<meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'IdeaLab | GIFT - AICTE Approved Innovation Ecosystem') ?>">
	<meta property="og:description" content="<?= htmlspecialchars($metaDescription ?? 'Explore dynamic engineering, IoT prototyping, 3D printing, and CAD designs at AICTE IDEA Lab GIFT Bhubaneswar.') ?>">
	<meta property="og:image" content="https://idealab.gift.edu.in/assets/heroImage.png">
	<meta property="og:site_name" content="GIFT Autonomous IDEA Lab">

	<!-- TWITTER CARD META TAGS -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:url" content="<?= htmlspecialchars($canonical ?? 'https://idealab.gift.edu.in/') ?>">
	<meta name="twitter:title" content="<?= htmlspecialchars($pageTitle ?? 'IdeaLab | GIFT') ?>">
	<meta name="twitter:description" content="<?= htmlspecialchars($metaDescription ?? 'Discover 3D printing, IoT, and automated hardware prototyping.') ?>">
	<meta name="twitter:image" content="https://idealab.gift.edu.in/assets/heroImage.png">

	<!-- PERFORMANCE PRECONNECTS -->
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="preconnect" href="https://unpkg.com" crossorigin>

	<!-- TAILWIND PLAY CDN -->
	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	
	<style type="text/tailwindcss">
		.filter-btn {
    		@apply rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-blue-900 hover:text-white cursor-pointer hover:shadow-md;
  		}
  		.filter-btn.active {
    		@apply bg-blue-900 text-white border-blue-900 shadow-md;
  		}
  		.project-card {
    		@apply overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-xl transition-all duration-300;
  		}
	</style>

	<!-- STRUCTURED RICH SNIPPETS SCHEMA (JSON-LD Organization & EducationalLab) -->
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@graph": [
			{
				"@type": "EducationalOrganization",
				"@id": "https://gift.edu.in/#organization",
				"name": "Gandhi Institute for Technology (GIFT) Autonomous",
				"url": "https://gift.edu.in/",
				"address": {
					"@type": "PostalAddress",
					"streetAddress": "GIFT Autonomous, Bhubaneswar",
					"addressLocality": "Bhubaneswar",
					"addressRegion": "Odisha",
					"postalCode": "752054",
					"addressCountry": "IN"
				}
			},
			{
				"@type": "EducationalLab",
				"@id": "https://idealab.gift.edu.in/",
				"name": "AICTE IDEA Lab GIFT",
				"parentOrganization": {
					"@id": "https://gift.edu.in/"
				},
				"url": "https://idealab.gift.edu.in/",
				"description": "An AICTE-approved multidisciplinary incubation and prototyping space where student innovators build functional models.",
				"image": "https://idealab.gift.edu.in/assets/heroImage.png",
				"location": {
					"@type": "Place",
					"name": "IDEA Lab, GIFT Bhubaneswar",
					"address": {
						"@type": "PostalAddress",
						"streetAddress": "Gandhi Institute for Technology (GIFT) Autonomous, Bhubaneswar, Odisha",
						"addressLocality": "Bhubaneswar",
						"addressCountry": "IN"
					}
				}
			}
		]
	}
	</script>
</head>

<body class="bg-slate-50 font-sans text-slate-800 flex flex-col min-h-screen">
	<!-- HEADER BRANDING & NAVIGATION -->
	<header id="site-header" class="sticky top-0 z-50 bg-white transition-all duration-300 border-b border-slate-100/80 backdrop-blur-md bg-white/95">
		<nav aria-label="Main Website Navigation">
			<div class="w-full flex h-20 px-6 items-center justify-between transition-all duration-300" id="navbar">

				<!-- Approved Logos -->
				<div class="flex items-center gap-3.5">
					<img loading="lazy" src="assets/aicte.png" alt="AICTE Logo Badge" class="h-10 w-10 object-contain" width="40" height="40" />
					<img loading="lazy" src="assets/ideaLab.png" alt="AICTE IDEA Lab Logo" class="h-10 w-10 object-contain" width="40" height="40" />
					<img loading="lazy" src="assets/logo.png" alt="GIFT Autonomous Campus Logo" class="h-10 w-10 object-contain" width="40" height="40" />
				</div>

				<!-- Desktop Menu Navigation Links -->
				<ul class="hidden lg:flex items-center gap-8 text-sm font-bold text-slate-700">
					<li><a href="./#home" class="hover:text-blue-700 transition">Home</a></li>
					<li><a href="./#about" class="hover:text-blue-700 transition">About</a></li>
					<li><a href="./#facilities" class="hover:text-blue-700 transition">Facilities</a></li>
					<li><a href="./#programs" class="hover:text-blue-700 transition">Programs</a></li>
					<li><a href="./#projects" class="hover:text-blue-700 transition">Projects</a></li>
					<li><a href="./#contact" class="hover:text-blue-700 transition">Contact</a></li>
					<li><a href="./gallery" class="hover:text-blue-700 transition">Gallery</a></li>
				</ul>

				<!-- Call to Action -->
				<div class="hidden lg:block">
					<a href="./#apply" class="rounded-full bg-blue-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-blue-800 transition shadow-lg shadow-blue-900/10">
						Apply Now
					</a>
				</div>

				<!-- Mobile Menu Button -->
				<button id="menu-btn" class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-700 hover:bg-slate-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-slate-100" aria-label="Open Navigation Menu" aria-expanded="false">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-text-align-end-icon lucide-text-align-end" aria-hidden="true"><path d="M21 5H3"/><path d="M21 12H9"/><path d="M21 19H7"/></svg>
				</button>
			</div>
		</nav>

		<!-- Mobile Menu Overlay -->
		<div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-slate-100">
			<ul class="flex flex-col px-6 py-4 space-y-4 text-sm font-bold text-slate-700">
				<li><a href="./#home" class="hover:text-blue-700">Home</a></li>
				<li><a href="./#about" class="hover:text-blue-700">About</a></li>
				<li><a href="./#facilities" class="hover:text-blue-700">Facilities</a></li>
				<li><a href="./#programs" class="hover:text-blue-700">Programs</a></li>
				<li><a href="./#projects" class="hover:text-blue-700">Projects</a></li>
				<li><a href="./#contact" class="hover:text-blue-700">Contact</a></li>
				<li><a href="./gallery" class="hover:text-blue-700">Gallery</a></li>
				<li>
					<a href="./#apply" class="block text-center rounded-full bg-blue-900 py-3 text-white font-extrabold shadow-md hover:bg-blue-800 transition">
						Apply Now
					</a>
				</li>
			</ul>
		</div>
	</header>