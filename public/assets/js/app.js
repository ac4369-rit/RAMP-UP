// 1) Cambio de idioma sin botón
document.querySelector('[data-locale-select]')?.addEventListener('change', (e) => e.target.form.submit());

// 2) Aviso offline
const banner = document.querySelector('[data-offline-banner]');
const syncOnline = () => { if (banner) banner.hidden = navigator.onLine; };
window.addEventListener('online', syncOnline);
window.addEventListener('offline', syncOnline);
syncOnline();

// 3) Service worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js').catch(() => {});
}

// 4) Regiones recargables con estados reales: loading / error / offline
function stateBox(kind, text, retry) {
  const box = document.createElement('div');
  box.className = `state state--${kind}`;
  box.setAttribute('role', kind === 'error' ? 'alert' : 'status');
  const p = document.createElement('p');
  p.textContent = text;
  box.append(p);
  if (retry) box.append(retry);
  return box;
}

async function loadRegion(region, url) {
  const msg = region.dataset;
  region.setAttribute('aria-busy', 'true');
  region.replaceChildren(stateBox('loading', msg.msgLoading));

  const retryBtn = () => {
    const b = document.createElement('button');
    b.type = 'button';
    b.textContent = msg.msgRetry;
    b.addEventListener('click', () => loadRegion(region, url));
    return b;
  };

  if (!navigator.onLine) {
    region.replaceChildren(stateBox('offline', msg.msgOffline, retryBtn()));
    region.removeAttribute('aria-busy');
    return;
  }
  try {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' } });
    if (!res.ok) throw new Error(String(res.status));
    const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
    const fresh = doc.querySelector('[data-async-region]');
    if (!fresh) throw new Error('no region');
    region.innerHTML = fresh.innerHTML;
    history.replaceState(null, '', url);
  } catch {
    region.replaceChildren(stateBox('error', msg.msgError, retryBtn()));
  } finally {
    region.removeAttribute('aria-busy');
  }
}

document.querySelectorAll('form[data-async]').forEach((form) => {
  form.addEventListener('submit', (e) => {
    const region = document.querySelector('[data-async-region]');
    if (!region) return;                       // sin región: envío normal
    e.preventDefault();
    const params = new URLSearchParams(new FormData(form));
    loadRegion(region, `${form.action.split('?')[0]}?${params}`);
  });
});
