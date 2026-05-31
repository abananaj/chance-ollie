
// import gsap from "gsap";
console.log("header.js loaded");
import initAdminBar from "./wp-admin-bar.js";

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  if (!header) return;

  function applyMainOffset() {
    const main = document.querySelector("main");
    if (!main) return;
    const headerPosition = window.getComputedStyle(header).position;
    const needsOffset = headerPosition === "fixed";
    main.style.marginTop = needsOffset ? `${header.offsetHeight}px` : "0px";
  }

  applyMainOffset();
  window.addEventListener("resize", applyMainOffset);
  initAdminBar(header);

  const submenus = document.querySelectorAll(".ct-submenu");
  const soon = document.getElementById("comingSoon");

  // Hide submenus initially
  submenus.forEach((submenu) => submenu.classList.add("hidden"));
  if (soon) soon.classList.add("hidden");

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
      requestAnimationFrame(applyMainOffset);
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