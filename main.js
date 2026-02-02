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
	additive: {
		title: "Additive Manufacturing Lab",
		content:
			"Advanced additive manufacturing facilities including industrial-grade 3D printers for rapid prototyping, functional parts, and complex geometries."
	},
	digital: {
		title: "Digital Manufacturing Lab",
		content:
			"Digitally enabled manufacturing tools such as CNC machining, laser cutting, and precision fabrication systems supporting smart production workflows."
	},
	iot: {
		title: "IoT & Automation Lab",
		content:
			"Comprehensive IoT and automation setup with sensors, microcontrollers, PLCs, and real-time data acquisition for smart systems development."
	},
	pcb: {
		title: "Chip / PCB Design Lab",
		content:
			"Facilities for electronic circuit design, schematic development, PCB layout, prototyping, and hardware testing for embedded applications."
	},
	product: {
		title: "Product Design Lab",
		content:
			"Design thinking-driven product development lab focused on ideation, ergonomics, user experience, and physical product realization."
	},
	design: {
		title: "Design & Simulation Studio",
		content:
			"Advanced CAD, CAE, and simulation software to validate, analyze, and optimize designs before manufacturing."
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