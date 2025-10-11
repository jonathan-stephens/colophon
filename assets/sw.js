// Service Worker for PWA with Share Target support
const CACHE_NAME = 'bookmarks-pwa-v3';
const urlsToCache = [
  '/',
  '/links'
  // Don't pre-cache /share - it needs fresh data
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
    console.log('Service Worker: Full URL:', request.url);
    console.log('Service Worker: Request headers:', [...request.headers.entries()]);

    event.respondWith(
      (async () => {
        try {
          // Clone request to read body
          const formData = await request.formData();
          const title = formData.get('title') || '';
          const text = formData.get('text') || '';
          const sharedUrl = formData.get('url') || '';

          console.log('Service Worker: 📦 Share data extracted:', {
            title,
            text,
            url: sharedUrl
          });

          // Build GET parameters
          const params = new URLSearchParams();
          if (sharedUrl) params.set('url', sharedUrl);
          if (title) params.set('title', title);
          if (text) params.set('text', text);

          // Build full redirect URL with proper origin
          const redirectUrl = `${url.origin}/share?${params.toString()}`;
          console.log('Service Worker: ➡️ Full redirect URL:', redirectUrl);

          // Use 303 redirect to convert POST to GET
          const response = Response.redirect(redirectUrl, 303);
          console.log('Service Worker: ✅ Redirect response created');
          return response;
        } catch (error) {
          console.error('Service Worker: ❌ Error processing share:', error);
          console.error('Service Worker: Error stack:', error.stack);
          // Fallback: just redirect to share page without data
          return Response.redirect(`${url.origin}/share`, 303);
        }
      })()
    );
    return;
  }

  // Handle all other requests (GET, etc.)
  // NEVER cache /share page - it needs authentication and fresh data
  if (url.pathname === '/share' || url.pathname.startsWith('/api/')) {
    console.log('Service Worker: 🌐 Network-only for:', request.url);
    event.respondWith(fetch(request));
    return;
  }

  // For everything else, try network first, then cache
  event.respondWith(
    fetch(request)
      .then(response => {
        console.log('Service Worker: 🌐 Network response for:', request.url);

        // Don't cache non-successful responses or non-GET requests
        if (!response || response.status !== 200 || request.method !== 'GET') {
          return response;
        }

        // Only cache basic responses (same-origin)
        if (response.type === 'basic') {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(request, responseToCache);
          });
        }

        return response;
      })
      .catch(err => {
        console.log('Service Worker: ⚠️ Network failed, trying cache for:', request.url);
        return caches.match(request).then(cachedResponse => {
          if (cachedResponse) {
            console.log('Service Worker: 📦 Serving from cache:', request.url);
            return cachedResponse;
          }
          console.error('Service Worker: ❌ No cache available for:', request.url);
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
