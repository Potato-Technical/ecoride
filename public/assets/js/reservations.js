/**
 * Gestion des annulations de réservation (AJAX)
 */
document.querySelectorAll('.js-cancel-form').forEach(form => {
  if (form.dataset.bound === '1') return;
  form.dataset.bound = '1';

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (form.dataset.submitting === '1') return;
    form.dataset.submitting = '1';

    let btn = form.querySelector('button');

    if (btn) {
      btn.disabled = true;
      btn.dataset.originalText = btn.dataset.originalText || btn.textContent;
      btn.textContent = 'Annulation...';
    }

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });

      const contentType = response.headers.get('content-type') || '';
      let data;

      if (contentType.includes('application/json')) {
        data = await response.json();
      } else {
        const text = await response.text();
        data = {
          status: 'error',
          message: (text && text.trim()) ? text.trim() : 'Réponse serveur invalide'
        };
      }

      if (!response.ok) {
        data.status = 'error';
        data.message = data.message || `Erreur serveur (${response.status})`;
      }

      showToast(data.message, data.status);

      if (data.status === 'success') {
        const card = form.closest('.card');
        if (card) {
          card.style.transition = 'opacity 0.3s';
          card.style.opacity = '0';

          setTimeout(() => {
            // Supprime la colonne Bootstrap qui contient la card (évite un "trou" vide)
            const col = card.closest('.col-md-6') || card.closest('.col-lg-4') || card.parentElement;
            if (col) col.remove();
            else card.remove();

            // Si plus aucune réservation, afficher le message "Aucune réservation..."
            const list = document.getElementById('reservations-list');
            if (list && list.querySelectorAll('.card').length === 0) {
              list.outerHTML = `
                <div class="alert alert-info">
                  Aucune réservation pour le moment.
                </div>
              `;
            }
          }, 300);
        }
        return;
      }

      if (btn) {
        btn.disabled = false;
        btn.textContent = btn.dataset.originalText || 'Annuler la réservation';
      }

    } catch (e) {
      showToast('Erreur réseau', 'error');
      if (btn) {
        btn.disabled = false;
        btn.textContent = btn.dataset.originalText || 'Annuler la réservation';
      }
    } finally {
      form.dataset.submitting = '0';
    }
  });
});