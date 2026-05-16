console.log("Hello from Chance Ollie index.js!");

import "./index.scss";
import siteHeader from "./js/header.js";
import {initFormEffects,initFormEffectsOnDOMReady} from "./js/forms.js";
import { siteFooter, initFooter } from "./js/footer.js";

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', siteHeader);
} else {
  siteHeader();
}
