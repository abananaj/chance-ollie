document.addEventListener("DOMContentLoaded", () => {
    const menuItems = document.querySelectorAll(".menu-item > a");

    menuItems.forEach((item) => {
        item.addEventListener("click", (e) => {
            e.preventDefault();
            const parentLi = item.parentElement;

            // Toggle active class on parent
            parentLi.classList.toggle("active");
        });
    });
});

