const CACHE_NAME = "bookmarks-pwa-v5"; // Increment to force update
const urlsToCache = ["/", "/share"];

// Log everything during install
self.addEventListener("install", (e) => {
  console.log("🔧 SW v5: INSTALLING");
  e.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log("✅ SW v5: Cache opened");
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        console.log("⏭️ SW v5: Skip waiting");
        return self.skipWaiting();
      })
      .catch((err) => {
        console.error("❌ SW v5: Install error:", err);
      })
  );
});

self.addEventListener("activate", (e) => {
  console.log("🚀 SW v5: ACTIVATING");
  e.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log("🗑️ SW v5: Deleting old cache:", cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log("👑 SW v5: Claiming clients");
        return self.clients.claim();
      })
  );
});

// THE CRITICAL FETCH HANDLER
self.addEventListener("fetch", (e) => {
  const { request } = e;
  const url = new URL(request.url);

  // Log EVERY fetch request
  console.log("📡 SW v5 FETCH:", {
    method: request.method,
    url: request.url,
    pathname: url.pathname,
    origin: url.origin,
    destination: request.destination,
    mode: request.mode
  });

  // =====================================================
  // SHARE TARGET POST HANDLER - MOST CRITICAL
  // =====================================================
  if (request.method === "POST" && url.pathname === "/share") {
    console.log("🎯🎯🎯 SHARE POST DETECTED! 🎯🎯🎯");
    console.log("Full URL:", request.url);
    console.log("Headers:", [...request.headers.entries()]);

    e.respondWith(
      (async () => {
        try {
          console.log("📦 SW v5: Reading form data...");

          // Clone before reading (body can only be read once)
          const clonedRequest = request.clone();
          const formData = await clonedRequest.formData();

          console.log("📦 SW v5: FormData entries:");
          for (const [key, value] of formData.entries()) {
            console.log(`  - ${key}: ${value}`);
          }

          const sharedUrl = formData.get("url") || "";
          const sharedTitle = formData.get("title") || "";
          const sharedText = formData.get("text") || "";

          console.log("📦 SW v5: Extracted data:", {
            url: sharedUrl,
            title: sharedTitle,
            text: sharedText
          });

          // Build redirect URL
          const params = new URLSearchParams();
          if (sharedUrl) params.set("url", sharedUrl);
          if (sharedTitle) params.set("title", sharedTitle);
          if (sharedText) params.set("text", sharedText);

          const redirectUrl = `${url.origin}/share?${params.toString()}`;

          console.log("➡️ SW v5: Redirecting to:", redirectUrl);
          console.log("➡️ SW v5: Params string:", params.toString());

          // Create redirect response with 303 See Other
          const response = Response.redirect(redirectUrl, 303);

          console.log("✅ SW v5: Redirect response created");
          console.log("Response type:", response.type);
          console.log("Response status:", response.status);
          console.log("Response URL:", response.url);

          return response;

        } catch (err) {
          console.error("❌ SW v5: Error in share handler:", err);
          console.error("Error name:", err.name);
          console.error("Error message:", err.message);
          console.error("Error stack:", err.stack);

          // Fallback
          const fallbackUrl = `${url.origin}/share?error=processing_failed`;
          console.log("⚠️ SW v5: Fallback redirect to:", fallbackUrl);
          return Response.redirect(fallbackUrl, 303);
        }
      })()
    );

    console.log("🎯 SW v5: Share POST handler completed");
    return; // Exit early
  }

  // =====================================================
  // Handle GET /share - Network only
  // =====================================================
  if (url.pathname === "/share") {
    console.log("🌐 SW v5: GET /share - fetching from network");
    e.respondWith(
      fetch(request)
        .then((response) => {
          console.log("✅ SW v5: /share network response:", response.status);
          return response;
        })
        .catch((err) => {
          console.error("❌ SW v5: /share network error:", err);
          throw err;
        })
    );
    return;
  }

  // =====================================================
  // Handle API calls - Network only
  // =====================================================
  if (url.pathname.startsWith("/api/")) {
    console.log("🌐 SW v5: API call - network only");
    e.respondWith(fetch(request));
    return;
  }

  // =====================================================
  // All other requests - Network first, cache fallback
  // =====================================================
  e.respondWith(
    fetch(request)
      .then((response) => {
        if (response && response.status === 200 && request.method === "GET" && response.type === "basic") {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, responseToCache);
          });
        }
        return response;
      })
      .catch(() => {
        return caches.match(request);
      })
  );
});

self.addEventListener("message", (e) => {
  console.log("💬 SW v5: Message received:", e.data);

  if (e.data && e.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  }

  if (e.data && e.data.type === "CLEAR_CACHE") {
    caches.delete(CACHE_NAME).then(() => {
      console.log("🗑️ SW v5: Cache cleared");
      e.ports[0].postMessage({ cleared: true });
    });
  }
});

console.log("✅ SW v5 script loaded and ready");
