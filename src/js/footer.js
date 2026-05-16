
function siteFooter() {
	
	console.log("siteFooter");

	// const siteContent = document.getElementById('site-content');
	// const siteContentHeight = siteContent.offsetHeight;
	// const siteContentWidth = siteContent.offsetWidth;

	// const siteFooter = document.getElementById('site-footer');
	// const siteFooterHeight = siteFooter.offsetHeight;
	// const siteFooterWidth = siteFooter.offsetWidth;

	// console.log('Content Height = ' + siteContentHeight + 'px');
	// console.log('Content Width = ' + siteContentWidth + 'px');
	// console.log('Footer Height = ' + siteFooterHeight + 'px');
	// console.log('Footer Width = ' + siteFooterWidth + 'px');

	// siteContent.style.marginBottom = siteFooterHeight + 50 + 'px';
}

function initFooter() {
	siteFooter();
	// window.addEventListener('resize', siteFooter);
	console.log("initFooter");
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initFooter);
} else {
	initFooter();
}

export { siteFooter, initFooter };