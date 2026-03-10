const CACHE_NAME = "bookmarks-pwa-v8";

// Precache the shell pages AND their core dependencies
const PRECACHE_URLS = [
  "/",
  "/share",
  "/assets/css/main.css",
  "/assets/js/share-min.js",
  "/assets/android-chrome-192x192.png"
];

// Only cache responses for these origins/paths
const CACHE_ALLOWLIST = new Set(["/", "/share"]);

// =====================================================
// INSTALL — precache shell assets
// =====================================================

self.addEventListener("install", (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

// =====================================================
// ACTIVATE — purge old caches
// =====================================================

self.addEventListener("activate", (e) => {
  e.waitUntil(
    caches.keys()
      .then((cacheNames) => Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      ))
      .then(() => self.clients.claim())
  );
});

// =====================================================
// HELPERS
// =====================================================

// Single-pass URL extraction from Web Share data
function extractSharedUrl(formData) {
  const url   = formData.get("url")  || "";
  const text  = formData.get("text") || "";
  const title = formData.get("title") || "";

  if (url) return { url, title, text };

  // Text field may carry the URL (some share targets behave this way)
  if (text.startsWith("http://") || text.startsWith("https://")) {
    return { url: text, title, text: "" };
  }

  const match = text.match(/(https?:\/\/[^\s]+)/);
  if (match) {
    return { url: match[1], title, text: text.replace(match[1], "").trim() };
  }

  return { url: "", title, text };
}

// Stale-while-revalidate: serve cache immediately, update in background
function staleWhileRevalidate(request) {
  const networkFetch = fetch(request).then((response) => {
    if (response.status === 200 && response.type === "basic") {
      const clone = response.clone();
      caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
    }
    return response;
  });

  return caches.match(request).then((cached) => cached || networkFetch);
}

// =====================================================
// FETCH
// =====================================================

self.addEventListener("fetch", (e) => {
  const { request } = e;

  // Ignore non-GET methods except the share POST handler below
  if (request.method !== "GET" && request.method !== "POST") return;

  // Only handle same-origin requests
  if (!request.url.startsWith(self.location.origin)) return;

  // Parse URL once, after origin check
  const url = new URL(request.url);

  // --- Share target POST ---
  if (request.method === "POST" && url.pathname === "/share") {
    e.respondWith(
      request.clone().formData()
        .then((formData) => {
          const { url: sharedUrl, title, text } = extractSharedUrl(formData);

          const params = new URLSearchParams();
          if (sharedUrl) params.set("url", sharedUrl);
          if (title)     params.set("title", title);
          if (text)      params.set("text", text);

          return Response.redirect(
            `${url.origin}/share?${params.toString()}`,
            303
          );
        })
        .catch(() => Response.redirect(`${url.origin}/share`, 303))
    );
    return;
  }

  // --- API calls — network only, no caching ---
  if (url.pathname.startsWith("/api/")) {
    e.respondWith(fetch(request));
    return;
  }

  // --- /share GET — network only (always needs fresh auth state) ---
  if (url.pathname === "/share") {
    e.respondWith(fetch(request).catch(() => caches.match(request)));
    return;
  }

  // --- Allowlisted paths — stale-while-revalidate ---
  if (CACHE_ALLOWLIST.has(url.pathname)) {
    e.respondWith(staleWhileRevalidate(request));
    return;
  }

  // --- Everything else — network only ---
  e.respondWith(fetch(request));
});

// =====================================================
// MESSAGE
// =====================================================

self.addEventListener("message", (e) => {
  if (e.data?.type === "SKIP_WAITING") self.skipWaiting();
});