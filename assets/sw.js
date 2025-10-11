const CACHE_NAME = "bookmarks-pwa-v4"; // Increment version to force update
const urlsToCache = ["/", "/share"];

// ============================================
// INSTALL EVENT
// ============================================
self.addEventListener("install", (e) => {
  console.log("🔧 Service Worker: Installing v4...");
  e.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => {
        console.log("✅ Service Worker: Opened cache");
        return cache.addAll(urlsToCache);
      })
      .catch((err) => {
        console.error("❌ Service Worker: Cache install error:", err);
      })
      .then(() => {
        console.log("⏭️ Service Worker: Skip waiting");
        return self.skipWaiting();
      })
  );
});

// ============================================
// ACTIVATE EVENT
// ============================================
self.addEventListener("activate", (e) => {
  console.log("🚀 Service Worker: Activating v4...");
  const cacheWhitelist = [CACHE_NAME];
  e.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheWhitelist.indexOf(cacheName) === -1) {
              console.log("🗑️ Service Worker: Deleting old cache:", cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log("👑 Service Worker: Claiming clients");
        return self.clients.claim();
      })
  );
});

// ============================================
// FETCH EVENT - THE CRITICAL PART
// ============================================
self.addEventListener("fetch", (e) => {
  const { request } = e;
  const url = new URL(request.url);

  console.log(`📡 SW Fetch: ${request.method} ${url.pathname}`);

  // ============================================
  // SHARE TARGET POST HANDLER - MOST CRITICAL
  // ============================================
  if (request.method === "POST" && url.pathname === "/share") {
    console.log("🎯 SHARE TARGET DETECTED!");
    console.log("🎯 Full URL:", request.url);
    console.log("🎯 Origin:", url.origin);
    console.log("🎯 Pathname:", url.pathname);

    e.respondWith(
      (async () => {
        try {
          console.log("📦 Extracting form data...");

          // Clone the request before reading body (can only be read once)
          const formData = await request.clone().formData();

          const sharedUrl = formData.get("url") || "";
          const sharedTitle = formData.get("title") || "";
          const sharedText = formData.get("text") || "";

          console.log("📦 Share data extracted:");
          console.log("  - URL:", sharedUrl);
          console.log("  - Title:", sharedTitle);
          console.log("  - Text:", sharedText);

          // Build redirect URL with GET parameters
          const params = new URLSearchParams();
          if (sharedUrl) params.set("url", sharedUrl);
          if (sharedTitle) params.set("title", sharedTitle);
          if (sharedText) params.set("text", sharedText);

          // Use url.origin to ensure we're using the correct base
          const redirectUrl = `${url.origin}/share?${params.toString()}`;

          console.log("➡️ Redirecting to:", redirectUrl);

          // Use 303 See Other - this forces the browser to use GET for the redirect
          const response = Response.redirect(redirectUrl, 303);

          console.log("✅ Redirect response created");
          return response;

        } catch (err) {
          console.error("❌ Error processing share:", err);
          console.error("❌ Error stack:", err.stack);

          // Fallback: redirect to share page without params
          return Response.redirect(`${url.origin}/share`, 303);
        }
      })()
    );
    return; // Important: exit early
  }

  // ============================================
  // HANDLE GET /share - Network only (don't cache)
  // ============================================
  if (url.pathname === "/share") {
    console.log("🌐 GET /share - Network only");
    e.respondWith(
      fetch(request).catch((err) => {
        console.error("❌ Network failed for /share:", err);
        return new Response("Offline - cannot load share page", {
          status: 503,
          statusText: "Service Unavailable"
        });
      })
    );
    return;
  }

  // ============================================
  // HANDLE API calls - Network only
  // ============================================
  if (url.pathname.startsWith("/api/")) {
    console.log("🌐 API call - Network only:", url.pathname);
    e.respondWith(fetch(request));
    return;
  }

  // ============================================
  // HANDLE all other requests - Network first, cache fallback
  // ============================================
  e.respondWith(
    fetch(request)
      .then((response) => {
        console.log("🌐 Network response for:", url.pathname);

        // Only cache successful GET requests
        if (
          !response ||
          response.status !== 200 ||
          request.method !== "GET"
        ) {
          return response;
        }

        // Only cache basic responses (not opaque)
        if (response.type === "basic") {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, responseToCache);
          });
        }

        return response;
      })
      .catch((err) => {
        console.log("⚠️ Network failed, trying cache for:", url.pathname);
        return caches.match(request).then((response) => {
          if (response) {
            console.log("📦 Serving from cache:", url.pathname);
            return response;
          }
          console.error("❌ No cache available for:", url.pathname);
          throw err;
        });
      })
  );
});

// ============================================
// MESSAGE EVENT - For manual control
// ============================================
self.addEventListener("message", (e) => {
  console.log("💬 Service Worker: Message received:", e.data);

  if (e.data && e.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  }

  if (e.data && e.data.type === "CLEAR_CACHE") {
    caches.delete(CACHE_NAME).then(() => {
      console.log("🗑️ Service Worker: Cache cleared");
      e.ports[0].postMessage({ cleared: true });
    });
  }

  if (e.data && e.data.type === "CHECK_STATUS") {
    e.ports[0].postMessage({
      active: true,
      version: CACHE_NAME,
      scope: self.registration.scope
    });
  }
});

console.log("✅ Service Worker v4 script loaded");
