
// import gsap from "gsap";
console.log("header.js loaded");

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  const submenus = document.querySelectorAll(".ct-submenu");
  const soon = document.getElementById("comingSoon");
  const adminBar = document.getElementById("wpadminbar");
  const menuToggle = document.querySelector(".ct-menu-toggle");
  const mobileNav = document.getElementById("mobileNav");

  // Hide submenus initially
  submenus.forEach((submenu) => submenu.classList.add("hidden"));
  if (soon) soon.classList.add("hidden");

  // --- Menu toggle functionality ---
  if (menuToggle && mobileNav) {
    menuToggle.addEventListener("click", (e) => {
      e.preventDefault();
      mobileNav.classList.toggle("visible");
    });

    // Close mobile nav when clicking outside
    document.addEventListener("click", (e) => {
      if (!menuToggle.contains(e.target) && !mobileNav.contains(e.target)) {
        mobileNav.classList.remove("visible");
      }
    });
  }

  // --- Positioning ---
  const firstBlock = document.querySelector(
    "main > div.wp-block-group, .wp-site-blocks > div.wp-block-group"
  );

  function applyOffsets() {
    const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
    header.style.top = `${adminBarHeight}px`;

    const headerHeight = header.offsetHeight;
    document.documentElement.style.setProperty(
      "--header-height",
      `${headerHeight}px`
    );

    if (firstBlock) {
      firstBlock.style.marginTop = `${headerHeight}px`;
    }
  }

  applyOffsets();
  window.addEventListener("resize", applyOffsets);

  // --- Handle admin bar scroll on mobile ---
  function handleAdminBarScroll() {
    const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
    const adminBarTop = adminBar ? adminBar.getBoundingClientRect().top : 0;
    const adminBarVisible = adminBarTop >= 0;

    if (adminBarVisible) {
      // Admin bar is visible, keep header positioned below it
      header.style.top = `${adminBarHeight}px`;
    } else {
      // Admin bar scrolled out of view, move header to top of viewport
      header.style.top = "0";
    }
  }

  // Initial check
  handleAdminBarScroll();

  // Listen for admin bar scroll events
  if (adminBar) {
    adminBar.addEventListener("scroll", handleAdminBarScroll);
    // Also listen to window scroll to detect admin bar movement
    window.addEventListener("scroll", handleAdminBarScroll);
  }

  // --- Submenu toggle ---
  header.addEventListener("mouseenter", () => {
    submenus.forEach((submenu) => submenu.classList.remove("hidden"));
    if (soon) soon.classList.remove("hidden");
  });

  header.addEventListener("mouseleave", () => {
    submenus.forEach((submenu) => submenu.classList.add("hidden"));
    if (soon) soon.classList.add("hidden");
  });
}

addEventListener("load", siteHeader);