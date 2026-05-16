
console.log("Hello from forms!");
/**
 * Initialize form effects on inputs with 'underline' or 'label-bg' classes
 */
export function initFormEffects() {
  console.log("initFormEffects!");
  // const inputs = document.querySelectorAll('.ct-form-input input');

  // inputs.forEach(input => {
  //   const container = input.closest('.ct-form-input');

  //   // Add 'has-content' class on input if it already has a value
  //   if (input.value) {
  //     container.classList.add('has-content');
  //   }

  //   // Add 'has-content' class on focus
  //   input.addEventListener('focus', () => {
  //     container.classList.add('has-content');
  //   });

  //   // Remove 'has-content' class if input is empty and loses focus
  //   input.addEventListener('blur', () => {
  //     if (!input.value) {
  //       container.classList.remove('has-content');
  //     }
  //   });

  //   // Add 'has-content' class on input to track changes
  //   input.addEventListener('input', () => {
  //     if (input.value) {
  //       container.classList.add('has-content');
  //     } else {
  //       container.classList.remove('has-content');
  //     }
  //   });
  // });
}

// Initialize on DOM ready
export function initFormEffectsOnDOMReady() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFormEffects);
  } else {
    initFormEffects();
  }
}
