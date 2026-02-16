<!-- small utility CSS to include in your stylesheet (or keep inline in header) -->
<style>
	/* slight tweak to scale to avoid huge overflow during hover on small devices */
	img {
		backface-visibility: hidden;
		-webkit-backface-visibility: hidden;
	}

	/* smooth transition for tag color changes */
	.tag-transition {
		transition: background-color 220ms ease, color 220ms ease, border-color 220ms ease;
		-webkit-transition: background-color 220ms ease, color 220ms ease, border-color 220ms ease;
	}

	/* small accessibility / layout helpers */
	.article-card {
		min-width: 0;
	}

	a[data-open-group] {
		min-width: 0;
	}

	/* optional clamp for long text */
	.line-clamp-3 {
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<?php
function formatEventLabel($key)
{
    // If fully uppercase or short acronym, keep as uppercase
    if (strlen($key) <= 4 && strtoupper($key) === $key) {
        return $key;
    }

    // Replace underscores with spaces
    $label = str_replace('_', ' ', $key);

    // If key has no underscore and is short (like fdp), make uppercase
    if (strlen($key) <= 4 && strpos($key, '_') === false) {
        return strtoupper($key);
    }

    // Convert to Title Case
    return ucwords(strtolower($label));
}
?>
<?php
$pageTitle = "Gallery - IdeaLab | GIFT";
$metaDescription = "Discover moments of creativity and innovation in our IDEA Lab gallery - from hands-on training sessions to project demonstrations and industry collaborations.";
$canonical = "https://idealab.gift.edu.in/gallery";
// ---------------------
// Config
// ---------------------
$jsonFile = './data/gallery.json';
$perPage = 8;
$noItemMsg = "No Images Available.";

$items = [];

if (file_exists($jsonFile)) {
	$raw = file_get_contents($jsonFile);
	$data = json_decode($raw, true);
	if (!is_array($data)) $data = [];

	// Polymorphic behavior based on ?ev=
	$eventKey = isset($_GET['ev']) ? strtolower(trim($_GET['ev'])) : null;

	if ($eventKey && isset($data[$eventKey]) && is_array($data[$eventKey])) {
		// Show only selected object
		$items = $data[$eventKey];
		$pageTitle = "IDEA Lab Gallery";
		if (!empty($eventKey)) {
			$pageTitle .= " - " . formatEventLabel($eventKey);
		}
	} else {
		// Merge all objects if no ?ev
		foreach ($data as $groupArray) {
			if (is_array($groupArray)) {
				$items = array_merge($items, $groupArray);
			}
		}
	}
}


// sanitize items and ensure consistent structure
$items = array_values(array_filter($items, function ($g) {
	return isset($g['title']) && isset($g['images']) && is_array($g['images']) && count($g['images']) >= 1;
}));

$totalItems = count($items);

// ---------------------
// Pagination
// ---------------------
$page = max(1, intval($_GET['page'] ?? 1));
$totalPages = max(1, (int)ceil($totalItems / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$pageItems = array_slice($items, $offset, $perPage);

// For deep-linking: optional group/photo in query (use numeric index)
$openGroup = isset($_GET['group']) ? intval($_GET['group']) : null;
$openPhoto = isset($_GET['photo']) ? intval($_GET['photo']) : 0;

// base url for pagination links
$baseUrl = strtok($_SERVER["REQUEST_URI"], '?'); // current script path without query
// helper to build url with params
function buildUrl($params = [])
{
	$base = strtok($_SERVER["REQUEST_URI"], '?');
	$existing = $_GET;

	// Preserve event key
	if (isset($existing['ev'])) {
		$params['ev'] = $existing['ev'];
	}

	$qs = http_build_query($params);
	return $base . ($qs ? '?' . $qs : '');
}


// ---------------------
// Helper: format date
// ---------------------
function fmtDate($iso)
{
	$dt = @date_create($iso);
	if (!$dt) return htmlspecialchars($iso);
	return $dt->format('F j, Y');
}

// Encode items for client JS
$itemsJson = json_encode(array_values($items), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

// ---------------------
// Output HTML
// ---------------------
?>
<?php
// deterministic color generator (same as before)
function tag_colors(string $tag): array
{
	$h = crc32($tag) % 360;

	// Light (vibrant pastel)
	$lightBg     = "hsl({$h}, 85%, 92%)";
	$lightFg     = "hsl({$h}, 28%, 16%)";
	$lightBorder = "hsl({$h}, 40%, 75%)";

	// Dark (faded background via HSLA, readable foreground)
	$darkBg     = "hsla({$h}, 60%, 20%, 0.22)"; // use HSLA (alpha) for dark fade
	$darkFg     = "hsl({$h}, 70%, 88%)";
	$darkBorder = "hsl({$h}, 40%, 35%)";

	return [
		'lightBg'     => $lightBg,
		'lightFg'     => $lightFg,
		'lightBorder' => $lightBorder,
		'darkBg'      => $darkBg,
		'darkFg'      => $darkFg,
		'darkBorder'  => $darkBorder,
	];
}
include "./header.php";
?>
<main id="main" class="min-h-screen">
	<header class="container px-4 md:px-6 py-8">
		<h1 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-800">Gallery</h1>
		<p class="mt-2 text-sm text-slate-600">Browse photo groups from IdeaLab - Gandhi Institute for Technology events, trainings and activities. Click any card to view the gallery and details.</p>
	</header>

	<section class="container px-4 md:px-6 pb-2">
		<div class="grid justify-center sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
			<?= ($totalItems == 0) ? $noItemMsg : '' ?>
			<?php foreach ($pageItems as $idx => $group) :
				$globalIndex = $offset + $idx; // index within full items array
				$safeTitle = htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8');
				$safeDesc = htmlspecialchars($group['description'] ?? '', ENT_QUOTES, 'UTF-8');
				$tags = $group['tags'] ?? [];
				$images = array_slice($group['images'], 0, 3); // use up to 3 for card preview
				$imgCount = count($images);
				$moreCount = max(0, count($group['images']) - $imgCount);
			?>

				<!-- Article card -->
				<article class="article-card relative min-w-0 rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-transform duration-200 transform hover:-translate-y-1 max-w-[300px] sm:max-w-full p-[6px]" aria-labelledby="g-title-<?= $globalIndex ?>">
					<a href="<?= htmlspecialchars(buildUrl(['group' => $globalIndex])) ?>" data-open-group="<?= $globalIndex ?>" class="block group focus:outline-none flex flex-col h-full min-w-0" aria-describedby="g-desc-<?= $globalIndex ?>">

						<!-- IMAGE AREA -->
						<div class="relative w-full flex-none bg-transparent">
							<?php if ($imgCount === 3) : ?>
								<div class="grid grid-cols-2 gap-1 h-48 md:h-56">
									<div class="overflow-hidden rounded-l-[14px] relative">
										<img loading="lazy" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= $safeTitle ?> - image 1" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-105 block">
									</div>
									<div class="flex flex-col gap-1">
										<div class="overflow-hidden rounded-tr-[14px] h-1/2 relative">
											<img loading="lazy" src="<?= htmlspecialchars($images[1]) ?>" alt="<?= $safeTitle ?> - image 2" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-105 block">
										</div>
										<div class="overflow-hidden rounded-br-[14px] h-1/2 relative">
											<img loading="lazy" src="<?= htmlspecialchars($images[2]) ?>" alt="<?= $safeTitle ?> - image 3" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-105 block">
										</div>
									</div>
								</div>

							<?php elseif ($imgCount === 2) : ?>
								<div class="grid grid-cols-2 gap-1 h-48 md:h-56">
									<div class="overflow-hidden rounded-l-[14px] relative">
										<img loading="lazy" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= $safeTitle ?> - image 1" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-105 block">
									</div>
									<div class="overflow-hidden rounded-r-[14px] relative">
										<img loading="lazy" src="<?= htmlspecialchars($images[1]) ?>" alt="<?= $safeTitle ?> - image 2" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-105 block">
									</div>
								</div>

							<?php else : ?>
								<div class="overflow-hidden rounded-[14px] h-56 md:h-64 relative">
									<img loading="lazy" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= $safeTitle ?>" class="w-full h-full object-cover transition-transform duration-400 will-change-transform group-hover:scale-103 block">
								</div>
							<?php endif; ?>

							<!-- +N MORE indicator: pointer-events-none so it doesn't block anchor clicks -->
							<?php if ($moreCount > 0) : ?>
								<span class="absolute top-3 right-3 z-20 inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-semibold shadow-md pointer-events-none" style="background: rgba(2,6,23,0.72); color: white;" title="<?= (int)$moreCount ?> more images">
									+<?= (int)$moreCount ?>
								</span>
							<?php endif; ?>
						</div>

						<!-- META (z-10 ensures it sits above image transforms) -->
						<div class="relative z-10 flex-shrink-0 py-4 px-2 border-t border-slate-100 bg-white">
							<h3 id="g-title-<?= $globalIndex ?>" class="text-lg font-semibold text-slate-800 leading-tight break-words">
								<?= $safeTitle ?>
							</h3>

							<div class="mt-3 flex items-center justify-between gap-1">
								<div class="flex items-center gap-1 flex-wrap max-w-[70%]">
									<?php foreach ($tags as $t) :
										$colors = tag_colors($t);
										$safeTag = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
									?>
										<span class="inline-flex items-center text-xs px-2 py-1 rounded-full font-medium tag-transition" style="background: <?= $colors['lightBg'] ?>; color: <?= $colors['lightFg'] ?>; border: 1px solid <?= $colors['lightBorder'] ?>;" data-light-bg="<?= $colors['lightBg'] ?>" data-light-fg="<?= $colors['lightFg'] ?>" data-light-border="<?= $colors['lightBorder'] ?>" data-dark-bg="<?= $colors['darkBg'] ?>" data-dark-fg="<?= $colors['darkFg'] ?>" data-dark-border="<?= $colors['darkBorder'] ?>">
											<?= $safeTag ?>
										</span>
									<?php endforeach; ?>
								</div>

								<time datetime="<?= htmlspecialchars($group['date']) ?>" class="text-xs text-slate-500">
									<?= fmtDate($group['date']) ?>
								</time>
							</div>
						</div>
					</a>
				</article>

			<?php endforeach; ?>
		</div>

		<!-- Pagination -->
		<nav class="mt-8 flex items-center justify-between<?= ($totalItems == 0) ? ' hidden' : '' ?>" aria-label="Pagination">
			<div class="text-sm text-slate-600">
				Showing <?= $offset + 1 ?>-<?= min($offset + $perPage, $totalItems) ?> of <?= $totalItems ?>
			</div>
			<div class="flex gap-2 items-center">
				<?php if ($page > 1) : ?>
					<a href="<?= htmlspecialchars(buildUrl(['page' => $page - 1])) ?>" class="px-3 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-blue-900 hover:text-slate-50 font-semibold">Prev</a>
				<?php else : ?>
					<span class="font-semibold px-3 py-1 rounded-md text-slate-400 cursor-not-allowed">Prev</span>
				<?php endif; ?>

				<?php
				// show a compact set of page numbers
				$startPage = max(1, $page - 2);
				$endPage = min($totalPages, $page + 2);
				if ($startPage > 1) echo '<a href="' . htmlspecialchars(buildUrl(['page' => 1])) . '" class="px-3 py-1 rounded-md text-slate-700 border border-slate-200">1</a>';
				if ($startPage > 2) echo '<span class="px-2">…</span>';
				for ($p = $startPage; $p <= $endPage; $p++) :
				?>
					<?php if ($p == $page) : ?>
						<span class="px-3 py-1 rounded-md bg-blue-900 text-slate-50 font-semibold"><?= $p ?></span>
					<?php else : ?>
						<a href="<?= htmlspecialchars(buildUrl(['page' => $p])) ?>" class="px-3 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-blue-900 hover:text-slate-50 font-semibold"><?= $p ?></a>
					<?php endif; ?>
				<?php endfor;
				if ($endPage < $totalPages - 1) echo '<span class="px-2">…</span>';
				if ($endPage < $totalPages) echo '<a href="' . htmlspecialchars(buildUrl(['page' => $totalPages])) . '" class="px-3 py-1 rounded-md border border-slate-200 text-slate-700">' . $totalPages . '</a>';
				?>

				<?php if ($page < $totalPages) : ?>
					<a href="<?= htmlspecialchars(buildUrl(['page' => $page + 1])) ?>" class="px-3 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-blue-900 hover:text-slate-50 font-semibold">Next</a>
				<?php else : ?>
					<span class="font-semibold px-3 py-1 rounded-md text-slate-400 cursor-not-allowed">Next</span>
				<?php endif; ?>
			</div>
		</nav>
	</section>
</main>

<!-- ------------------------------
     Modal / Lightbox (single DOM modal for all groups)
     ------------------------------ -->
<div id="gallery-modal" class="fixed inset-0 z-60 hidden items-center justify-center bg-black/60 px-4 pt-28 md:pt-4 pb-4 overflow-auto min-h-[100dvh]">
	<div class="relative max-w-6xl w-full bg-white rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-2 md:mt-28 sm:mt-[6rem] mt-[12rem]">
		<button id="modal-close" class="absolute z-[60] right-3 top-3 text-slate-600 bg-white/70 rounded-full p-2 hover:bg-white" aria-label="Close">
			<svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
				<path d="M6 6 14 14M6 14 14 6" stroke-width="1.6" stroke-linecap="round" />
			</svg>
		</button>
		<!-- Left: images -->
		<div class="relative bg-black/5 flex items-center justify-center">
			<button id="modal-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 rounded-full p-2 bg-white/80 hover:bg-white" aria-label="Previous image">
				<svg class="h-5 w-5 text-slate-800" viewBox="0 0 20 20" fill="none" stroke="currentColor">
					<path d="M13 16 7 10l6-6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
			<div id="modal-image-wrap" class="w-full h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden bg-black">
				<img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain transition-transform">
			</div>
			<button id="modal-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 rounded-full p-2 bg-white/80 hover:bg-white" aria-label="Next image">
				<svg class="h-5 w-5 text-slate-800" viewBox="0 0 20 20" fill="none" stroke="currentColor">
					<path d="M7 4 13 10 7 16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>

		<!-- Right: details -->
		<aside class="p-6 overflow-auto">
			<h3 id="modal-title" class="text-xl font-bold text-slate-800">Title</h3>
			<div class="mt-2 flex items-center gap-2">
				<div id="modal-tags" class="flex gap-2 flex-wrap"></div>
				<time id="modal-date" class="text-sm text-slate-500 ml-auto"></time>
			</div>
			<p id="modal-desc" class="mt-4 text-slate-700 overflow-y-scroll max-h-[250px]"></p>

			<div class="md:bottom-5 pr-6">
				<div class="mt-6 flex items-center gap-3 justify-between">
					<button id="modal-prev-group" class="px-3 py-1 rounded border border-slate-200 text-slate-900 hover:bg-blue-900 hover:border-blue-900 cursor-pointer font-semibold hover:text-slate-50">Prev Group</button>
					<button id="modal-next-group" class="px-3 py-1 rounded border border-slate-200 text-slate-900 hover:bg-blue-900 hover:border-blue-900 cursor-pointer font-semibold hover:text-slate-50">Next Group</button>
					<a id="modal-view-link" class="ml-auto text-sm text-amber-600 underline hidden" href="#">Open group link</a>
				</div>

				<div class="mt-6">
					<p class="text-xs text-slate-500">Tip: Use arrow keys or swipe on mobile to navigate images. When you reach the end of a group's images, the next group's images will open automatically.</p>
				</div>
			</div>
		</aside>
	</div>
</div>
<?php
	include "./footer.php";
?>
<script src="./navbar.js"></script>
<!-- JSON-LD (basic gallery structure) -->
<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "CollectionPage",
		"name": "Balaram Panda Trust — Gallery",
		"description": "Photo gallery showing events, trainings and impact from Balaram Panda Trust.",
		"hasPart": [
			<?php
			$ld = [];
			foreach ($items as $g) {
				$firstImg = isset($g['images'][0]) ? $g['images'][0] : null;
				$ld[] = json_encode([
					"@type" => "ImageObject",
					"name" => $g['title'],
					"caption" => $g['description'],
					"contentUrl" => $firstImg,
					"datePublished" => $g['date'],
				]);
			}
			echo implode(",\n", $ld);
			?>
		]
	}
</script>

<!-- Client-side data + behavior -->
<script>
	// Items array from PHP (safe encoded)
	const GALLERY_ITEMS = <?= $itemsJson ?>;
	const totalGroups = GALLERY_ITEMS.length;

	// State
	let currentGroup = <?= is_null($openGroup) ? 'null' : intval($openGroup) ?>;
	let currentPhoto = <?= intval($openPhoto) ?>;

	const modal = document.getElementById('gallery-modal');
	const overlay = modal; // modal container already has a dark backdrop
	const modalImage = document.getElementById('modal-image');
	const modalTitle = document.getElementById('modal-title');
	const modalDesc = document.getElementById('modal-desc');
	const modalTags = document.getElementById('modal-tags');
	const modalDate = document.getElementById('modal-date');
	const modalViewLink = document.getElementById('modal-view-link');

	// JS crc32 implementation (used to deterministically generate a hue to match PHP)
	// lightweight implementation producing unsigned 32-bit int
	function crc32(str) {
		let crc = 0 ^ (-1);
		for (let i = 0, len = str.length; i < len; i++) {
			crc = (crc >>> 8) ^ crc32_table[(crc ^ str.charCodeAt(i)) & 0xFF];
		}
		return (crc ^ (-1)) >>> 0;
	}

	// generate crc32 table once
	const crc32_table = (function() {
		const table = new Uint32Array(256);
		for (let i = 0; i < 256; i++) {
			let c = i;
			for (let k = 0; k < 8; k++) {
				c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
			}
			table[i] = c >>> 0;
		}
		return table;
	})();

	// deterministic tag color generator that mirrors PHP tag_colors()
	function tagColorsDeterministic(tag) {
		const h = crc32(String(tag)) % 360;

		const lightBg = `hsl(${h}, 85%, 92%)`;
		const lightFg = `hsl(${h}, 28%, 16%)`;
		const lightBorder = `hsl(${h}, 40%, 75%)`;

		const darkBg = `hsla(${h}, 60%, 20%, 0.22)`;
		const darkFg = `hsl(${h}, 70%, 88%)`;
		const darkBorder = `hsl(${h}, 40%, 35%)`;

		return {
			lightBg,
			lightFg,
			lightBorder,
			darkBg,
			darkFg,
			darkBorder
		};
	}

	// Open modal for groupIndex, photoIndex
	function openModal(groupIndex, photoIndex = 0) {
		if (groupIndex < 0 || groupIndex >= totalGroups) return;
		currentGroup = groupIndex;
		const group = GALLERY_ITEMS[groupIndex];
		if (!group) return;
		currentPhoto = Math.max(0, Math.min(photoIndex, group.images.length - 1));

		renderModal();

		modal.classList.remove('hidden');
		modal.classList.add('flex');
		document.documentElement.classList.add('overflow-hidden');

		// Push history for deep linking
		const url = new URL(window.location);
		url.searchParams.set('group', String(groupIndex));
		url.searchParams.set('photo', String(currentPhoto));
		history.replaceState({
			group: groupIndex,
			photo: currentPhoto
		}, '', url);

		// focus for accessibility
		document.getElementById('modal-close').focus();
	}

	function closeModal() {
		modal.classList.add('hidden');
		modal.classList.remove('flex');
		document.documentElement.classList.remove('overflow-hidden');

		// remove query params
		const url = new URL(window.location);
		url.searchParams.delete('group');
		url.searchParams.delete('photo');
		history.replaceState({}, '', url.pathname + url.search);
		currentGroup = null;
		currentPhoto = 0;
	}

	// NOTE: Replaced the random palette with deterministic generator above.
	// This function returns tag color objects (deterministic), but also acts as a fallback
	// when dataset attributes are missing on server-rendered tags.
	function tagColors(tag) {
		return tagColorsDeterministic(tag);
	}

	function renderModal() {
		const group = GALLERY_ITEMS[currentGroup];
		if (!group) return;
		const img = group.images[currentPhoto];

		modalImage.src = img;
		modalImage.alt = group.title + " - image " + (currentPhoto + 1);
		modalTitle.textContent = group.title;
		modalDesc.innerHTML = group.description || ""; // securityV
		modalDate.textContent = new Date(group.date).toLocaleDateString('en-US', {
			year: 'numeric',
			month: 'long',
			day: 'numeric'
		});

		modalTags.innerHTML = '';
		(group.tags || []).forEach(t => {
			const colors = tagColors(t); // deterministic generator you already have
			const span = document.createElement('span');
			span.className = "inline-flex items-center text-xs px-2 py-1 rounded-full font-medium tag-transition";
			span.textContent = t;

			// store dark/light variants (so other code can still use them)
			span.dataset.lightBg = colors.lightBg;
			span.dataset.lightFg = colors.lightFg;
			span.dataset.lightBorder = colors.lightBorder;
			span.dataset.darkBg = colors.darkBg;
			span.dataset.darkFg = colors.darkFg;
			span.dataset.darkBorder = colors.darkBorder;

			// Immediately apply correct inline style based on current theme
			const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');

			const bg = isDark ? colors.darkBg : colors.lightBg;
			const fg = isDark ? colors.darkFg : colors.lightFg;
			const border = isDark ? colors.darkBorder : colors.lightBorder;

			// Apply inline styles (ensures new tags follow current theme right away)
			span.style.background = bg;
			span.style.color = fg;
			span.style.border = `1px solid ${border}`;

			modalTags.appendChild(span);
		});


		// set view link to deep-link to this group
		const url = new URL(window.location);
		url.searchParams.set("group", String(currentGroup));
		url.searchParams.set("photo", String(currentPhoto));
		modalViewLink.href = url.toString();

		// preload neighbors
		preloadNeighborImages();
	}

	function preloadNeighborImages() {
		// preload current group's prev/next
		const imgs = GALLERY_ITEMS[currentGroup].images;
		[currentPhoto - 1, currentPhoto + 1].forEach(i => {
			if (i >= 0 && i < imgs.length) {
				const img = new Image();
				img.src = imgs[i];
			}
		});
		// preload next group's first image
		if (currentGroup + 1 < totalGroups) {
			const img = new Image();
			img.src = GALLERY_ITEMS[currentGroup + 1].images[0];
		}
		// preload prev group's last image
		if (currentGroup - 1 >= 0) {
			const arr = GALLERY_ITEMS[currentGroup - 1].images;
			if (arr && arr.length) {
				const img = new Image();
				img.src = arr[arr.length - 1];
			}
		}
	}

	// navigation
	function nextPhoto() {
		const group = GALLERY_ITEMS[currentGroup];
		if (!group) return;
		if (currentPhoto < group.images.length - 1) {
			currentPhoto++;
		} else {
			// move to next group if exists
			if (currentGroup < totalGroups - 1) {
				currentGroup++;
				currentPhoto = 0;
			} else {
				// wrap to first group
				currentGroup = 0;
				currentPhoto = 0;
			}
		}
		renderModal();
		pushHistoryState();
	}

	function prevPhoto() {
		const group = GALLERY_ITEMS[currentGroup];
		if (!group) return;
		if (currentPhoto > 0) {
			currentPhoto--;
		} else {
			// move to previous group's last image
			if (currentGroup > 0) {
				currentGroup--;
				currentPhoto = GALLERY_ITEMS[currentGroup].images.length - 1;
			} else {
				// wrap to last
				currentGroup = totalGroups - 1;
				currentPhoto = GALLERY_ITEMS[currentGroup].images.length - 1;
			}
		}
		renderModal();
		pushHistoryState();
	}

	function pushHistoryState() {
		const url = new URL(window.location);
		url.searchParams.set('group', String(currentGroup));
		url.searchParams.set('photo', String(currentPhoto));
		history.replaceState({
			group: currentGroup,
			photo: currentPhoto
		}, '', url);
	}

	// attach events to cards (links)
	// Combined click + keyboard handling in a single pass (avoids duplication)
	document.querySelectorAll('a[data-open-group]').forEach(a => {
		a.addEventListener('click', function(e) {
			// if user has JS, open modal instead of following the link
			e.preventDefault();
			const gi = parseInt(this.getAttribute('data-open-group'), 10);
			openModal(gi, 0);
		});

		a.addEventListener('keydown', function(e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				const gi = parseInt(this.getAttribute('data-open-group'), 10);
				openModal(gi, 0);
			}
		});
	});

	// modal buttons
	document.getElementById('modal-next').addEventListener('click', nextPhoto);
	document.getElementById('modal-prev').addEventListener('click', prevPhoto);
	document.getElementById('modal-close').addEventListener('click', closeModal);

	// next/prev group buttons
	document.getElementById('modal-next-group').addEventListener('click', function() {
		openModal((currentGroup + 1) % totalGroups, 0);
	});
	document.getElementById('modal-prev-group').addEventListener('click', function() {
		openModal((currentGroup - 1 + totalGroups) % totalGroups, 0);
	});

	// keyboard navigation
	document.addEventListener('keydown', function(e) {
		if (modal.classList.contains('hidden')) return;
		if (e.key === 'ArrowRight') nextPhoto();
		if (e.key === 'ArrowLeft') prevPhoto();
		if (e.key === 'Escape') closeModal();
	});

	// overlay click to close (click outside content)
	document.getElementById('gallery-modal').addEventListener('click', function(e) {
		if (e.target === this) closeModal();
	});

	// Mobile swipe detection inside image wrap
	(function enableSwipe() {
		const wrap = document.getElementById('modal-image-wrap');
		let startX = 0,
			startY = 0,
			startTime = 0;
		wrap.addEventListener('touchstart', function(e) {
			const t = e.touches[0];
			startX = t.clientX;
			startY = t.clientY;
			startTime = Date.now();
		}, {
			passive: true
		});

		wrap.addEventListener('touchend', function(e) {
			const t = e.changedTouches[0];
			const dx = t.clientX - startX;
			const dy = t.clientY - startY;
			const dt = Date.now() - startTime;
			// horizontal swipe threshold
			if (Math.abs(dx) > 50 && Math.abs(dy) < 80 && dt < 1000) {
				if (dx < 0) nextPhoto();
				else prevPhoto();
			}
			// vertical swipe to close
			if (Math.abs(dy) > 150 && Math.abs(dx) < 80 && dt < 1000) {
				closeModal();
			}
		}, {
			passive: true
		});
	})();

	// open modal automatically if deep-link present
	(function checkDeepLink() {
		const url = new URL(window.location);
		const g = url.searchParams.get('group');
		const p = url.searchParams.get('photo');
		if (g !== null) {
			const gi = parseInt(g, 10);
			const pi = p ? parseInt(p, 10) : 0;
			if (!isNaN(gi) && gi >= 0 && gi < GALLERY_ITEMS.length) {
				// Defer slightly to allow DOM ready
				setTimeout(() => openModal(gi, pi), 300);
			}
		} else if (currentGroup !== null) {
			// server-side provided state
			setTimeout(() => openModal(currentGroup, currentPhoto), 300);
		}
	})();

	// history back/forward handling: close modal if state removed
	window.addEventListener('popstate', function(e) {
		const s = e.state;
		if (!s || typeof s.group === 'undefined') {
			closeModal();
		} else {
			openModal(Number(s.group), Number(s.photo) || 0);
		}
	});
</script>

<script>
	document.addEventListener("DOMContentLoaded", () => {
		const htmlEl = document.documentElement;
		const bodyEl = document.body;
		const modalTagsContainer = document.getElementById('modal-tags');

		// helper: determine if currently dark (check html OR body)
		const isCurrentlyDark = () => htmlEl.classList.contains('dark') || bodyEl.classList.contains('dark');

		// CRC32 table + function (kept local)
		const crc32_table = (function() {
			const table = new Uint32Array(256);
			for (let i = 0; i < 256; i++) {
				let c = i;
				for (let k = 0; k < 8; k++) {
					c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
				}
				table[i] = c >>> 0;
			}
			return table;
		})();

		function crc32(str) {
			let crc = 0 ^ (-1);
			for (let i = 0, len = str.length; i < len; i++) {
				crc = (crc >>> 8) ^ crc32_table[(crc ^ str.charCodeAt(i)) & 0xFF];
			}
			return (crc ^ (-1)) >>> 0;
		}

		// deterministic color generator (matches PHP tag_colors)
		function computeDeterministicColors(tag) {
			const h = crc32(String(tag)) % 360;
			return {
				lightBg: `hsl(${h}, 85%, 92%)`,
				lightFg: `hsl(${h}, 28%, 16%)`,
				lightBorder: `hsl(${h}, 40%, 75%)`,
				darkBg: `hsla(${h}, 60%, 20%, 0.22)`,
				darkFg: `hsl(${h}, 70%, 88%)`,
				darkBorder: `hsl(${h}, 40%, 35%)`
			};
		}

		// Return a live list (re-query) of tag elements whenever needed
		const getTagEls = () => Array.from(document.querySelectorAll('.tag-transition'));

		// ensure dataset values exist for an element (compute if missing)
		function ensureTagDatasets(el) {
			if (!el) return;
			if (!el.dataset.lightBg || !el.dataset.lightFg || !el.dataset.lightBorder || !el.dataset.darkBg || !el.dataset.darkFg || !el.dataset.darkBorder) {
				try {
					const tagText = (el.textContent || "").trim();
					if (!tagText) return;
					const colors = computeDeterministicColors(tagText);
					el.dataset.lightBg = el.dataset.lightBg || colors.lightBg;
					el.dataset.lightFg = el.dataset.lightFg || colors.lightFg;
					el.dataset.lightBorder = el.dataset.lightBorder || colors.lightBorder;
					el.dataset.darkBg = el.dataset.darkBg || colors.darkBg;
					el.dataset.darkFg = el.dataset.darkFg || colors.darkFg;
					el.dataset.darkBorder = el.dataset.darkBorder || colors.darkBorder;
				} catch (err) {
					// ignore
				}
			}
		}

		// apply theme to all tags (re-queries DOM each time so modal-created tags are included)
		function applyThemeToTags() {
			const dark = isCurrentlyDark();
			getTagEls().forEach(el => {
				ensureTagDatasets(el);
				const bg = dark ? el.dataset.darkBg : el.dataset.lightBg;
				const fg = dark ? el.dataset.darkFg : el.dataset.lightFg;
				const border = dark ? el.dataset.darkBorder : el.dataset.lightBorder;

				if (bg !== undefined && bg !== '') el.style.background = bg;
				if (fg !== undefined && fg !== '') el.style.color = fg;
				if (border !== undefined && border !== '') el.style.border = `1px solid ${border}`;
			});
		}

		// initial population: ensure datasets exist and apply initial theme
		getTagEls().forEach(el => ensureTagDatasets(el));
		applyThemeToTags();

		// Observe BOTH html and body for 'class' changes (some toggles add 'dark' on body)
		const obsCallback = () => applyThemeToTags();
		const moHtml = new MutationObserver(obsCallback);
		const moBody = new MutationObserver(obsCallback);
		moHtml.observe(htmlEl, {
			attributes: true,
			attributeFilter: ['class']
		});
		moBody.observe(bodyEl, {
			attributes: true,
			attributeFilter: ['class']
		});

		// ALSO watch the modal-tags container for newly added children (modal creates tags dynamically)
		if (modalTagsContainer) {
			const moModal = new MutationObserver(mutations => {
				// if child nodes are added, ensure datasets and re-apply theme
				for (const m of mutations) {
					if (m.type === 'childList' && m.addedNodes && m.addedNodes.length) {
						m.addedNodes.forEach(node => {
							if (node.nodeType === 1 && node.classList && node.classList.contains('tag-transition')) {
								ensureTagDatasets(node);
							}
						});
						// apply theme to include the newly added tags
						applyThemeToTags();
					}
				}
			});
			moModal.observe(modalTagsContainer, {
				childList: true
			});
		}

		// storage event (multi-tab sync)
		window.addEventListener('storage', function(e) {
			if (e.key === 'theme') {
				const val = e.newValue;
				if (val === 'dark') {
					htmlEl.classList.add('dark');
					bodyEl.classList.add('dark');
				} else {
					htmlEl.classList.remove('dark');
					bodyEl.classList.remove('dark');
				}
				// ensure tags update
				setTimeout(applyThemeToTags, 6);
			}
		});

		const themeToggleBtn = document.getElementById('themeToggle');
		if (themeToggleBtn) {
			themeToggleBtn.addEventListener('click', () => {
				setTimeout(applyThemeToTags, 8);
			});
		}
	});
</script>