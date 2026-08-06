// site.js — shared across every page.
// 1. Silently logs this page visit.
// 2. If an element with id="visitCounter" exists on the page, fills it with the total count.

(function () {
  // Log this visit
  const formData = new FormData();
  formData.append('page', window.location.pathname);
  fetch('/track-visit.php', { method: 'POST', body: formData }).catch(function () {});

  // Fill in a visit counter badge if the page has one
  const counterEl = document.getElementById('visitCounter');
  if (counterEl) {
    fetch('/site-visits-count.php')
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          counterEl.textContent = data.total.toLocaleString();
        }
      })
      .catch(function () {});
  }
})();
