const CACHE_NAME = "bookmarks-pwa-v7";
const urlsToCache = ["/", "/share"];

self.addEventListener("install", (e) => {
  console.log("🔧 SW v6: Installing");
  e.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener("activate", (e) => {
  console.log("🚀 SW v6: Activating");
  e.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => self.clients.claim())
  );
});

self.addEventListener("fetch", (e) => {
  const { request } = e;
  const url = new URL(request.url);

  // =====================================================
  // SHARE TARGET POST HANDLER - WITH URL FIELD DETECTION
  // =====================================================
  if (request.method === "POST" && url.pathname === "/share") {
    console.log("🎯 SW v6: Share POST detected");

    e.respondWith(
      (async () => {
        try {
          const formData = await request.clone().formData();

          // Log what we received
          console.log("📦 SW v6: Raw form data:");
          for (const [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
          }

          // Extract data from form
          let sharedUrl = formData.get("url") || "";
          let sharedTitle = formData.get("title") || "";
          let sharedText = formData.get("text") || "";

          console.log("📦 SW v6: Initial extraction:", {
            url: sharedUrl,
            title: sharedTitle,
            text: sharedText
          });

          // FIX: If url is empty but text looks like a URL, use text as url
          if (!sharedUrl && sharedText) {
            // Check if text looks like a URL
            if (sharedText.startsWith("http://") || sharedText.startsWith("https://")) {
              console.log("🔧 SW v6: URL found in text field, swapping");
              sharedUrl = sharedText;
              sharedText = ""; // Clear text since it was actually the URL
            }
          }

          // FIX: If still no URL but text contains a URL, try to extract it
          if (!sharedUrl && sharedText) {
            const urlMatch = sharedText.match(/(https?:\/\/[^\s]+)/);
            if (urlMatch) {
              console.log("🔧 SW v6: URL extracted from text");
              sharedUrl = urlMatch[1];
              sharedText = sharedText.replace(urlMatch[1], "").trim();
            }
          }

          console.log("📦 SW v6: After processing:", {
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

          console.log("➡️ SW v6: Redirecting to:", redirectUrl);

          return Response.redirect(redirectUrl, 303);

        } catch (err) {
          console.error("❌ SW v6: Share handler error:", err);
          return Response.redirect(`${url.origin}/share`, 303);
        }
      })()
    );
    return;
  }

  // Handle GET /share - Network only
  if (url.pathname === "/share") {
    e.respondWith(fetch(request));
    return;
  }

  // Handle API calls - Network only
  if (url.pathname.startsWith("/api/")) {
    e.respondWith(fetch(request));
    return;
  }

  // All other requests - Network first, cache fallback
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
      .catch(() => caches.match(request))
  );
});

self.addEventListener("message", (e) => {
  if (e.data && e.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  }
});

console.log("✅ SW v6 loaded");
