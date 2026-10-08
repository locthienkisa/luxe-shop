document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (form.dataset.validated === '1') return;
  if (!form.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
  }
  form.classList.add('was-validated');
});
