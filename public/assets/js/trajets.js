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

  const form = document.querySelector('#trajets-search-form');
  if (!form) return;

  // Conteneur global (section résultats)
  const container = document.querySelector('.trajets-results');
  if (!container) return;

  // On insère les nouvelles cards avant le bloc qui contient le bouton
  const loadMoreBlock = loadBtn.parentElement;
  if (!loadMoreBlock) return;

  let offset = parseInt(loadBtn.dataset.offset || '6', 10);
  const limit = parseInt(loadBtn.dataset.limit || '6', 10);

  loadBtn.addEventListener('click', async () => {
    try {
      const formData = new FormData(form);
      formData.append('offset', String(offset));
      formData.append('limit', String(limit));

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
      formData.append('_csrf', csrfToken);

      const res = await fetch('/trajets/load-more', {
        method: 'POST',
        credentials: 'same-origin',
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
        const dt = formatDateTime(trajet.date_heure_depart);
        const places = parseInt(trajet.places_restantes ?? 0, 10);

        loadMoreBlock.insertAdjacentHTML('beforebegin', `
          <article class="trajet-card card shadow-sm mb-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong>${escapeHtml(trajet.lieu_depart)}</strong> →
                  <strong>${escapeHtml(trajet.lieu_arrivee)}</strong>
                </div>
                <div class="trajet-price fw-semibold">
                  ${Number(trajet.prix).toFixed(2).replace('.', ',')} crédits
                </div>
              </div>

              <div class="text-muted mb-3">
                ${escapeHtml(dt)}
              </div>

              <div class="text-muted small">
                Places restantes : ${Number.isFinite(places) ? places : 0}
              </div>

              <a href="/trajet?id=${parseInt(trajet.id, 10)}"
                 class="btn btn-outline-success w-100">
                Voir le détail
              </a>
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

// Format "HH:MM • DD/MM/YYYY" depuis "YYYY-MM-DD HH:MM:SS"
function formatDateTime(dateStr) {
  const d = new Date(String(dateStr).replace(' ', 'T'));
  if (Number.isNaN(d.getTime())) return '';

  const hh = String(d.getHours()).padStart(2, '0');
  const mm = String(d.getMinutes()).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  const mo = String(d.getMonth() + 1).padStart(2, '0');
  const yy = d.getFullYear();

  return `${hh}:${mm} • ${dd}/${mo}/${yy}`;
}

// helper XSS côté client
function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}