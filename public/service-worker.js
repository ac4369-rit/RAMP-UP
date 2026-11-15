// Ramp-Up service worker.
// - CSS, JS and icons: served from the cache first (they only change with a new CACHE_NAME).
// - Pages: always asked from the network first, so people see fresh data;
//   if there is no connection, the last saved copy of that page is shown,
//   and if there is none, the offline page.
const CACHE_NAME = "ramp-up-v2";

const FILES_TO_CACHE = [
    "/offline",
    "/assets/css/style.css?v=2",
    "/assets/js/app.js",
    "/assets/img/icon.svg",
    "/manifest.json"
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(FILES_TO_CACHE))
            .then(() => self.skipWaiting())
    );
});

// Delete caches from older versions
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", (event) => {
    const request = event.request;

    // Only GET requests on our own site; forms (POST) always go to the server.
    if (request.method !== "GET" || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    if (request.mode === "navigate") {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const copy = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(() => caches.match(request).then((saved) => saved || caches.match("/offline")))
        );
        return;
    }

    event.respondWith(
        caches.match(request).then((saved) => saved || fetch(request))
    );
});
