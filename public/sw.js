self.addEventListener('install', (e) => {
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    // Dummy activate
});

self.addEventListener('fetch', (e) => {
    // Fallback to network
});
