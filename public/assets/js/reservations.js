/**
 * Gestion des annulations de réservation (AJAX)
 */
document.querySelectorAll('.js-cancel-form').forEach(form => {
  form.addEventListener('submit', event => {
    event.preventDefault();

    const btn = form.querySelector('button');
    if (btn) {
      btn.disabled = true;
      btn.dataset.originalText = btn.textContent;
      btn.textContent = 'Annulation...';
    }

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(({ message, status }) => {
        showToast(message, status);

        if (status === 'success') {
          const card = form.closest('.card');
          if (card) {
            card.style.transition = 'opacity 0.3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
          }
          return;
        }

        // erreur => réactive le bouton
        if (btn) {
          btn.disabled = false;
          btn.textContent = btn.dataset.originalText || 'Annuler la réservation';
        }
      })
      .catch(() => {
        showToast('Erreur réseau', 'error');
        if (btn) {
          btn.disabled = false;
          btn.textContent = btn.dataset.originalText || 'Annuler la réservation';
        }
      });
  });
});


/**
 * Prévention du double clic (réservation / confirmation)
 * (submit normal, on ne bloque pas l’événement)
 */
document.querySelectorAll('.js-reserve-form').forEach(form => {
  if (form.dataset.bound === '1') return;
  form.dataset.bound = '1';

  form.addEventListener('submit', () => {
    const btn = form.querySelector('button');
    if (!btn) return;

    btn.disabled = true;
    btn.textContent = 'Traitement...';
  });
});

