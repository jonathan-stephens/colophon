// Service Worker for PWA with Share Target support
const CACHE_NAME = 'bookmarks-pwa-v2';
const urlsToCache = [
  '/',
  '/share',
  '/links'
];

// Install event
self.addEventListener('install', event => {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Opened cache');
        return cache.addAll(urlsToCache);
      })
      .catch(err => {
        console.error('Service Worker: Cache install error:', err);
      })
      .then(() => {
        console.log('Service Worker: Skip waiting');
        return self.skipWaiting();
      })
  );
});

// Activate event
self.addEventListener('activate', event => {
  console.log('Service Worker: Activating...');
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            console.log('Service Worker: Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('Service Worker: Claiming clients');
      return self.clients.claim();
    })
  );
});

// Fetch event - CRITICAL: Handle share target POST requests
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Handle share target POST requests from Android
  if (request.method === 'POST' && url.pathname === '/share') {
    console.log('Service Worker: 🎯 Share POST request detected');

    event.respondWith(
      (async () => {
        try {
          // Extract form data from POST request
          const formData = await request.formData();
          const title = formData.get('title') || '';
          const text = formData.get('text') || '';
          const sharedUrl = formData.get('url') || '';

          console.log('Service Worker: 📦 Share data:', {
            title,
            text,
            url: sharedUrl
          });

          // Build GET parameters
          const params = new URLSearchParams();
          if (sharedUrl) params.set('url', sharedUrl);
          if (title) params.set('title', title);
          if (text) params.set('text', text);

          // Redirect to share page with GET parameters
          const redirectUrl = `/share?${params.toString()}`;
          console.log('Service Worker: ➡️ Redirecting to:', redirectUrl);

          // Use 303 redirect to convert POST to GET
          return Response.redirect(redirectUrl, 303);
        } catch (error) {
          console.error('Service Worker: ❌ Error processing share:', error);
          // Fallback: just redirect to share page without data
          return Response.redirect('/share', 303);
        }
      })()
    );
    return;
  }

  // Handle all other requests (GET, etc.)
  event.respondWith(
    caches.match(request)
      .then(response => {
        if (response) {
          console.log('Service Worker: 📦 Serving from cache:', request.url);
          return response;
        }

        console.log('Service Worker: 🌐 Fetching from network:', request.url);
        const fetchRequest = request.clone();

        return fetch(fetchRequest).then(response => {
          // Don't cache non-successful responses
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Cache successful GET requests
          if (request.method === 'GET') {
            const responseToCache = response.clone();
            caches.open(CACHE_NAME).then(cache => {
              cache.put(request, responseToCache);
            });
          }

          return response;
        }).catch(err => {
          console.error('Service Worker: Fetch failed:', err);
          throw err;
        });
      })
  );
});

// Message event - for cache control
self.addEventListener('message', event => {
  console.log('Service Worker: Message received:', event.data);

  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.delete(CACHE_NAME).then(() => {
      console.log('Service Worker: Cache cleared');
      event.ports[0].postMessage({ cleared: true });
    });
  }
});
