

// ── Register service worker ───────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker
            .register('/service-worker.js')
            .then(reg => console.log('TekioTask SW registered:', reg.scope))
            .catch(err => console.log('SW registration failed:', err));
    });
}

// ── Online / offline banner ───────────────────────────────────
function showOfflineBanner() {
    if (document.getElementById('offline-banner')) return;
    const banner = document.createElement('div');
    banner.id = 'offline-banner';
    banner.style.cssText = `
        position:fixed; bottom:0; left:0; right:0;
        background:#dc2626; color:#fff;
        text-align:center; padding:10px;
        font-size:14px; font-weight:600; z-index:9999;
    `;
    banner.textContent = '📶 You are offline. Tasks will be saved and synced when back online.';
    document.body.appendChild(banner);
}

function hideOfflineBanner() {
    document.getElementById('offline-banner')?.remove();

    // Trigger background sync when back online
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        navigator.serviceWorker.ready.then(reg => {
            reg.sync.register('sync-progress-logs');
        });
    }
}

window.addEventListener('online',  hideOfflineBanner);
window.addEventListener('offline', showOfflineBanner);

if (!navigator.onLine) showOfflineBanner();

// ── IndexedDB offline queue ───────────────────────────────────
// Called by student routine view when network is unavailable

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

async function queueOfflineLog(action, data, csrf) {
    const db    = await openOfflineDB();
    const tx    = db.transaction('offlineLogs', 'readwrite');
    const store = tx.objectStore('offlineLogs');
    store.add({ action, data, csrf, queuedAt: new Date().toISOString() });
    console.log('Queued offline log:', action, data);
}

// Expose globally so routine view JS can call it
window.TekioOffline = { queueOfflineLog };
