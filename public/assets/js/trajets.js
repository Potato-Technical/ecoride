document.addEventListener('DOMContentLoaded', () => {
  initLoadMore();
  initFilterAutoSubmit();
});

function initLoadMore() {
  const loadBtn = document.querySelector('.load-more-btn');
  const form = document.querySelector('#trajets-search-form');
  const loadMoreWrapper = document.querySelector('#load-more-wrapper');
  const trajetsList = document.querySelector('.trajets-list');

  if (!loadBtn || !form || !loadMoreWrapper || !trajetsList) {
    return;
  }

  let offset = parseInt(loadBtn.dataset.offset || '6', 10);
  const limit = parseInt(loadBtn.dataset.limit || '6', 10);

  loadBtn.addEventListener('click', async () => {
    loadBtn.disabled = true;

    try {
      const formData = new FormData(form);
      formData.append('offset', String(offset));
      formData.append('limit', String(limit));

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
      formData.append('_csrf', csrfToken);

      const response = await fetch('/trajets/load-more', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      });

      if (!response.ok) {
        console.error('Load more failed', response.status);
        loadBtn.disabled = false;
        return;
      }

      const trajets = await response.json();

      if (!Array.isArray(trajets) || trajets.length === 0) {
        loadMoreWrapper.remove();
        return;
      }

      trajets.forEach((trajet) => {
        trajetsList.insertAdjacentHTML('beforeend', renderTrajetCard(trajet));
      });

      offset += trajets.length;
      loadBtn.dataset.offset = String(offset);

      if (trajets.length < limit) {
        loadMoreWrapper.remove();
        return;
      }

      loadBtn.disabled = false;
    } catch (error) {
      console.error(error);
      loadBtn.disabled = false;
    }
  });
}

function initFilterAutoSubmit() {
  const form = document.querySelector('#trajets-search-form');
  const filters = document.querySelectorAll('[data-trajets-filter]');

  if (!form || !filters.length) {
    return;
  }

  filters.forEach((field) => {
    const eventName = field.type === 'radio' || field.type === 'checkbox' || field.tagName === 'SELECT'
      ? 'change'
      : 'change';

    field.addEventListener(eventName, () => {
      form.requestSubmit();
    });
  });
}

function renderTrajetCard(trajet) {
  const depart = parseSqlDate(trajet.date_heure_depart);

  const dureeMinutes = parseInt(trajet.duree_estimee_minutes ?? 0, 10);
  const arrivee = depart && Number.isFinite(dureeMinutes) && dureeMinutes > 0
    ? new Date(depart.getTime() + dureeMinutes * 60000)
    : null;

  const departTime = depart ? formatTime(depart) : '';
  const departDate = depart ? formatDate(depart) : '';
  const arriveeTime = arrivee ? formatTime(arrivee) : '';
  const duration = formatDuration(dureeMinutes);

  const places = parseInt(trajet.places_restantes ?? 0, 10);
  const note = Number(trajet.note_moyenne ?? 0).toFixed(1).replace('.', ',');
  const eco = String(trajet.energie || '') === 'electrique';
  const prix = Number(trajet.prix ?? 0).toFixed(2).replace('.', ',');
  const pseudo = String(trajet.pseudo || '');
  const initial = pseudo ? pseudo.charAt(0).toUpperCase() : '?';
  const arriveeVille = String(trajet.lieu_arrivee || '');
  const departVille = String(trajet.lieu_depart || '');

  return `
    <article class="trajet-card">
      <a class="trajet-card-link" href="/trajets/${parseInt(trajet.id, 10)}">
        <div class="trajet-card-top">
          <div class="trajet-card-times">
            <div class="trajet-card-route">
              <span class="trajet-time">${escapeHtml(departTime)}</span>
              <span class="trajet-city">${escapeHtml(departVille)}</span>
            </div>

            <div class="trajet-card-duration">${escapeHtml(duration)}</div>

            <div class="trajet-card-route">
              ${arriveeTime ? `<span class="trajet-time">${escapeHtml(arriveeTime)}</span>` : ''}
              <span class="trajet-city">${escapeHtml(arriveeVille)}</span>
            </div>
          </div>

          <div class="trajet-card-price">${escapeHtml(prix)} €</div>
        </div>

        <div class="trajet-card-separator"></div>

        <div class="trajet-card-bottom">
          <div class="trajet-driver-avatar" aria-hidden="true">${escapeHtml(initial)}</div>

          <div class="trajet-driver-meta">
            <div class="trajet-driver-line">
              <span class="trajet-driver-name">${escapeHtml(pseudo)}</span>
              ${eco ? '<span class="trajet-eco-badge" title="Voyage écologique">🍃</span>' : ''}
              <span class="trajet-rating">★ ${escapeHtml(note)}</span>
            </div>

            <div class="trajet-driver-extra">
              <span>${escapeHtml(departDate)}</span>
              <span>•</span>
              <span>${Number.isFinite(places) ? places : 0} place(s)</span>
              <span>•</span>
              <span>${eco ? 'Électrique' : 'Non électrique'}</span>
            </div>
          </div>
        </div>
      </a>
    </article>
  `;
}

function parseSqlDate(dateStr) {
  const d = new Date(String(dateStr).replace(' ', 'T'));
  return Number.isNaN(d.getTime()) ? null : d;
}

function formatTime(dateObj) {
  const hh = String(dateObj.getHours()).padStart(2, '0');
  const mm = String(dateObj.getMinutes()).padStart(2, '0');
  return `${hh}:${mm}`;
}

function formatDate(dateObj) {
  const dd = String(dateObj.getDate()).padStart(2, '0');
  const mo = String(dateObj.getMonth() + 1).padStart(2, '0');
  const yy = dateObj.getFullYear();
  return `${dd}/${mo}/${yy}`;
}

function formatDuration(minutesTotal) {
  const minutes = parseInt(minutesTotal ?? 0, 10);

  if (!Number.isFinite(minutes) || minutes <= 0) {
    return '';
  }

  const hours = Math.floor(minutes / 60);
  const minutesReste = minutes % 60;

  return `${hours}h${String(minutesReste).padStart(2, '0')}`;
}

function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}