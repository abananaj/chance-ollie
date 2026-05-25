
// import gsap from "gsap";
console.log("header.js loaded");

import initAdminBar from "./wp-admin-bar.js";

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  const submenus = document.querySelectorAll(".ct-submenu:not(#mobileNav .ct-submenu)");
  const soon = document.getElementById("comingSoon");
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
  // --- Header spacing adjustment ---
  function setHeaderSpacing() {
    const main = document.querySelector("main");
    if (main && header) {
      main.style.marginTop = `${header.offsetHeight}px`;
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", setHeaderSpacing);
  } else {
    setHeaderSpacing();
  }

  // --- Admin bar positioning ---
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
}

addEventListener("load", siteHeader);