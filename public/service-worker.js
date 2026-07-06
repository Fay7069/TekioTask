// PLACE AT: public/service-worker.js

const CACHE_NAME   = 'tekiotask-v1';
const OFFLINE_PAGE = '/offline.html';

const PRECACHE_URLS = [
    '/',
    '/offline.html',
    '/css/tekiotask.css',
    '/js/pwa-register.js',
];

// ── Install ───────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
              .then(cache => cache.addAll(PRECACHE_URLS))
              .then(() => self.skipWaiting())
    );
});

// ── Activate — remove old caches ─────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
              .then(keys => Promise.all(
                  keys.filter(k => k !== CACHE_NAME)
                      .map(k => caches.delete(k))
              ))
              .then(() => self.clients.claim())
    );
});

// ── Fetch ─────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and cross-origin
    if (request.method !== 'GET' || url.origin !== location.origin) return;

    // Never cache auth, logout, or task POST routes
    const skipPaths = ['/login', '/logout', '/student/task/', '/teacher/', '/parent/comment'];
    if (skipPaths.some(p => url.pathname.startsWith(p))) return;

    // CSS / JS / images — cache first
    if (['style', 'script', 'image'].includes(request.destination)) {
        event.respondWith(
            caches.match(request).then(cached =>
                cached || fetch(request).then(res => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    return res;
                })
            )
        );
        return;
    }

    // HTML pages — network first, fallback to cache, then offline page
    event.respondWith(
        fetch(request)
            .then(res => {
                if (res.ok) {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then(c => c.put(request, clone));
                }
                return res;
            })
            .catch(() =>
                caches.match(request).then(cached => cached || caches.match(OFFLINE_PAGE))
            )
    );
});

// ── Background sync — flush queued offline logs ───────────────
self.addEventListener('sync', event => {
    if (event.tag === 'sync-progress-logs') {
        event.waitUntil(syncProgressLogs());
    }
});

async function syncProgressLogs() {
    const db    = await openOfflineDB();
    const tx    = db.transaction('offlineLogs', 'readwrite');
    const store = tx.objectStore('offlineLogs');
    const logs  = await getAllFromStore(store);

    for (const log of logs) {
        try {
            const res = await fetch('/student/task/' + log.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': log.csrf,
                },
                body: JSON.stringify(log.data),
            });
            if (res.ok) await store.delete(log.id);
        } catch {
            // Still offline — leave in queue for next sync
        }
    }
}

function openOfflineDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('TekioTaskOffline', 1);
        req.onupgradeneeded = e => {
            e.target.result.createObjectStore('offlineLogs', {
                keyPath: 'id', autoIncrement: true,
            });
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}

function getAllFromStore(store) {
    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = e => resolve(e.target.result);
        req.onerror   = e => reject(e.target.error);
    });
}
