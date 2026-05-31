/* =================================================================
   FM Admin — DATA LAYER (backend-connected)
   -----------------------------------------------------------------
   This is the SINGLE source of data for the admin SPA.
   All methods call real PHP API endpoints.

   Endpoints:
     GET /api/admin/overview.php?range=7|30|90   → analytics summary
     GET /api/admin/messages.php                  → contact messages
     GET /api/admin/messages.php?id=N             → single message
     GET /api/admin/content.php?locale=ru|en      → site content

   Analytics endpoints for sources / devices / geo / visitors
   are NOT yet implemented on the backend. They return lightweight
   fallback data marked with "// FALLBACK" comments below.
   To wire real data: implement GET /api/admin/analytics.php
   and replace the fallback bodies with fetch() calls.

   Base URL is read from window.CVF.baseUrl (set by PHP layout)
   so no production URL is hardcoded here.
   ================================================================= */
const API = (function () {

  /* ------ helpers ------ */
  function base() {
    try { return (window.CVF && window.CVF.baseUrl) ? window.CVF.baseUrl.replace(/\/$/, "") : ""; }
    catch (_) { return ""; }
  }

  async function fetchJson(path) {
    const url = base() + path;
    const res = await fetch(url, {
      credentials: "same-origin",
      headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
    });
    if (res.status === 401 || res.status === 403) {
      const err = new Error("unauthorized"); err.status = res.status; throw err;
    }
    const json = await res.json().catch(() => null);
    return json;
  }

  /* helper: last N days labels */
  function lastDays(n) {
    const out = []; const d = new Date();
    for (let i = n - 1; i >= 0; i--) {
      const x = new Date(d); x.setDate(d.getDate() - i);
      out.push(x.toLocaleDateString("ru-RU", { day: "2-digit", month: "2-digit" }));
    }
    return out;
  }

  /* helper: flat series for spark/fallback */
  function flatSeries(n, val) {
    return Array.from({ length: n }, () => val);
  }

  return {

    /* ------------------------------------------------------------------
       GET /api/admin/overview.php?range=N
       Returns data in format expected by admin.js renderOverview():
         { kpis:[{key,label,value,delta,dir,spark:[]}],
           views:{labels:[],data:[],unique:[]} }
    ------------------------------------------------------------------ */
    async overview(range) {
      range = range || 30;
      try {
        const res = await fetchJson("/api/admin/overview.php?range=" + encodeURIComponent(range));
        if (!res || !res.ok) return null;

        const daily = Array.isArray(res.daily) ? res.daily : [];
        const s = res.summary || {};
        const msgCounts = res.message_counts || {};

        const views = daily.map(r => Number(r.page_views || 0));
        const unique = daily.map(r => Number(r.unique_visitors || 0));
        const labels = daily.map(r => {
          const d = new Date(r.event_date);
          return d.toLocaleDateString("ru-RU", { day: "2-digit", month: "2-digit" });
        });

        const totalViews = views.reduce((a, b) => a + b, 0);
        const totalUnique = unique.reduce((a, b) => a + b, 0);
        const newMsgs = Number(msgCounts.new || 0);
        const allMsgs = Number(msgCounts.all || 0);

        return {
          kpis: [
            { key: "views",  label: "Просмотры за период",    value: totalViews,  delta: "—",           dir: "up",   spark: views.slice(-14).length ? views.slice(-14) : flatSeries(14, 0) },
            { key: "unique", label: "Уникальные посетители",  value: totalUnique, delta: "—",           dir: "up",   spark: unique.slice(-14).length ? unique.slice(-14) : flatSeries(14, 0) },
            { key: "msgs",   label: "Сообщений (новых " + newMsgs + ")", value: allMsgs, delta: "+" + newMsgs + " новых", dir: "up", spark: flatSeries(14, 1) },
            { key: "time",   label: "Среднее время на сайте", value: "—",         delta: "—",           dir: "up",   spark: flatSeries(14, 0) }
          ],
          views: { labels, data: views, unique }
        };
      } catch (e) {
        console.warn("[admin-data] overview fetch failed:", e);
        return null;
      }
    },

    /* ------------------------------------------------------------------
       GET /api/admin/messages.php
       Returns [{id,name,email,phone,company,budget,message,date,status}]
       status: 'new' | 'read' | 'replied'
    ------------------------------------------------------------------ */
    async messages() {
      try {
        const res = await fetchJson("/api/admin/messages.php");
        if (!res || !res.ok) return [];
        const msgs = Array.isArray(res.messages) ? res.messages : [];
        return msgs.map(m => ({
          id:      m.id,
          name:    m.name    || "—",
          email:   m.email   || "",
          phone:   m.phone   || "—",
          company: m.company || "—",
          budget:  m.budget  || "—",
          message: m.message || "",
          date:    m.created_at
                    ? new Date(m.created_at).toLocaleDateString("ru-RU", { day: "numeric", month: "long", hour: "2-digit", minute: "2-digit" })
                    : "—",
          status:  m.status  || "new"
        }));
      } catch (e) {
        console.warn("[admin-data] messages fetch failed:", e);
        return [];
      }
    },

    /* ------------------------------------------------------------------
       GET /api/admin/content.php?locale=ru
       Returns structured site content for admin editor
    ------------------------------------------------------------------ */
    async content() {
      try {
        const res = await fetchJson("/api/admin/content.php?locale=ru");
        if (!res || !res.ok) return {};

        const c = res.content || {};
        const site = c.site || {};
        const hero = c.hero || {};
        const about = c.about || {};
        const contacts = c.contacts || {};
        const projects = Array.isArray(c.projects) ? c.projects : [];
        const posts = Array.isArray(c.posts) ? c.posts : [];

        return {
          hero: {
            badge: hero.eyebrow || hero.badge || "Открыт к проектной работе",
            title: hero.headline || hero.title || "Backend, который держит продукт.",
            lead:  hero.subheadline || hero.lead || ""
          },
          about: {
            title: about.title || "Сильный backend-фокус без лишнего шума.",
            text:  about.body  || about.text  || ""
          },
          contacts: {
            email:    site.contact_email    || contacts.email    || "fakhridinkon2009@gmail.com",
            phone:    site.contact_phone    || contacts.phone    || "+992 881 845 151",
            telegram: site.contact_telegram || contacts.telegram || "@Fakhriddin_dev",
            linkedin: contacts.linkedin || "",
            github:   contacts.github   || "",
            location: site.location     || contacts.location || "Гиссар, Таджикистан"
          },
          projects: projects.map((p, i) => ({
            title:   p.title_ru || p.title || "Проект " + (i + 1),
            cat:     p.category_name_ru || p.category_name_en || p.category_name || "—",
            stack:   p.technologies || "—",
            live:    Boolean(p.external_url),
            visible: (p.status || "draft") === "published"
          })),
          posts: posts.map(p => ({
            title:   p.title_ru || p.title || "Запись",
            date:    p.published_at ? new Date(p.published_at).toLocaleDateString("ru-RU") : "—",
            status:  (p.status || "draft") === "published" ? "Опубликовано" : "Черновик",
            visible: (p.status || "draft") === "published"
          }))
        };
      } catch (e) {
        console.warn("[admin-data] content fetch failed:", e);
        return {};
      }
    },

    /* ------------------------------------------------------------------
       FALLBACK — sources / devices / geo / visitors
       Backend analytics endpoints are not yet implemented.
       Replace fetch bodies below when GET /api/admin/analytics.php
       is available. The UI reads only from these methods.
    ------------------------------------------------------------------ */

    // FALLBACK: no real endpoint yet — returns placeholder data
    async sources() {
      return [
        { name: "Прямые заходы", value: 45, color: "#E4581F" },
        { name: "Google",        value: 31, color: "#F8B65A" },
        { name: "LinkedIn",      value: 14, color: "#3B73D6" },
        { name: "Telegram",      value: 7,  color: "#2F9E54" },
        { name: "GitHub",        value: 3,  color: "#8C8175" }
      ];
    },

    // FALLBACK: no real endpoint yet — returns placeholder data
    async devices() {
      return [
        { name: "Desktop", value: 68, color: "#16120E" },
        { name: "Mobile",  value: 28, color: "#E4581F" },
        { name: "Tablet",  value: 4,  color: "#F8B65A" }
      ];
    },

    // FALLBACK: real top_projects from overview used when available
    async pages() {
      try {
        const res = await fetchJson("/api/admin/overview.php?range=30");
        if (res && res.ok && Array.isArray(res.top_projects) && res.top_projects.length) {
          const max = res.top_projects[0].view_count || 1;
          return res.top_projects.map(p => ({
            path:  "/projects/" + (p.slug || p.title || ""),
            views: Number(p.view_count || 0),
            share: Math.round((Number(p.view_count || 0) / max) * 100)
          }));
        }
      } catch (_) {}
      return []; // FALLBACK: empty until analytics endpoint is built
    },

    // FALLBACK: no real endpoint yet
    async geo() {
      return []; // Replace with fetch('/api/admin/analytics.php?section=geo') when ready
    },

    // FALLBACK: no real endpoint yet
    async visitors() {
      return []; // Replace with fetch('/api/admin/analytics.php?section=visitors') when ready
    }

  };
})();

/* Expose as window.API (used by admin.js) and window.AdminData (legacy) */
try {
  window.API = API;
  window.AdminData = API;
} catch (_) {}
