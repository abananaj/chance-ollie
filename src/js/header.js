
// import gsap from "gsap";
console.log("header.js loaded");
import initAdminBar from "./wp-admin-bar.js";

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  if (!header) return;

  const submenus = document.querySelectorAll(".ct-submenu");
  const soon = document.getElementById("comingSoon");

  // Hide submenus initially
  submenus.forEach((submenu) => submenu.classList.add("hidden"));
  if (soon) soon.classList.add("hidden");

  function measureCollapsedHeaderHeight() {
    const wasMobileOpen = header.classList.contains("mobile-open");
    const visibleSubmenus = [];
    const soonWasVisible = !!soon && !soon.classList.contains("hidden");

    submenus.forEach((submenu) => {
      if (!submenu.classList.contains("hidden")) {
        visibleSubmenus.push(submenu);
      }
      submenu.classList.add("hidden");
    });

    if (soon) soon.classList.add("hidden");
    header.classList.remove("mobile-open");

    const height = header.offsetHeight;

    if (wasMobileOpen) header.classList.add("mobile-open");
    visibleSubmenus.forEach((submenu) => submenu.classList.remove("hidden"));
    if (soon && soonWasVisible) soon.classList.remove("hidden");

    return height;
  }

  let collapsedHeaderHeight = measureCollapsedHeaderHeight();

  let spacer = document.getElementById("chanceHeaderSpacer");
  if (!spacer) {
    spacer = document.createElement("div");
    spacer.id = "chanceHeaderSpacer";
    header.insertAdjacentElement("afterend", spacer);
  }

  function applyHeaderSpacer() {
    const headerPosition = window.getComputedStyle(header).position;
    const height = headerPosition === "fixed" ? collapsedHeaderHeight : 0;
    spacer.style.height = `${height}px`;
    document.documentElement.style.setProperty("--header-height", `${height}px`);
  }

  applyHeaderSpacer();

  const resizeObserver = new ResizeObserver(() => {
    collapsedHeaderHeight = measureCollapsedHeaderHeight();
    applyHeaderSpacer();
  });
  resizeObserver.observe(header);

  initAdminBar(header);

  // --- Submenu toggle ---
  let submenuTimeout;

  header.addEventListener("mouseenter", () => {
    clearTimeout(submenuTimeout);
    submenuTimeout = setTimeout(() => {
      submenus.forEach((submenu) => submenu.classList.remove("hidden"));
      if (soon) soon.classList.remove("hidden");
    }, 100);
  });

  header.addEventListener("mouseleave", () => {
    clearTimeout(submenuTimeout);
    submenuTimeout = setTimeout(() => {
      submenus.forEach((submenu) => submenu.classList.add("hidden"));
      if (soon) soon.classList.add("hidden");
    }, 100);
  });

  // --- Mobile menu toggle ---
  const navToggle = document.getElementById("navToggle");
  const mobileNav = document.getElementById("mobileNav");

  if (navToggle) {
    navToggle.addEventListener("click", (e) => {
      e.preventDefault();
      header.classList.toggle("mobile-open");
    });

    document.addEventListener("click", (e) => {
      const target = e.target;
      if (!(target instanceof Node)) return;

      const clickedToggle = navToggle.contains(target);
      const clickedHeader = header.contains(target);
      const clickedMobileNav = mobileNav ? mobileNav.contains(target) : false;

      if (!clickedToggle && !clickedHeader && !clickedMobileNav) {
        header.classList.remove("mobile-open");
      }
    });
  }
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", siteHeader);
} else {
  siteHeader();
}