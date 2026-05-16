
import gsap from "gsap";
console.log("Hello from header!");
const header = document.getElementById("chanceHeader");
const title = document.getElementById("siteTitle");
const search = document.getElementById("searchBox");
const soon = document.getElementById("comingSoon");
const social = document.getElementById("socialLinks");
const onstage = document.getElementById("onstageMenu");
const join = document.getElementById("joinMenu");
const give = document.getElementById("donateMenu");
const education = document.getElementById("educationMenu");
const backstage = document.getElementById("backstageMenu");

const submenus = document.querySelectorAll("ct-submenu");


export default function siteHeader() {
  console.log("siteHeader");
}


// if (document.readyState === 'loading') {
//   document.addEventListener('DOMContentLoaded', siteHeader);
// } else {
//   siteHeader();
// }
