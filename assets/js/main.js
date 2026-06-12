/* ═══════════════════════════════════════════════════════════
   Jena Brands Ltd · main.js
   ═══════════════════════════════════════════════════════════ */

/* ── Nav scroll class ─────────────────────────────────────── */
(function () {
  const nav = document.getElementById('nav');
  if (!nav) return;
  const sync = () => nav.classList.toggle('scrolled', window.scrollY > 40);
  window.addEventListener('scroll', sync, { passive: true });
  sync();
})();

/* ── Scroll-reveal ────────────────────────────────────────── */
(function () {
  const ro = new IntersectionObserver((entries, obs) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });
  document.querySelectorAll('.reveal').forEach(el => ro.observe(el));
})();

/* ── Hero stagger animation ───────────────────────────────── */
window.addEventListener('load', function () {
  document.querySelectorAll('.hero-h1, .hero-sub, .hero-actions, .hero-scroll')
    .forEach(function (el, i) {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition =
        'opacity .9s cubic-bezier(.16,1,.3,1) ' + (i * 0.13) + 's,' +
        'transform .9s cubic-bezier(.16,1,.3,1) ' + (i * 0.13) + 's';
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          el.style.opacity = '1';
          el.style.transform = 'none';
        });
      });
    });
});

/* ── Contact form AJAX ────────────────────────────────────── */
(function () {
  var form = document.getElementById('contact-form');
  if (!form) return;

  var statusEl = document.getElementById('form-status');
  var submitBtn = form.querySelector('.btn-submit');
  var originalText = submitBtn ? submitBtn.textContent : 'Send Message';

  function setStatus(type, msg) {
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.className = 'form-status visible form-status--' + type;
    statusEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending…';
    }
    if (statusEl) {
      statusEl.className = 'form-status';
      statusEl.textContent = '';
    }

    var data = new FormData(form);

    fetch('assets/php/contact.php', {
      method: 'POST',
      body: data
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.success) {
          setStatus('success', json.message);
          form.reset();
        } else {
          setStatus('error', json.message);
        }
      })
      .catch(function () {
        setStatus('error', 'Something went wrong. Please try again or contact us directly.');
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      });
  });
})();

/* ── Active nav link highlight ────────────────────────────── */
(function () {
  var path = window.location.pathname.replace(/\/$/, '').split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a, .oc-nav a').forEach(function (a) {
    var href = a.getAttribute('href') || '';
    if (href && !href.startsWith('#') && href.split('/').pop() === path) {
      a.style.color = 'var(--purple)';
    }
  });
})();