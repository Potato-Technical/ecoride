// Sélectionne tous les formulaires d’annulation
document.querySelectorAll('.js-cancel-form').forEach(form => {

  // On écoute l’événement "submit" du formulaire
  form.addEventListener('submit', event => {

    // Empêche le rechargement de page (comportement par défaut)
    event.preventDefault();

    // Récupère toutes les données du formulaire (POST)
    const formData = new FormData(form);

    // Envoi de la requête HTTP vers le contrôleur PHP
    fetch(form.action, {
      method: 'POST',
      body: formData
    })

    // Transforme la réponse HTTP en JSON
    .then(response => response.json())

    // Exploite la réponse JSON renvoyée par le contrôleur
    .then(({ message, status }) => {

      // Affiche le toast utilisateur (succès / erreur)
      showToast(message, status);

      // Si l’annulation est un succès
      if (status === 'success') {

        // OPTION B (propre) :
        // suppression visuelle de la réservation sans rechargement de page
        const li = form.closest('li');

        if (li) {
          // Animation douce (fade-out)
          li.style.transition = 'opacity 0.3s';
          li.style.opacity = '0';

          // Suppression réelle du DOM après l’animation
          setTimeout(() => {
            li.remove();
          }, 300);
        }
      }
    })

    // Erreur réseau / serveur (timeout, 500, etc.)
    .catch(() => {
      showToast('Erreur réseau', 'error');
    });
  });
});

/**
 * Affiche un toast Bootstrap flottant (bas-droite)
 *
 * @param {string} message  Message à afficher
 * @param {string} status   success | error
 */
function showToast(message, status) {
  const toastEl = document.getElementById('app-toast');
  const body = toastEl.querySelector('.toast-body');

  // Injecte le message dans le toast
  body.textContent = message;

  // Reset des classes de couleur
  toastEl.classList.remove('text-bg-success', 'text-bg-danger');

  // Applique la couleur selon le statut
  toastEl.classList.add(
    status === 'success' ? 'text-bg-success' : 'text-bg-danger'
  );

  // Initialise et affiche le toast Bootstrap
  new bootstrap.Toast(toastEl, {
    delay: 3000
  }).show();
}
