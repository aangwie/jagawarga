const CACHE_NAME = 'jagawarga-pwa-v2';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.webmanifest',
  '/favicon.svg',
  '/icons/icon.svg'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  // Hanya intercept GET requests
  if (event.request.method !== 'GET') return;

  // Jangan cache endpoint API panic dan status
  if (event.request.url.includes('/api/panic') || event.request.url.includes('/api/pwa')) {
    return;
  }

  // Navigasi halaman HTML: Network first, fallback ke offline.html
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match('/offline.html');
      })
    );
    return;
  }

  // Aset statis: Cache first, fallback ke network
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(event.request).then((networkResponse) => {
        // Cache respons valid dari origin sendiri
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          event.request.url.startsWith(self.location.origin)
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      });
    }).catch(() => {
      // Jika fetch gagal dan ini file gambar, biarkan browser handle
    })
  );
});

// Message Event: Handler untuk menerima instruksi tampil notifikasi dari aplikasi PWA
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SHOW_PANIC_NOTIFICATION') {
    const payload = event.data.payload || {};
    const title = payload.title || '🚨 DARURAT: KENTONGAN ONLINE RW 02';
    const alertId = payload.alertId || Date.now();

    const options = Object.assign({
      body: payload.body || 'Sinyal bahaya kentongan online aktif di lingkungan RW 02!',
      icon: '/icons/icon.svg',
      badge: '/icons/icon.svg',
      vibrate: [500, 200, 500, 200, 500, 200, 1000],
      tag: 'kentongan-darurat-' + alertId,
      renotify: true,
      requireInteraction: true,
      data: {
        url: payload.url || '/#panic-button',
        alertId: alertId,
        kategori: payload.kategori || 'darurat',
      },
      actions: [
        { action: 'open', title: '🚨 Buka Lokasi & Siaga' },
        { action: 'dismiss', title: 'Tutup' }
      ]
    }, payload.options || {});

    event.waitUntil(
      self.registration.showNotification(title, options)
    );
  }
});

// Push Event: Handler untuk notifikasi Web Push
self.addEventListener('push', (event) => {
  let data = {};
  try {
    data = event.data ? event.data.json() : {};
  } catch (e) {
    data = { body: event.data ? event.data.text() : 'Sinyal bahaya kentongan online aktif!' };
  }

  const title = data.title || '🚨 DARURAT: KENTONGAN ONLINE RW 02';
  const alertId = data.alertId || Date.now();

  const options = {
    body: data.body || 'Sinyal bahaya kentongan online aktif di lingkungan RW 02!',
    icon: '/icons/icon.svg',
    badge: '/icons/icon.svg',
    vibrate: [500, 200, 500, 200, 500, 200, 1000],
    tag: 'kentongan-darurat-' + alertId,
    renotify: true,
    requireInteraction: true,
    data: {
      url: data.url || '/#panic-button',
      alertId: alertId,
      kategori: data.kategori || 'darurat'
    },
    actions: [
      { action: 'open', title: '🚨 Buka Lokasi & Siaga' },
      { action: 'dismiss', title: 'Tutup' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification Click Event: Ketika notifikasi sistem diklik/disentuh oleh pengguna
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'dismiss') {
    return;
  }

  const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Jika ada jendela atau tab PWA yang terbuka, fokuskan jendela tersebut
      for (const client of clientList) {
        if ('focus' in client) {
          client.focus();
          client.postMessage({
            type: 'PANIC_NOTIFICATION_CLICKED',
            data: event.notification.data
          });
          return;
        }
      }
      // Jika belum ada jendela terbuka, buka jendela baru aplikasi PWA
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
