function showToast(message, status) {
  const toastEl = document.getElementById('app-toast');
  if (!toastEl) return;

  const body = toastEl.querySelector('.toast-body');
  if (body) body.textContent = message ?? '';

  toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-info');
  toastEl.classList.add(
    status === 'success' ? 'text-bg-success' :
    status === 'info' ? 'text-bg-info' :
    'text-bg-danger'
  );

  new bootstrap.Toast(toastEl, { delay: 3000 }).show();
}
