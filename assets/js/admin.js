document.addEventListener("DOMContentLoaded", () => {
  "use strict";

  /* =====================================================================
     Burger menu / sidebar toggle (mobile)
  ===================================================================== */
  const side  = document.getElementById("side");
  const scrim = document.getElementById("scrim");
  const burger = document.getElementById("burger");

  function openSide()  { side?.classList.add("open");  scrim?.classList.add("show"); }
  function closeSide() { side?.classList.remove("open"); scrim?.classList.remove("show"); }

  burger?.addEventListener("click", openSide);
  scrim?.addEventListener("click", closeSide);

  const normalize = (value) => String(value || "").trim().toLowerCase();
  const searchInput = document.querySelector("[data-admin-search]");
  const searchClear = document.querySelector("[data-admin-search-clear]");
  const navLinks = Array.from(document.querySelectorAll("[data-admin-nav] a"));
  const content = document.querySelector("[data-admin-content]");

  if (searchInput && content) {
    const emptyState = document.createElement("div");
    emptyState.className = "empty-state admin-search-empty";
    emptyState.innerHTML = "<strong>No matches found.</strong><p>Try another title, status, email, slug, or section name.</p>";
    content.appendChild(emptyState);

    const searchableSelector = [
      ".admin-table tbody tr",
      ".mini-list-item",
      ".media-card",
      ".detail-grid > div",
      ".media-summary-card",
      ".upload-status-card",
    ].join(",");

    const applySearch = () => {
      const query = normalize(searchInput.value);
      let visibleCount = 0;
      let totalCount = 0;

      navLinks.forEach((link) => {
        const visible = query === "" || normalize(link.textContent).includes(query);
        link.classList.toggle("admin-hidden-by-search", !visible);
      });

      Array.from(content.querySelectorAll(searchableSelector)).forEach((node) => {
        if (node.classList.contains("admin-search-empty")) return;
        totalCount += 1;
        const visible = query === "" || normalize(node.textContent).includes(query);
        node.classList.toggle("admin-hidden-by-search", !visible);
        if (visible) visibleCount += 1;
      });

      emptyState.classList.toggle("is-visible", query !== "" && totalCount > 0 && visibleCount === 0);
      if (searchClear) searchClear.hidden = query === "";
    };

    searchInput.addEventListener("input", applySearch);
    searchInput.closest("form")?.addEventListener("submit", (event) => event.preventDefault());
    searchClear?.addEventListener("click", () => {
      searchInput.value = "";
      searchInput.focus();
      applySearch();
    });
  }

  document.querySelectorAll(".toast").forEach((toast, index) => {
    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(-8px)";
      setTimeout(() => toast.remove(), 250);
    }, 2600 + index * 250);
  });

  document.querySelectorAll("form[action*='/delete/'], form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      const message = form.dataset.confirm || "Delete this item?";
      if (!window.confirm(message)) event.preventDefault();
    });
  });

  /* =====================================================================
     Fast save for text-only settings/content forms
  ===================================================================== */
  const toastStack = document.getElementById("toast-stack");
  function pushToast(message, type) {
    if (!toastStack) return;
    const toast = document.createElement("div");
    toast.className = "toast toast--" + (type || "success");
    toast.textContent = message;
    toastStack.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(-8px)";
      setTimeout(() => toast.remove(), 250);
    }, 2800);
  }

  document.querySelectorAll("form[data-ajax-save]").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const submitters = Array.from(form.querySelectorAll("button[type='submit'], input[type='submit']"));
      submitters.forEach((button) => {
        button.disabled = true;
        button.dataset.originalText = button.textContent || button.value || "";
        if (button.tagName === "INPUT") button.value = "Saving...";
        else button.textContent = "Saving...";
      });

      try {
        const response = await fetch(form.action, {
          method: form.method || "POST",
          body: new FormData(form),
          credentials: "same-origin",
          headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const json = await response.json().catch(() => null);

        if (response.ok && json && json.ok) {
          pushToast(json.message || "Saved.", "success");
          form.dataset.dirty = "0";
          return;
        }

        pushToast((json && json.message) || "Save failed. Refresh and try again.", "error");
      } catch (_) {
        pushToast("Network error. Your edits are still on screen.", "error");
      } finally {
        submitters.forEach((button) => {
          button.disabled = false;
          if (button.tagName === "INPUT") button.value = button.dataset.originalText || "Save";
          else button.textContent = button.dataset.originalText || "Save";
        });
      }
    });
  });

  /* =====================================================================
     Traffic chart
  ===================================================================== */
  const trafficCanvas = document.getElementById("dailyTrafficChart");
  const trafficWrap   = document.getElementById("traffic-chart-wrap");
  const trafficEmpty  = document.getElementById("traffic-empty");
  const trafficLegend = document.getElementById("traffic-legend");
  let trafficChart    = null;
  let currentDays     = 30;

  function buildTrafficChart(series) {
    if (trafficChart) {
      trafficChart.destroy();
      trafficChart = null;
    }

    const hasData = Array.isArray(series) && series.length > 0;

    if (trafficWrap)   trafficWrap.style.display   = hasData ? "" : "none";
    if (trafficLegend) trafficLegend.style.display  = hasData ? "" : "none";
    if (trafficEmpty)  trafficEmpty.style.display   = hasData ? "none" : "block";

    if (!hasData || !trafficCanvas || !window.Chart) return;

    const labels        = series.map((r) => r.event_date || r.date || "");
    const pageViews     = series.map((r) => Number(r.page_views  || r.views  || 0));
    const uniqueVisitors= series.map((r) => Number(r.unique_visitors || r.unique || 0));

    Chart.defaults.font.family = "'Hanken Grotesk', sans-serif";
    Chart.defaults.color       = "#8C8175";

    trafficChart = new Chart(trafficCanvas, {
      type: "line",
      data: {
        labels,
        datasets: [
          {
            label: "Page views",
            data: pageViews,
            borderColor: "#E4581F",
            backgroundColor: "rgba(228,88,31,.16)",
            tension: 0.35,
            fill: true,
            pointRadius: 0,
            pointHoverRadius: 5,
          },
          {
            label: "Unique visitors",
            data: uniqueVisitors,
            borderColor: "#16120E",
            borderDash: [5, 4],
            tension: 0.35,
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 5,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
          y: { border: { display: false }, grid: { color: "#F0EBE2" }, ticks: { maxTicksLimit: 5 } },
        },
      },
    });
  }

  // Initial render
  buildTrafficChart(window.CVF_ADMIN?.charts?.dailySeries || []);

  // Range buttons
  document.querySelectorAll("#traffic-range [data-days]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      document.querySelectorAll("#traffic-range [data-days]").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentDays = Number(btn.dataset.days);

      // Update export link days param
      const exportBtn = document.querySelector('a[href*="export-csv"]');
      if (exportBtn) {
        exportBtn.href = exportBtn.href.replace(/days=\d+/, `days=${currentDays}`);
      }

      try {
        const base = window.CVF?.baseUrl ?? "";
        const resp = await fetch(`${base}/api/admin/overview?range=${currentDays}`);
        const data = await resp.json();
        if (data.ok) buildTrafficChart(data.daily);
      } catch (_) {}
    });
  });

  /* =====================================================================
     Auto-refresh metrics every 5 minutes (dashboard only)
  ===================================================================== */
  const metricStrip = document.querySelector(".metric-strip");
  if (metricStrip) {
    const refreshMetrics = async () => {
      try {
        const base = window.CVF?.baseUrl ?? "";
        const resp = await fetch(`${base}/api/admin/overview?range=${currentDays}`);
        const data = await resp.json();
        if (!data.ok) return;

        // Update page_views and unique_visitors panels
        const updates = {
          page_views:       data.summary?.page_views       ?? null,
          unique_visitors:  data.summary?.unique_visitors  ?? null,
        };

        Object.entries(updates).forEach(([key, val]) => {
          if (val === null) return;
          const panel = metricStrip.querySelector(`[data-metric="${key}"] strong`);
          if (panel && panel.textContent !== String(val)) {
            panel.textContent = val;
            panel.closest(".metric-panel")?.classList.add("metric-panel--updated");
            setTimeout(() => panel.closest(".metric-panel")?.classList.remove("metric-panel--updated"), 1200);
          }
        });

        // Also refresh the chart silently
        if (data.daily) buildTrafficChart(data.daily);
      } catch (_) {}
    };

    setInterval(refreshMetrics, 5 * 60 * 1000);
  }
});
