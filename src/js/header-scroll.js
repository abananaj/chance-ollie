/**
 * GSAP FUNCTIONALITY
 * Optimized header scroll animations with ScrollTrigger
 */

gsap.registerPlugin(ScrollTrigger);

ScrollTrigger.defaults({
	markers: false
});

// Shared ScrollTrigger configurations to avoid repetition
const SCROLL_CONFIG = {
	trigger: ".header-scroll-trigger",
	start: "top top",
	end: "top+=100 top",
	scrub: 1, // 1s smoothing for smoother scrolling feel
	invalidateOnRefresh: true,
	anticipatePin: 1
};

const SECONDARY_NAV_CONFIG = {
	trigger: ".logo",
	start: "top top",
	end: "top+=150 top",
	scrub: 1,
	invalidateOnRefresh: true,
	anticipatePin: 1
};

// Initialize animations on DOM ready
document.addEventListener('DOMContentLoaded', () => {
	const mm = gsap.matchMedia();

	mm.add("(min-width: 961px)", () => {
		// Logo scale animation
		gsap.fromTo(
			".logo",
			{ height: 150, width: 150 },
			{
				height: 75,
				width: 75,
				ease: "power1.inOut",
				scrollTrigger: SCROLL_CONFIG
			}
		);

		// Logo SVG animation
		gsap.to(".logo a svg", {
			height: 50,
			width: 50,
			ease: "power1.inOut",
			scrollTrigger: SCROLL_CONFIG
		});

		// Secondary navigation hide animation
		gsap.to("#secondarynavigation", {
			height: 0,
			opacity: 0,
			ease: "power2.inOut",
			scrollTrigger: SECONDARY_NAV_CONFIG
		});

		// Return cleanup function for when breakpoint changes
		return () => {
			// Animations will automatically revert via matchMedia
		};
	});
});

// Refresh ScrollTrigger after all assets load
window.addEventListener("load", () => {
	ScrollTrigger.refresh();
});