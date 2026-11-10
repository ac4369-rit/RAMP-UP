function showPage(pageId) {
    var pages = document.querySelectorAll(".page");

    pages.forEach(function(page) {
        page.classList.remove("active");
    });

    document.getElementById(pageId).classList.add("active");
}

document.getElementById("blocked-form").addEventListener("submit", function(event) {
    event.preventDefault();

    document.getElementById("blocked-message").textContent =
        "Your task has been marked as blocked.";

    document.getElementById("blocked-form").reset();
});

document.getElementById("progress-form").addEventListener("submit", function(event) {
    event.preventDefault();

    document.getElementById("progress-message").textContent =
        "Your progress has been updated.";

    document.getElementById("progress-form").reset();
});

document.getElementById("review-form").addEventListener("submit", function(event) {
    event.preventDefault();

    document.getElementById("review-message").textContent =
        "Your task has been submitted for review.";

    document.getElementById("review-form").reset();
});

if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/service-worker.js");
}