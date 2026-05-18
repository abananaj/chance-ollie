
// import gsap from "gsap";
console.log("header.js loaded");

export default function siteHeader() {
  const header = document.getElementById("chanceHeader");
  //  hide 
  const submenus = document.querySelectorAll(".ct-submenu");
  const soon = document.getElementById("comingSoon");

  // console.log("siteHeader called");
  // console.log("header element:", header);

  // Toggle each submenu
  submenus.forEach((submenu) => {
    submenu.classList.add("hidden");
  });

  // Toggle coming soon if it exists
  if (soon) {
    soon.classList.add("hidden");
  }

  header.addEventListener("mouseenter", () => {

    // Toggle each submenu
    submenus.forEach((submenu) => {
      submenu.classList.remove("hidden");
    });

    // Toggle coming soon if it exists
    if (soon) {
      soon.classList.remove("hidden");
    }
  });

  header.addEventListener("mouseleave", () => {

    // Hide each submenu again
    submenus.forEach((submenu) => {
      submenu.classList.add("hidden");
    });

    // Hide coming soon if it exists
    if (soon) {
      soon.classList.add("hidden");
    }
  });
}

addEventListener("load", siteHeader);