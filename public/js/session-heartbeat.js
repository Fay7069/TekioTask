// Session / CSRF heartbeat.
// Pings /keep-alive every 5 minutes while the tab is open (and not
// hidden), touching the session's last_activity and pulling a fresh
// CSRF token. The token is written into the <meta name="csrf-token">
// tag and into every _token hidden input on the page, so any form the
// user submits after the tab has been open a long time still carries
// a valid token — preventing the 419 "Page Expired" error instead of
// just handling it after the fact.

(function () {
    const HEARTBEAT_INTERVAL_MS = 5 * 60 * 1000; // 5 minutes
    const KEEP_ALIVE_URL = '/keep-alive';

    function updateTokenEverywhere(token) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', token);

        document.querySelectorAll('input[name="_token"]').forEach((input) => {
            input.value = token;
        });
    }

    async function pingKeepAlive() {
        // Don't bother pinging if the tab isn't visible — no point
        // keeping a session alive for a tab nobody is looking at, and
        // it avoids unnecessary requests when many tabs are open.
        if (document.hidden) return;

        try {
            const response = await fetch(KEEP_ALIVE_URL, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) return; // session genuinely expired; let it 419/redirect naturally

            const data = await response.json();
            if (data && data.token) {
                updateTokenEverywhere(data.token);
            }
        } catch (err) {
            // Network hiccup — not worth surfacing to the user, next
            // interval will just try again.
            console.warn('Session heartbeat failed:', err);
        }
    }

    setInterval(pingKeepAlive, HEARTBEAT_INTERVAL_MS);

    // Also ping once when the tab becomes visible again after being
    // backgrounded, in case it was hidden past the interval.
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) pingKeepAlive();
    });
})();
