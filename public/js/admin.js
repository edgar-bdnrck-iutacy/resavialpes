
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// Appelé après chargement de Lucide pour rendre les icônes
function renderIcons() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Gestion du thème -------------------------------------------------
const html = document.documentElement;
const themeBtn = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const savedTheme = localStorage.getItem('resavialpes-theme');

function applyTheme(theme) {
    html.classList.remove('theme-dark', 'theme-light');
    html.classList.add(theme);
    localStorage.setItem('resavialpes-theme', theme);

    // Icône du bouton
    themeIcon.setAttribute('data-lucide', theme === 'theme-dark' ? 'moon' : 'sun');
    renderIcons();
}

// Thème initial
if (savedTheme === 'theme-light' || savedTheme === 'theme-dark') {
    applyTheme(savedTheme);
} else {
    applyTheme('theme-dark');
}

themeBtn.addEventListener('click', () => {
    if (html.classList.contains('theme-dark')) {
        applyTheme('theme-light');
    } else {
        applyTheme('theme-dark');
    }
});

// Rendu initial des icônes quand Lucide est chargé
window.addEventListener('load', renderIcons);

document.addEventListener('DOMContentLoaded', () => {

    // ====== Données envoyées par Laravel
    const alerts = window.__ALERTS__ ?? [];
    window.__alerts = alerts; // debug
    console.log('[ALERTS] count =', alerts.length, alerts);

    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer || !Array.isArray(alerts)) return;

    // ====== Réafficher comme "première fois"
    // (si tu as un bouton reset, ça marchera aussi)
    // localStorage.removeItem('seenAlerts');

    // ====== Afficher uniquement les nouvelles alertes
    const seen = JSON.parse(localStorage.getItem('seenAlerts') || '[]');

    alerts.forEach(alert => {
        if (alert?.id && !seen.includes(alert.id)) {
            showToast(alert);
            seen.push(alert.id);
        }
    });

    localStorage.setItem('seenAlerts', JSON.stringify(seen));

    // ====== Toast avec:
    // - barre de temps
    // - pause au survol
    // - clic => url
    // - animation slide over
    function showToast(alert) {
        const duration = 5500; // un peu plus agréable
        let remaining = duration;
        let start = Date.now();
        let timer = null;
        let paused = false;

        const toast = document.createElement('div');
        toast.className = `toast toast-${alert.level || 'info'}`;
        toast.style.cursor = alert.url ? 'pointer' : 'default';

        toast.innerHTML = `
            <div class="toast-content">
                <strong>${escapeHtml(alert.title || 'Notification')}</strong><br>
                ${escapeHtml(alert.message || '')}
            </div>
            <div class="toast-progress"></div>
        `;

        const progress = toast.querySelector('.toast-progress');

        // clic => fiche avion / page filtrée
        if (alert.url) {
            toast.addEventListener('click', () => {
                window.location.href = alert.url;
            });
        }

        function renderProgress() {
            // largeur = proportion restante
            const elapsed = Date.now() - start;
            const ratio = Math.max(0, 1 - (elapsed / remaining));
            progress.style.width = (ratio * 100) + '%';

            if (elapsed >= remaining) {
                stopTimer();
                hideAndRemove();
            }
        }

        function startTimer() {
            start = Date.now();
            timer = setInterval(renderProgress, 50);
        }

        function stopTimer() {
            if (timer) clearInterval(timer);
            timer = null;
        }

        function pauseTimer() {
            if (paused) return;
            paused = true;
            const elapsed = Date.now() - start;
            remaining = Math.max(0, remaining - elapsed);
            stopTimer();
        }

        function resumeTimer() {
            if (!paused) return;
            paused = false;
            startTimer();
        }

        function hideAndRemove() {
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 260);
        }

        toast.addEventListener('mouseenter', pauseTimer);
        toast.addEventListener('mouseleave', resumeTimer);

        // injecter + animer "slide over"
        toastContainer.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('toast-show'));

        // init barre
        progress.style.width = '100%';
        startTimer();
    }

    // Sécurise l’injection HTML
    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
});

// ====== AJAX live filters (Users + Aircraft) ======================
(function () {
  const forms = document.querySelectorAll('form[data-ajax-filter]');
  if (!forms.length) return;

  const debounce = (fn, ms) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  async function fetchInto(url, target) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) return;
    const html = await res.text();
    target.innerHTML = html;

    // re-render Lucide icons if any in replaced area
    if (window.lucide) window.lucide.createIcons();
  }

  async function refresh(form) {
    const targetSel = form.getAttribute('data-target');
    const target = document.querySelector(targetSel);
    if (!target) return;

    const url = new URL(form.action || window.location.href, window.location.origin);
    const fd = new FormData(form);

    // params
    for (const [k, v] of fd.entries()) {
      const val = (v ?? '').toString().trim();
      if (val !== '') url.searchParams.set(k, val);
      else url.searchParams.delete(k);
    }

    await fetchInto(url.toString(), target);
    window.history.replaceState({}, '', url.toString());
  }

  // Intercept sort clicks inside replaced areas (th-sortable)
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a.th-sortable');
    if (!a) return;

    const form = e.target.closest('.card')?.querySelector('form[data-ajax-filter]');
    if (!form) return;

    const targetSel = form.getAttribute('data-target');
    const target = document.querySelector(targetSel);
    if (!target) return;

    // only intercept if link points to same index page
    e.preventDefault();
    fetchInto(a.href, target);
    window.history.replaceState({}, '', a.href);
  });

  for (const form of forms) {
    const debounced = debounce(() => refresh(form), 250);

    form.addEventListener('change', () => refresh(form));
    form.addEventListener('input', () => debounced());

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      refresh(form);
    });
  }
})();
