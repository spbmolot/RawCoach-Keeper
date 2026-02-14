// RawPlan Service Worker v1.0
const CACHE_NAME = 'rawplan-v1';
const OFFLINE_URL = '/offline.html';

// Статические ресурсы для предкеширования
const PRECACHE_ASSETS = [
  OFFLINE_URL,
  '/favicon.svg',
  '/favicon-192.png',
  '/favicon-512.png',
  '/apple-touch-icon.png',
];

// Install — кешируем offline-страницу и статику
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate — удаляем старые кеши
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

// Fetch — стратегии кеширования
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Только GET-запросы
  if (request.method !== 'GET') return;

  // Пропускаем запросы к API, Livewire, admin, webhooks
  if (
    url.pathname.startsWith('/api/') ||
    url.pathname.startsWith('/livewire/') ||
    url.pathname.startsWith('/admin/') ||
    url.pathname.startsWith('/webhook/') ||
    url.pathname.startsWith('/sanctum/') ||
    url.pathname.includes('livewire')
  ) {
    return;
  }

  // Статика (CSS, JS, шрифты, изображения) — Cache First
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirst(request));
    return;
  }

  // HTML-страницы — Network First с offline fallback
  if (request.headers.get('Accept')?.includes('text/html')) {
    event.respondWith(networkFirstWithOffline(request));
    return;
  }

  // Остальное — Network First
  event.respondWith(networkFirst(request));
});

function isStaticAsset(pathname) {
  return /\.(css|js|woff2?|ttf|eot|svg|png|jpe?g|gif|webp|ico|avif)(\?.*)?$/i.test(pathname)
    || pathname.startsWith('/build/')
    || pathname.startsWith('/vendor/');
}

// Cache First — сначала кеш, потом сеть
async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('', { status: 408, statusText: 'Offline' });
  }
}

// Network First — сначала сеть, потом кеш
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached || new Response('', { status: 408, statusText: 'Offline' });
  }
}

// Network First + offline fallback для HTML
async function networkFirstWithOffline(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    if (cached) return cached;

    // Показываем offline-страницу
    return caches.match(OFFLINE_URL);
  }
}
