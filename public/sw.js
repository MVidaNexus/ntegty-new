const CACHE_NAME = 'ntegty-cache-v1';
const urlsToCache = [
  '/',
  '/manifest.json',
  '/images/icon-192x192.png',
  '/images/icon-512x512.png'
  // CSS and JS will be cached dynamically since Vite uses hash in filenames
];

// Install Event - cache core assets
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate Event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event - network first, then cache
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  
  // Don't cache admin, API, or dynamic user-specific pages
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/livewire')) {
      return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Only cache successful responses (exclude 5xx or 404s)
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        
        // Clone the response because the stream can only be consumed once
        const responseToCache = response.clone();
        
        caches.open(CACHE_NAME)
          .then(cache => {
            cache.put(event.request, responseToCache);
          });
          
        return response;
      })
      .catch(() => {
        // Fallback to cache if network fails (Offline mode)
        return caches.match(event.request);
      })
  );
});
