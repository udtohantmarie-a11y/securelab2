const CACHE_NAME = 'securelab-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('SecureLab: Caching minimal assets');
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch Event: Dito natin aayusin ang error
self.addEventListener('fetch', (event) => {
    // 1. IMPORTANTE: Huwag i-cache ang POST requests (Login/Logout)
    // Ang Service Worker ay para sa GET requests lang (images, css, html)
    if (event.request.method !== 'GET') {
        return; 
    }

    event.respondWith(
        caches.match(event.request).then((response) => {
            // I-return ang cache kung meron, kung wala ay mag-network fetch
            return response || fetch(event.request).catch(() => {
                // Offline fallback para sa navigation
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
                // Siguraduhin na laging may valid na Response para hindi mag-error
                return new Response('Network error occurred', {
                    status: 408,
                    headers: { 'Content-Type': 'text/plain' }
                });
            });
        })
    );
});