/**
 * HiFiPigTheme - header.js
 * - Theme toggle (no-flash apply)
 * - Search overlay
 * - Mobile menu popup panel (drill-down)
 *
 * Markup expected:
 * - Theme toggle button: [data-theme-toggle]
 * - Search button: [data-search-open]
 * - Search overlay: [data-search-overlay] with [data-search-input] and [data-search-results]
 * - Hamburger: .menu-toggle
 * - Desktop nav: .site-header .site-nav containing ul#primary-menu (WP menu)
 */

(function () {
  // Prevent double-init
  if (document.documentElement.classList.contains("js-header-ready")) return;
  document.documentElement.classList.add("js-header-ready");

  const docEl = document.documentElement;
  const body = document.body;
  const whenIdle = (fn) => {
    if ("requestIdleCallback" in window) {
      window.requestIdleCallback(fn, { timeout: 1500 });
      return;
    }
    setTimeout(fn, 400);
  };

  /* -----------------------------
   * Theme (apply ASAP = no flash)
   * ----------------------------- */
  (function initTheme() {
    const KEY = "hifipig_theme";

    // Apply saved theme immediately (fixes “loads wrong then switches”)
    const existing = docEl.getAttribute("data-theme");
    if (existing !== "dark" && existing !== "light" && existing !== "pink") {
      try {
        const saved = localStorage.getItem(KEY);
        if (saved === "dark" || saved === "light" || saved === "pink") {
          docEl.setAttribute("data-theme", saved);
          docEl.style.colorScheme = saved === "dark" ? "dark" : "light";
        }
      } catch (e) {}
    }

    const btn = document.querySelector("[data-theme-toggle]");
    if (!btn) return;

    function setTheme(mode) {
      docEl.setAttribute("data-theme", mode);
      docEl.style.colorScheme = mode === "dark" ? "dark" : "light";
      try {
        localStorage.setItem(KEY, mode);
      } catch (e) {}
      btn.setAttribute("aria-pressed", mode === "dark" ? "true" : "false");
    }

    // Sync aria on load
    const current = docEl.getAttribute("data-theme");
    btn.setAttribute("aria-pressed", current === "dark" ? "true" : "false");

    btn.addEventListener("click", () => {
      const cur = docEl.getAttribute("data-theme") === "dark" ? "dark" : "light";
      setTheme(cur === "dark" ? "light" : "dark");
    });

    // Easter egg: Konami code enables hidden pink mode.
    const konami = [
      "ArrowUp", "ArrowUp",
      "ArrowDown", "ArrowDown",
      "ArrowLeft", "ArrowRight",
      "ArrowLeft", "ArrowRight",
      "b", "a",
    ];
    let pos = 0;
    let phraseBuffer = "";
    let phraseAt = 0;

    document.addEventListener("keydown", (e) => {
      if (e.ctrlKey || e.metaKey || e.altKey) return;

      const key = (e.key || "").toLowerCase();
      const expected = konami[pos].toLowerCase();
      if (key === expected) {
        pos += 1;
        if (pos === konami.length) {
          pos = 0;
          setTheme("pink");
        }
        return;
      }
      pos = key === konami[0].toLowerCase() ? 1 : 0;

      // Easter egg phrase: type "pink pig" then press Enter.
      const now = Date.now();
      if (now - phraseAt > 4000) {
        phraseBuffer = "";
      }

      if (key === "backspace") {
        phraseBuffer = phraseBuffer.slice(0, -1);
        phraseAt = now;
        return;
      }

      if (key === "enter") {
        if (phraseBuffer.trim() === "pink pig") {
          setTheme("pink");
        }
        phraseBuffer = "";
        phraseAt = now;
        return;
      }

      if (key === " ") {
        phraseBuffer = (phraseBuffer + " ").replace(/\s+/g, " ").slice(-24);
        phraseAt = now;
        return;
      }

      if (key.length === 1) {
        phraseBuffer = (phraseBuffer + key).slice(-24);
        phraseAt = now;
      }
    });
  })();

  /* -----------------------------
   * Cookie notice
   * ----------------------------- */
  const createNoticeFromHTML = (html) => {
    const template = document.createElement("template");
    template.innerHTML = html.trim();
    const node = template.content.firstElementChild;
    return node instanceof HTMLElement ? node : null;
  };

  const getNoticeStack = () => document.querySelector("[data-notice-stack]");

  const ensureCookieNotice = () => {
    const stack = getNoticeStack();
    if (!stack) return null;

    let notice = stack.querySelector("[data-cookie-notice]");
    if (notice) return notice;

    notice = createNoticeFromHTML(`
      <div class="cookie-notice notice-card is-hidden" role="dialog" aria-live="polite" aria-label="Cookie notice" data-cookie-notice>
        <p class="cookie-notice__title">Cookies</p>
        <p class="cookie-notice__text">We use cookies to improve your experience.</p>
        <div class="cookie-notice__actions">
          <button class="cookie-notice__btn cookie-notice__btn--accept" type="button" data-cookie-accept>Accept</button>
          <button class="cookie-notice__btn cookie-notice__btn--decline" type="button" data-cookie-decline>Decline</button>
        </div>
      </div>
    `);

    if (!notice) return null;

    stack.appendChild(notice);
    return notice;
  };

  const ensureSubscribeNotice = () => {
    const stack = getNoticeStack();
    if (!stack) return null;

    let notice = stack.querySelector("[data-subscribe-notice]");
    if (notice) return notice;

    notice = createNoticeFromHTML(`
      <div class="subscribe-notice notice-card is-hidden" role="dialog" aria-live="polite" aria-label="Subscribe" data-subscribe-notice>
        <p class="cookie-notice__title">Subscribe</p>
        <p class="cookie-notice__text"><strong>Thanks for visiting.</strong> Stay up to date with Hifi Pig's newsletter.</p>
        <form
          class="subscribe-notice__form"
          action="https://hifipig.us3.list-manage.com/subscribe/post?u=5dc87d0c48b2fa1cfc7f3b168&id=8326f3b4d5"
          accept-charset="UTF-8"
          method="post"
          enctype="multipart/form-data"
          novalidate
          target="_blank"
        >
          <input type="hidden" name="u" value="5dc87d0c48b2fa1cfc7f3b168">
          <input type="hidden" name="id" value="8326f3b4d5">
          <div class="subscribe-notice__honeypot field-shift" aria-hidden="true">
            <label for="b_name">Name</label>
            <input type="text" name="b_name" tabindex="-1" value="" id="b_name">
            <label for="b_email">Email</label>
            <input type="email" name="b_email" tabindex="-1" value="" id="b_email">
            <label for="b_comment">Comment</label>
            <textarea name="b_comment" tabindex="-1" id="b_comment"></textarea>
          </div>
          <div class="subscribe-notice__field">
            <label for="MERGE0">Email Address <span aria-hidden="true">*</span></label>
            <input class="subscribe-notice__input" type="email" name="MERGE0" id="MERGE0" required>
          </div>
          <div class="subscribe-notice__field">
            <label for="MERGE1">First Name</label>
            <input class="subscribe-notice__input" type="text" name="MERGE1" id="MERGE1">
          </div>
          <div class="subscribe-notice__field">
            <label for="MERGE2">Last Name</label>
            <input class="subscribe-notice__input" type="text" name="MERGE2" id="MERGE2">
          </div>
          <div class="subscribe-notice__gdpr">
            <label class="subscribe-notice__label">Marketing Permissions <span aria-hidden="true">*</span></label>
            <p class="subscribe-notice__legal">Please select all the ways you would like to hear from us:</p>
            <div class="subscribe-notice__checkboxes">
              <label class="subscribe-notice__checkbox" for="gdpr_577">
                <input type="checkbox" id="gdpr_577" name="gdpr[577]" value="Y">
                <span>Email</span>
              </label>
              <label class="subscribe-notice__checkbox" for="gdpr_581">
                <input type="checkbox" id="gdpr_581" name="gdpr[581]" value="Y">
                <span>Direct Mail</span>
              </label>
              <label class="subscribe-notice__checkbox" for="gdpr_585">
                <input type="checkbox" id="gdpr_585" name="gdpr[585]" value="Y">
                <span>Customized online advertising</span>
              </label>
            </div>
            <p class="subscribe-notice__legal">You can change your mind at any time by clicking the unsubscribe link in the footer of any email you receive from us, or by contacting us at stuart@hifipig.com. We will treat your information with respect.</p>
            <p class="subscribe-notice__legal">We use Mailchimp as our marketing platform. By clicking below to subscribe, you acknowledge that your information will be transferred to Mailchimp for processing. <a href="https://mailchimp.com/legal/terms" target="_blank" rel="noopener">Learn more</a>.</p>
          </div>
          <div class="subscribe-notice__actions">
            <button class="cookie-notice__btn cookie-notice__btn--accept" type="submit">Subscribe</button>
            <button class="cookie-notice__btn cookie-notice__btn--decline" type="button" data-subscribe-dismiss>No thanks</button>
          </div>
          <input type="hidden" name="ht" value="7731c78a1eead3afbdeb40bcadb57061f6b86b07:MTc2OTQ1MDc2NC4xOTk=">
          <input type="hidden" name="mc_signupsource" value="hosted">
        </form>
      </div>
    `);

    if (!notice) return null;

    stack.appendChild(notice);
    return notice;
  };

  whenIdle(function initCookieNotice() {
    const KEY = "hifipig_cookie_choice";

    const saved = (() => {
      try {
        return localStorage.getItem(KEY);
      } catch (e) {
        return null;
      }
    })();

    if (saved === "accept" || saved === "decline") return;

    const notice = ensureCookieNotice();
    if (!notice) return;

    notice.classList.remove("is-hidden");

    const accept = notice.querySelector("[data-cookie-accept]");
    const decline = notice.querySelector("[data-cookie-decline]");

    function setChoice(value) {
      try { localStorage.setItem(KEY, value); } catch (e) {}
      notice.classList.add("is-hidden");
    }

    accept?.addEventListener("click", () => setChoice("accept"));
    decline?.addEventListener("click", () => setChoice("decline"));
  });

  /* -----------------------------
   * Subscribe notice
   * ----------------------------- */
  whenIdle(function initSubscribeNotice() {
    const KEY = "hifipig_subscribe_choice";

    const saved = (() => {
      try {
        return localStorage.getItem(KEY);
      } catch (e) {
        return null;
      }
    })();

    if (saved === "dismissed" || saved === "submitted") return;

    const notice = ensureSubscribeNotice();
    if (!notice) return;

    const dismiss = notice.querySelector("[data-subscribe-dismiss]");
    const form = notice.querySelector("form");

    const show = () => notice.classList.remove("is-hidden");
    const hide = (value) => {
      try { localStorage.setItem(KEY, value); } catch (e) {}
      notice.classList.add("is-hidden");
    };

    setTimeout(show, 1200);
    dismiss?.addEventListener("click", () => hide("dismissed"));
    form?.addEventListener("submit", () => hide("submitted"));
  });

  /* -----------------------------
   * Search overlay
   * ----------------------------- */
  let searchReady = false;
  const searchBtn = document.querySelector("[data-search-open]");
  const initSearch = (autoOpen) => {
    if (searchReady) return;
    searchReady = true;

    const openBtn = searchBtn || document.querySelector("[data-search-open]");
    const overlay = document.querySelector("[data-search-overlay]");
    const input = document.querySelector("[data-search-input]");
    const results = document.querySelector("[data-search-results]");
    if (!openBtn || !overlay || !input || !results) return;

    const open = () => {
      overlay.classList.add("is-open");
      setTimeout(() => input.focus(), 0);
    };

    const close = () => {
      overlay.classList.remove("is-open");
      input.value = "";
      results.innerHTML = "";
    };

    openBtn.addEventListener("click", open);
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) close();
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") close();
    });

    // Ajax search (expects window.HiFiPig.ajaxUrl and optional window.HiFiPig.nonce)
    let t = 0;
    let activeController = null;
    const cachedResults = new Map();

    const renderResults = (items) => {
      results.innerHTML =
        items
          .map(
            (item) => `
              <a class="search-result" href="${item.url}">
                <p class="search-result__title">${item.title}</p>
                <p class="search-result__meta">${item.date}</p>
              </a>
            `
          )
          .join("") ||
        `<div class="search-result"><p class="search-result__meta">No results</p></div>`;
    };

    input.addEventListener("input", () => {
      const q = input.value.trim();
      clearTimeout(t);

      if (q.length < 2) {
        if (activeController) {
          activeController.abort();
          activeController = null;
        }
        results.innerHTML = "";
        return;
      }

      t = setTimeout(async () => {
        try {
          if (!window.HiFiPig || !HiFiPig.ajaxUrl) return;

          if (cachedResults.has(q)) {
            renderResults(cachedResults.get(q));
            return;
          }

          if (activeController) {
            activeController.abort();
          }

          activeController = new AbortController();

          const params = new URLSearchParams({
            action: "hifipig_search",
            q,
          });

          if (HiFiPig.nonce) {
            params.set("nonce", HiFiPig.nonce);
          }

          const url = `${HiFiPig.ajaxUrl}?${params.toString()}`;

          const res = await fetch(url, {
            credentials: "same-origin",
            signal: activeController.signal,
          });
          const json = await res.json();
          const items = json?.data?.results || [];
          cachedResults.set(q, items);
          if (q !== input.value.trim()) {
            return;
          }
          renderResults(items);
        } catch (err) {
          if (err && err.name === "AbortError") {
            return;
          }
          results.innerHTML = `<div class="search-result"><p class="search-result__meta">Error loading results</p></div>`;
        } finally {
          activeController = null;
        }
      }, 120);
    });

    if (autoOpen) open();
  };

  if (searchBtn) {
    searchBtn.addEventListener("click", () => initSearch(true), { once: true });
  }
  whenIdle(() => initSearch(false));

  /* -----------------------------
   * Mobile menu popup panel (Option B)
   * - Builds a drill-down menu from ul#primary-menu
   * - Scrollable panel, Back button
   * ----------------------------- */
  let mobileReady = false;
  const initMobilePanel = (autoOpen) => {
    if (mobileReady) return;
    mobileReady = true;
    const header = document.querySelector(".site-header");
    const nav = header?.querySelector(".site-nav");
    const menuToggle = header?.querySelector(".menu-toggle");
    if (!header || !nav || !menuToggle) return;

    const mqMobile = window.matchMedia("(max-width: 900px)");

    const ul =
      nav.querySelector("ul#primary-menu") ||
      nav.querySelector("ul.menu") ||
      nav.querySelector("ul");
    if (!ul) return;

    // Create overlay/panel if not present
    let overlay = document.querySelector(".nav-overlay");
    if (!overlay) {
      overlay = document.createElement("div");
      overlay.className = "nav-overlay";
      overlay.setAttribute("aria-hidden", "true");

      const panel = document.createElement("div");
      panel.className = "nav-panel";
      panel.setAttribute("role", "dialog");
      panel.setAttribute("aria-modal", "true");
      panel.setAttribute("aria-label", "Menu");

      panel.innerHTML = `
        <div class="nav-panel__head">
          <button class="nav-panel__back" type="button" aria-label="Back" disabled>Back</button>
          <div class="nav-panel__title">Menu</div>
          <button class="nav-panel__close" type="button" aria-label="Close">Close</button>
        </div>
        <div class="nav-panel__body"></div>
      `;

      overlay.appendChild(panel);
      body.appendChild(overlay);
    }

    const backBtn = overlay.querySelector(".nav-panel__back");
    const closeBtn = overlay.querySelector(".nav-panel__close");
    const titleEl = overlay.querySelector(".nav-panel__title");
    const bodyEl = overlay.querySelector(".nav-panel__body");

    if (!backBtn || !closeBtn || !titleEl || !bodyEl) return;

    function toTree(ulEl) {
      const out = [];
      Array.from(ulEl.children).forEach((li) => {
        if (!li || li.nodeType !== 1) return;
        const a = li.querySelector(":scope > a");
        if (!a) return;
        const childUl = li.querySelector(":scope > ul");
        out.push({
          title: (a.textContent || "").trim(),
          href: a.getAttribute("href") || "#",
          children: childUl ? toTree(childUl) : null,
        });
      });
      return out;
    }

    let tree = null;
    const stack = [];

    function ensureTree() {
      if (tree) return;
      tree = toTree(ul);
      stack.splice(0, stack.length, { title: "Menu", href: null, items: tree });
    }

    function renderLevel(level) {
      const list = document.createElement("ul");
      list.className = "nav-mobile-list";

      // Sublevel “View all”
      if (level.href) {
        const viewAll = document.createElement("li");
        viewAll.className = "nav-mobile-item nav-mobile-item--viewall";
        viewAll.innerHTML = `<a href="${level.href}">View all</a>`;
        list.appendChild(viewAll);
      }

      (level.items || []).forEach((it) => {
        const li = document.createElement("li");
        li.className = "nav-mobile-item";

        const link = document.createElement("a");
        link.href = it.href || "#";
        link.textContent = it.title || "";
        li.appendChild(link);

        if (it.children && it.children.length) {
          const chev = document.createElement("span");
          chev.className = "nav-mobile-chev";
          chev.textContent = "›";
          li.appendChild(chev);

          // Drill down (panel UX)
          li.addEventListener("click", (e) => {
            e.preventDefault();
            stack.push({ title: it.title, href: it.href, items: it.children });
            render();
            animateIn();
          });
        }

        list.appendChild(li);
      });

      return list;
    }

    function render() {
      ensureTree();
      const level = stack[stack.length - 1];
      if (!level) return;

      titleEl.textContent = level.title || "Menu";
      backBtn.disabled = stack.length <= 1;

      bodyEl.innerHTML = "";
      bodyEl.appendChild(renderLevel(level));
    }

    function animateIn() {
      bodyEl.classList.remove("slide-in", "slide-out");
      bodyEl.classList.add("slide-in");
      setTimeout(() => bodyEl.classList.remove("slide-in"), 220);
    }

    function openPanel() {
      ensureTree();
      render();
      overlay.classList.add("is-open");
      overlay.setAttribute("aria-hidden", "false");
      menuToggle.setAttribute("aria-expanded", "true");
      body.style.overflow = "hidden";
      setTimeout(() => closeBtn.focus(), 0);
    }

    function closePanel() {
      overlay.classList.remove("is-open");
      overlay.setAttribute("aria-hidden", "true");
      menuToggle.setAttribute("aria-expanded", "false");
      body.style.overflow = "";

      // reset stack
      if (stack.length > 1) {
        stack.splice(1);
        render();
      }
    }

    menuToggle.addEventListener("click", () => {
      if (!mqMobile.matches) return;
      if (overlay.classList.contains("is-open")) closePanel();
      else openPanel();
    });

    closeBtn.addEventListener("click", closePanel);
    backBtn.addEventListener("click", () => {
      if (stack.length <= 1) return;
      stack.pop();
      render();
      animateIn();
    });

    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) closePanel();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && overlay.classList.contains("is-open")) closePanel();
    });

    window.addEventListener(
      "resize",
      () => {
        if (!mqMobile.matches && overlay.classList.contains("is-open")) closePanel();
      },
      { passive: true }
    );
    if (autoOpen) openPanel();
  };

  const menuToggle = document.querySelector(".menu-toggle");
  if (menuToggle) {
    menuToggle.addEventListener("click", () => initMobilePanel(true), { once: true });
  }
  whenIdle(() => initMobilePanel(false));
})();
