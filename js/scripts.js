/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});

// Array of page links in sequence
const pages = [
    "sshAttackAi.html",
    "sshAttackAii.html",
    "sshAttackBi.html",
    "sshAttackBii.html",
    "sshDefendA.html",
    "sshDefendB.html",
    "sshDefendC.html"
];

// Get the current page URL
const currentPage = window.location.pathname.split("/").pop();

// Find the index of the current page in the pages array
const currentIndex = pages.indexOf(currentPage);

// Select the back and next buttons
const backButton = document.querySelector(".back-button");
const nextButton = document.querySelector(".next-button");

// Set the back and next buttons' href if there's a previous or next page
if (currentIndex > 0) {
    backButton.href = pages[currentIndex - 1];
} else {
    backButton.style.display = "none"; // Hide if no previous page
}

if (currentIndex < pages.length - 1) {
    nextButton.href = pages[currentIndex + 1];
} else {
    nextButton.style.display = "none"; // Hide if no next page
}

// Highlight the current page link
document.querySelectorAll(".nav-link").forEach(link => {
    if (link.getAttribute("href") === currentPage) {
        link.classList.add("active");
    }
});
