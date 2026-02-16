const header = document.getElementById("site-header");
const navbar = document.getElementById("navbar");
const menuBtn = document.getElementById("menu-btn");
const mobileMenu = document.getElementById("mobile-menu");

window.addEventListener("scroll", () => {
	if (window.scrollY > 50) {
		navbar.classList.remove("h-20");
		navbar.classList.add("h-16", "shadow-md");
	} else {
		navbar.classList.add("h-20");
		navbar.classList.remove("h-16", "shadow-md");
	}
});

const openMenuIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-text-align-end-icon lucide-text-align-end"><path d="M21 5H3"/><path d="M21 12H9"/><path d="M21 19H7"/></svg>';
const closeMenuIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';

menuBtn.addEventListener("click", () => {
	mobileMenu.classList.toggle("hidden");
  
	const expanded = menuBtn.getAttribute("aria-expanded") === "true";
	menuBtn.setAttribute("aria-expanded", !expanded);
	menuBtn.innerHTML = expanded ? openMenuIcon : closeMenuIcon;
  });
  // Close mobile menu when a menu item is clicked
  const mobileLinks = mobileMenu.querySelectorAll("a");
  
  mobileLinks.forEach(link => {
	link.addEventListener("click", () => {
	  mobileMenu.classList.add("hidden");
  
	  // Reset hamburger icon & aria
	  menuBtn.setAttribute("aria-expanded", "false");
	  menuBtn.innerHTML = openMenuIcon;
	});
  });
  
  window.addEventListener("resize", () => {
	if (window.innerWidth >= 1024) {
	  mobileMenu.classList.add("hidden");
	  menuBtn.innerHTML = openMenuIcon;
	  menuBtn.setAttribute("aria-expanded", "false");
	}
  });