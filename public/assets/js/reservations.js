/**
 * Gestion des annulations de réservation (AJAX)
 */
document.querySelectorAll('.js-cancel-form').forEach(form => {
  form.addEventListener('submit', event => {
    event.preventDefault();

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
        }
      })
      .catch(() => {
        showToast('Erreur réseau', 'error');
      });
  });
});

/**
 * Prévention du double clic (réservation / confirmation)
 */
document.querySelectorAll('.js-reserve-form').forEach(form => {
  form.addEventListener('submit', () => {
    const btn = form.querySelector('button');
    if (!btn) return;

    btn.disabled = true;
    btn.textContent = 'Traitement...';
  });
});

/**
 * Affiche un toast Bootstrap global
 */
function showToast(message, status) {
  const toastEl = document.getElementById('app-toast');
  const body = toastEl.querySelector('.toast-body');

  body.textContent = message;

  toastEl.classList.remove('text-bg-success', 'text-bg-danger');
  toastEl.classList.add(
    status === 'success' ? 'text-bg-success' : 'text-bg-danger'
  );

  new bootstrap.Toast(toastEl, { delay: 3000 }).show();
}
