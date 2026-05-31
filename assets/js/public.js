/* =================================================================
   FM Public Site — Interactions
   -----------------------------------------------------------------
   Language switching: PHP handles locale via URL (/ru/ vs /en/).
   Handles: header scroll, mobile menu, reveal, counters, accordions,
   filters, scrollspy, contact form AJAX → real PHP backend.
   ================================================================= */
(function () {
  "use strict";
  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  /* ---------- header scroll ---------- */
  const header = $(".header");
  const onScroll = () => header && header.classList.toggle("is-scrolled", window.scrollY > 12);
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  /* ---------- mobile menu ---------- */
  const burger = $(".burger");
  const menu = $(".mobile-menu");
  function setMenu(open) {
    document.body.classList.toggle("is-open", open);
    if (menu) menu.classList.toggle("is-open", open);
    if (burger) burger.setAttribute("aria-expanded", String(open));
    document.body.style.overflow = open ? "hidden" : "";
  }
  if (burger) burger.addEventListener("click", () => setMenu(!document.body.classList.contains("is-open")));
  if (menu) $$("a", menu).forEach(a => a.addEventListener("click", () => setMenu(false)));

  /* ---------- reveal on scroll ---------- */
  const reveals = $$(".reveal");
  if ("IntersectionObserver" in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add("is-visible"); io.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
    reveals.forEach(el => io.observe(el));
  } else {
    reveals.forEach(el => el.classList.add("is-visible"));
  }

  /* ---------- animated counters ---------- */
  function animateCount(el) {
    const target = parseFloat(el.getAttribute("data-count"));
    if (isNaN(target)) return;
    const dur = 1400;
    const start = performance.now();
    function tick(now) {
      const p = Math.min((now - start) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased).toString();
      if (p < 1) requestAnimationFrame(tick);
      else el.textContent = target.toString();
    }
    requestAnimationFrame(tick);
  }
  const counters = $$("[data-count]");
  if ("IntersectionObserver" in window) {
    const co = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { animateCount(e.target); co.unobserve(e.target); } });
    }, { threshold: 0.6 });
    counters.forEach(el => co.observe(el));
  } else counters.forEach(animateCount);

  /* ---------- skill group accordion ---------- */
  $$(".skillgroup__head").forEach(head => {
    head.addEventListener("click", () => {
      head.closest(".skillgroup").classList.toggle("is-open");
    });
  });

  /* ---------- FAQ accordion ---------- */
  $$(".faq__q").forEach(q => {
    q.addEventListener("click", () => {
      const item = q.closest(".faq__item");
      const open = item.classList.contains("is-open");
      $$(".faq__item").forEach(i => i.classList.remove("is-open"));
      if (!open) item.classList.add("is-open");
    });
  });

  /* ---------- portfolio filters ---------- */
  const filterBtns = $$(".filter");
  const projCards = $$(".proj");
  filterBtns.forEach(f => {
    f.addEventListener("click", () => {
      filterBtns.forEach(x => x.classList.remove("is-active"));
      f.classList.add("is-active");
      const cat = f.getAttribute("data-filter");
      projCards.forEach(p => {
        const cats = (p.getAttribute("data-cats") || "").split(" ");
        p.classList.toggle("is-hidden", cat !== "all" && !cats.includes(cat));
      });
    });
  });

  /* ---------- scrollspy: active nav link ---------- */
  const navLinks = $$(".nav a[href^='#']");
  const spyTargets = navLinks
    .map(a => { const el = $(a.getAttribute("href")); return el ? { a, el } : null; })
    .filter(Boolean);
  if (spyTargets.length && "IntersectionObserver" in window) {
    const spy = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          navLinks.forEach(a => a.classList.remove("is-active"));
          const match = spyTargets.find(t => t.el === e.target);
          if (match) match.a.classList.add("is-active");
        }
      });
    }, { rootMargin: "-45% 0px -50% 0px" });
    spyTargets.forEach(t => spy.observe(t.el));
  }

  /* ---------- pricing CTA → auto-fill contact budget ---------- */
  var budgetRanges = ['$500 - $1 500', '$1 500 - $5 000', '$5 000+'];
  function pickBudget(priceStr) {
    var num = parseFloat(priceStr.replace(/[^0-9.]/g, ''));
    if (isNaN(num) || num <= 0) return '';
    if (num <= 1500) return '$500 - $1 500';
    if (num <= 5000) return '$1 500 - $5 000';
    return '$5 000+';
  }
  $$('.price-plan-cta').forEach(function (cta) {
    cta.addEventListener('click', function (e) {
      e.preventDefault();
      var planName  = cta.getAttribute('data-plan')  || '';
      var planPrice = cta.getAttribute('data-price') || '';

      /* pre-fill budget select */
      var budgetSel = $('select[name="budget"]');
      if (budgetSel) {
        var target = pickBudget(planPrice);
        if (target) budgetSel.value = target;
      }

      /* show plan badge */
      var badge    = $('#plan-badge');
      var badgeVal = badge && badge.querySelector('.plan-badge__text');
      if (badge && badgeVal && planName) {
        var label = (badge.getAttribute('data-label') || 'Plan') + ': ';
        badgeVal.textContent = label + planName + (planPrice ? ' — ' + planPrice : '');
        badge.hidden = false;
      }

      /* smooth scroll to contact */
      var contact = $('#contact');
      if (contact) contact.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  /* plan badge dismiss */
  var planBadge = $('#plan-badge');
  if (planBadge) {
    var closeBtn = planBadge.querySelector('.plan-badge__close');
    if (closeBtn) closeBtn.addEventListener('click', function () { planBadge.hidden = true; });
  }

  /* ---------- language switcher: preserve current section ---------- */
  $$('[data-lang-btn]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();

      /* Find the last section whose top edge is at or above 55% of viewport height.
         That is the section the user is currently reading. */
      var sections = $$('section[id]');
      var currentId = '';
      var threshold = window.innerHeight * 0.55;
      sections.forEach(function (s) {
        if (s.getBoundingClientRect().top <= threshold) {
          currentId = s.id;
        }
      });

      var dest = btn.getAttribute('href') + (currentId ? '#' + currentId : '');
      window.location.href = dest;
    });
  });

  /* ---------- contact form — AJAX → real PHP backend ---------- */
  const form = $("#contact-form");
  if (form) {
    const submitBtn = $('[type="submit"]', form);
    const statusEl = $("#form-status");
    const locale = (window.CVF && window.CVF.locale) || document.documentElement.lang || "ru";
    const copy = locale === "en" ? {
      name: "Enter your name (at least 2 characters).",
      email: "Enter a valid email address.",
      message: "Message is too short (at least 10 characters).",
      forbidden: "Access denied. Refresh the page.",
      limited: "Too many attempts. Try again later.",
      sent: "Message sent!",
      sentButton: "Sent ✓",
      generic: "Something went wrong. Try again.",
      network: "No connection. Your data is kept, try again."
    } : {
      name: "Введите имя (минимум 2 символа).",
      email: "Укажите корректный email.",
      message: "Сообщение слишком короткое (минимум 10 символов).",
      forbidden: "Доступ запрещён. Обновите страницу.",
      limited: "Слишком много попыток. Повторите позже.",
      sent: "Сообщение отправлено!",
      sentButton: "Отправлено ✓",
      generic: "Произошла ошибка. Попробуйте ещё раз.",
      network: "Нет соединения. Данные сохранены, попробуйте снова."
    };

    function showStatus(msg, isError) {
      if (!statusEl) return;
      statusEl.textContent = msg;
      statusEl.className = "form-status " + (isError ? "form-status--error" : "form-status--ok");
      statusEl.style.display = "block";
    }

    function setBtnState(loading, successText) {
      if (!submitBtn) return;
      submitBtn.disabled = loading;
      if (successText) {
        submitBtn.textContent = successText;
        submitBtn.style.background = "#2f9e54";
      } else if (loading) {
        submitBtn.dataset.origText = submitBtn.textContent;
        submitBtn.textContent = "…";
      } else {
        if (submitBtn.dataset.origText) submitBtn.textContent = submitBtn.dataset.origText;
        submitBtn.style.background = "";
      }
    }

    /* Client-side validation */
    function validate(fd) {
      const errors = [];
      const name = (fd.get("name") || "").trim();
      const email = (fd.get("email") || "").trim();
      const msg = (fd.get("message") || "").trim();
      if (name.length < 2) errors.push(copy.name);
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push(copy.email);
      if (msg.length < 10) errors.push(copy.message);
      return errors;
    }

    /* Restore form data after network error so user doesn't lose input */
    function restoreFormData(saved) {
      Object.entries(saved).forEach(([name, value]) => {
        const el = form.elements[name];
        if (el && el.type !== "hidden") {
          try { el.value = value; } catch (_) {}
        }
      });
    }

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (statusEl) statusEl.style.display = "none";

      const fd = new FormData(form);
      const clientErrors = validate(fd);
      if (clientErrors.length) {
        showStatus(clientErrors.join(" "), true);
        return;
      }

      /* Save field values before clearing them on success */
      const savedData = {};
      for (const [k, v] of fd.entries()) savedData[k] = v;

      setBtnState(true);

      try {
        const action = form.getAttribute("action") || "/contact/submit";
        const res = await fetch(action, {
          method: "POST",
          body: fd,
          headers: { "X-Requested-With": "XMLHttpRequest" },
          credentials: "same-origin"
        });

        if (res.status === 401 || res.status === 403) {
          showStatus(copy.forbidden, true);
          setBtnState(false);
          return;
        }
        if (res.status === 429) {
          showStatus(copy.limited, true);
          setBtnState(false);
          return;
        }

        let json = null;
        try { json = await res.json(); } catch (_) {}

        if (res.ok && json && json.ok) {
          showStatus(json.message || copy.sent, false);
          setBtnState(false, copy.sentButton);
          setTimeout(() => {
            form.reset();
            setBtnState(false);
          }, 2400);
        } else if (json && json.errors) {
          showStatus(Object.values(json.errors).join(" "), true);
          setBtnState(false);
        } else {
          showStatus(copy.generic, true);
          restoreFormData(savedData);
          setBtnState(false);
        }
      } catch (_) {
        /* Network failure — keep user data */
        showStatus(copy.network, true);
        restoreFormData(savedData);
        setBtnState(false);
      }
    });
  }
})();
