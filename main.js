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

menuBtn.addEventListener("click", () => {
	mobileMenu.classList.toggle("hidden");
});

const modal = document.getElementById("facility-modal");
const title = document.getElementById("modal-title");
const content = document.getElementById("modal-content");

const facilityData = {
	prototyping: {
		title: "Rapid Prototyping Lab",
		content:
			"Equipped with 3D printers, CNC machines, laser cutters, and fabrication tools enabling fast product development."
	},
	electronics: {
		title: "Electronics & IoT Lab",
		content:
			"Supports embedded systems, sensor integration, PCB design, and IoT-based innovation projects."
	},
	design: {
		title: "Design & Simulation Studio",
		content:
			"Advanced CAD, CAE, and simulation software to validate and optimize designs before fabrication."
	}
};

function openModal(key) {
	title.textContent = facilityData[key].title;
	content.textContent = facilityData[key].content;
	modal.classList.remove("hidden");
	modal.classList.add("flex");
}

function closeModal() {
	modal.classList.add("hidden");
	modal.classList.remove("flex");
}

  const filterButtons = document.querySelectorAll(".filter-btn");
  const projectCards = document.querySelectorAll(".project-card");

  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      filterButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.getAttribute("data-filter");

      projectCards.forEach(card => {
        if (filter === "all" || card.dataset.category === filter) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  });