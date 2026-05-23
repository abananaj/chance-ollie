export default function initAdminBar(header) {
  const adminBar = document.getElementById("wpadminbar");

  function applyOffsets() {
    const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
    header.style.top = `${adminBarHeight}px`;
  }

  applyOffsets();
  window.addEventListener("resize", applyOffsets);

  // --- Handle admin bar scroll on mobile ---
  function handleAdminBarScroll() {
    const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
    const adminBarTop = adminBar ? adminBar.getBoundingClientRect().top : 0;
    const adminBarVisible = adminBarTop >= 0;

    if (adminBarVisible) {
      header.style.top = `${adminBarHeight}px`;
    } else {
      header.style.top = "0";
    }
  }

  handleAdminBarScroll();

  if (adminBar) {
    adminBar.addEventListener("scroll", handleAdminBarScroll);
    window.addEventListener("scroll", handleAdminBarScroll);
  }
}
