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

// Fetch Event: Do not intercept POST, API, messages, or polling routes
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return; 
    }

    const url = new URL(event.request.url);
    // Let dynamic routes pass straight through without Service Worker interference
    if (url.pathname.startsWith('/messages') || 
        url.pathname.startsWith('/api') || 
        url.pathname.startsWith('/audit-logs') || 
        url.pathname.startsWith('/alerts') ||
        url.pathname.startsWith('/dashboard') ||
        url.pathname.startsWith('/user-database') ||
        url.pathname.startsWith('/users') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/login') ||
        url.search) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request).catch(() => {
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
                return new Response('Network unavailable', {
                    status: 503,
                    headers: { 'Content-Type': 'text/plain' }
                });
            });
        })
    );
});

// Handle Notification Click (brings window to focus and navigates directly to targetUrl)
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    client.focus();
                    if ('navigate' in client) {
                        return client.navigate(targetUrl);
                    }
                    return;
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Handle Background Push Event (Web Push API)
self.addEventListener('push', (event) => {
    let data = { 
        title: '🔔 SecureLab Smart Access', 
        body: 'New security update available.', 
        url: '/',
        type: 'general',
        icon: '/assets/img/tpc-logo.jpg',
        badge: '/assets/img/tpc-logo.jpg',
        vibrate: [200, 100, 200, 100, 200]
    };

    try {
        if (event.data) {
            const parsed = event.data.json();
            data = Object.assign(data, parsed);
        }
    } catch (e) {
        data.body = event.data ? event.data.text() : data.body;
    }

    const options = {
        body: data.body,
        icon: data.icon || '/assets/img/tpc-logo.jpg',
        badge: data.badge || '/assets/img/tpc-logo.jpg',
        vibrate: data.vibrate || [200, 100, 200],
        tag: data.tag || ('securelab-' + Date.now()),
        renotify: true,
        data: { 
            url: data.url || '/',
            type: data.type || 'general'
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});