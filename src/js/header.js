
// import gsap from "gsap";
console.log("header.js loaded");

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  const submenus = document.querySelectorAll(".ct-submenu");
  const soon = document.getElementById("comingSoon");
  const adminBar = document.getElementById("wpadminbar");

  // Hide submenus initially
  submenus.forEach((submenu) => submenu.classList.add("hidden"));
  if (soon) soon.classList.add("hidden");

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