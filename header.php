<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?= $metaDescription ?>">
	<title><?= $pageTitle ?></title>
	<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
	<style type="text/tailwindcss">
		.filter-btn {
    		@apply rounded-full border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 transition hover:bg-blue-900 hover:text-white;
  		}
  		.filter-btn.active {
    		@apply bg-blue-900 text-white border-blue-900;
  		}
  		.project-card {
    		@apply overflow-hidden rounded-2xl bg-white shadow hover:shadow-xl transition;
  		}
	</style>
</head>

<body>
	<header id="site-header" class="min-w-100dvh sticky top-0 z-50 bg-white transition-all duration-300 border-b border-slate-50">
		<nav class="">
			<div class="w-full flex h-20 px-4 items-center justify-between transition-all duration-300" id="navbar">

				<!-- Logo -->
				<div class="flex items-center gap-3">
					<img loading="lazy" src="assets/aicte.png" alt="AICTE Logo" class="h-11 w-11 object-contain" />
					<img loading="lazy" src="assets/ideaLab.png" alt="IDEA Lab Logo" class="h-11 w-11 object-contain" />
					<img loading="lazy" src="assets/logo.png" alt="GIFT Autonomous Logo" class="h-11 w-11 object-contain" />
				</div>

				<!-- Desktop Menu -->
				<ul class="hidden lg:flex items-center gap-8 text-sm font-medium text-slate-700">
					<li><a href="./#home" class="hover:text-blue-700">Home</a></li>
					<li><a href="./#about" class="hover:text-blue-700">About</a></li>
					<li><a href="./#facilities" class="hover:text-blue-700">Facilities</a></li>
					<li><a href="./#programs" class="hover:text-blue-700">Programs</a></li>
					<li><a href="./#projects" class="hover:text-blue-700">Projects</a></li>
					<li><a href="./#contact" class="hover:text-blue-700">Contact</a></li>
					<li><a href="./gallery" class="hover:text-blue-700">Gallery</a></li>
				</ul>

				<!-- CTA -->
				<div class="hidden lg:block">
					<a href="./#apply" class="rounded-full bg-blue-900 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-800 transition">
						Apply Now
					</a>
				</div>

				<!-- Mobile Menu Button -->
				<button id="menu-btn" class="lg:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-700 hover:bg-slate-100" aria-label="Open Menu">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-text-align-end-icon lucide-text-align-end"><path d="M21 5H3"/><path d="M21 12H9"/><path d="M21 19H7"/></svg>
				</button>
			</div>
		</nav>

		<!-- Mobile Menu -->
		<div id="mobile-menu" class="hidden lg:hidden bg-white border-t">
			<ul class="flex flex-col px-6 py-4 space-y-4 text-sm font-medium text-slate-700">
				<li><a href="./#home">Home</a></li>
				<li><a href="./#about">About</a></li>
				<li><a href="./#facilities">Facilities</a></li>
				<li><a href="./#programs">Programs</a></li>
				<li><a href="./#projects">Projects</a></li>
				<li><a href="./#contact">Contact</a></li>
				<li><a href="./gallery">Gallery</a></li>
				<li>
					<a href="./#apply" class="block text-center rounded-full bg-blue-900 py-3 text-white font-semibold">
						Apply Now
					</a>
				</li>
			</ul>
		</div>
	</header>