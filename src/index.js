console.log("Hello from Chance Ollie index.js!");

import "./index.scss";
import siteHeader from "./js/header.js";
import initAdminBar from "./js/wp-admin-bar.js";
import { initFormEffects, initFormEffectsOnDOMReady } from "./js/forms.js";
import { siteFooter, initFooter } from "./js/footer.js";

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', siteHeader);
} else {
  siteHeader();
}

const logo = document.getElementById('siteTitle');
const ogLogo = document.getElementById('og-logo');
const newLogo = document.getElementById('new-logo');

function swapLogo() {
    if (ogLogo.style.display === "block") {
        ogLogo.style.display = "none";
        newLogo.style.display = "block";
        console.log("Swapped to new logo");
    } else {
        ogLogo.style.display = "block";
        newLogo.style.display = "none";
        console.log("Swapped back to OG logo");
    }
}

// Passed as a reference (no parentheses)
logo.addEventListener('click', swapLogo);