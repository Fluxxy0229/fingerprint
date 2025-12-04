const modal = document.getElementById("id01");
function openModal() {
  modal.style.display = "block";
}
function closeModal() {
  modal.style.display = "none";
}
window.onclick = function (e) {
  if (e.target === modal) {
    modal.style.display = "none";
  }
};

(function showLoginErrorFromQuery() {
  try {
    const params = new URLSearchParams(window.location.search);
    const err = params.get("error");
    if (err) {
      const el = document.getElementById("login-error");
      if (el) {
        el.textContent = decodeURIComponent(err);
        el.style.display = "block";
      }
      openModal();
      if (window.history && window.history.replaceState) {
        const clean = window.location.pathname + window.location.hash;
        window.history.replaceState({}, document.title, clean);
      }
    }
  } catch (e) {}
})();
