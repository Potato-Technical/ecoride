/**
 * TRAJETS
 * - Toggle filtres (mobile)
 * - Load more (AJAX) avec CSRF
 */

document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.querySelector('[data-toggle-filters]');
  const filters = document.querySelector('.trajets-filters');

  if (toggleBtn && filters) {
    toggleBtn.addEventListener('click', () => {
      filters.classList.toggle('is-open');
    });
  }

  const loadBtn = document.querySelector('.load-more-btn');
  if (!loadBtn) return;

  const form = document.querySelector('form');
  const container = document.querySelector('.trajets-results');

  // Offset = nombre déjà affiché côté serveur (injecté via data-*)
  let offset = parseInt(loadBtn.dataset.offset || '6', 10);
  const limit = parseInt(loadBtn.dataset.limit || '6', 10);

  loadBtn.addEventListener('click', async () => {
    try {
      const formData = new FormData(form);

      formData.append('offset', String(offset));
      formData.append('limit', String(limit));

      const csrfEl = document.getElementById('csrf-token');
      const csrfToken = csrfEl ? csrfEl.value : '';
      formData.append('csrf_token', csrfToken);

      const res = await fetch('/trajets/load-more', {
        method: 'POST',
        body: formData
      });

      if (!res.ok) {
        console.error('Load more failed', res.status);
        return;
      }

      const trajets = await res.json();

      if (!Array.isArray(trajets) || trajets.length === 0) {
        loadBtn.disabled = true;
        return;
      }

      trajets.forEach(trajet => {
        container.insertAdjacentHTML('beforeend', `
          <article class="trajet-card card shadow-sm mb-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div><strong>${escapeHtml(trajet.lieu_depart)}</strong> → <strong>${escapeHtml(trajet.lieu_arrivee)}</strong></div>
                <div class="trajet-price fw-semibold">${Number(trajet.prix).toFixed(2).replace('.', ',')} crédits</div>
              </div>
              <a href="/trajet?id=${parseInt(trajet.id, 10)}" class="btn btn-outline-success w-100">Voir le détail</a>
            </div>
          </article>
        `);
      });

      offset += trajets.length;
      loadBtn.dataset.offset = String(offset);

    } catch (e) {
      console.error(e);
    }
  });
});

// petit helper XSS côté client (complément, pas une sécurité serveur)
function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
