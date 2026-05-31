function e(e) {
  let t = document.getElementById(`wpadminbar`);
  function n() {
    let n = t ? t.offsetHeight : 0;
    e.style.top = `${n}px`;
  }
  (n(), window.addEventListener(`resize`, n));
  function r() {
    let n = t ? t.offsetHeight : 0;
    (t ? t.getBoundingClientRect().top : 0) >= 0
      ? (e.style.top = `${n}px`)
      : (e.style.top = `0`);
  }
  (r(),
    t &&
    (t.addEventListener(`scroll`, r),
      window.addEventListener(`scroll`, r)));
}

// import gsap from "gsap";
console.log("header.js loaded");

function siteHeader() {
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

  function applyMainOffset() {
    const main = document.querySelector("main");
    if (!main) return;
    const headerPosition = window.getComputedStyle(header).position;
    const needsOffset = headerPosition === "fixed";
    main.style.marginTop = needsOffset ? `${collapsedHeaderHeight}px` : "0px";
  }

  applyMainOffset();
  window.addEventListener("resize", () => {
    collapsedHeaderHeight = measureCollapsedHeaderHeight();
    applyMainOffset();
  });
  e(header);

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

      console.log("mobile-open");
    });

    document.addEventListener("click", (e) => {
      const target = e.target;
      if (!(target instanceof Node)) return;

      const clickedToggle = navToggle.contains(target);
      const clickedHeader = header.contains(target);
      const clickedMobileNav = mobileNav
        ? mobileNav.contains(target)
        : false;

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
console.log(`Hello from forms!`);
function n() {
  console.log(`siteFooter`);
}
function r() {
  (n(), console.log(`initFooter`));
}
(document.readyState === `loading`
  ? document.addEventListener(`DOMContentLoaded`, r)
  : r(),
  console.log(`Hello from Chance Ollie index.js!`),
  void 0);
var i = document.getElementById(`siteTitle`),
  a = document.getElementById(`og-logo`),
  o = document.getElementById(`new-logo`);
function s() {
  if (!a || !o) return;
  a.style.display === `block`
    ? ((a.style.display = `none`),
      (o.style.display = `block`),
      console.log(`Swapped to new logo`))
    : ((a.style.display = `block`),
      (o.style.display = `none`),
      console.log(`Swapped back to OG logo`));
}
i && i.addEventListener(`click`, s);
