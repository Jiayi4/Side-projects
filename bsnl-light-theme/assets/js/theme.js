(function () {
  document.querySelectorAll("[data-bsnl-back]").forEach(function (link) {
    link.addEventListener("click", function (event) {
      if (!document.referrer || window.history.length <= 1) return;

      try {
        if (new URL(document.referrer).origin !== window.location.origin) return;
      } catch (error) {
        return;
      }

      event.preventDefault();
      window.history.back();
    });
  });

  document.querySelectorAll("[data-countdown]").forEach(function (box) {
    var target = new Date(box.dataset.countdown);

    function updateCountdown() {
      var diff = Math.max(0, target - new Date());
      var values = {
        days: Math.floor(diff / 86400000),
        hours: Math.floor((diff % 86400000) / 3600000),
        mins: Math.floor((diff % 3600000) / 60000),
        secs: Math.floor((diff % 60000) / 1000)
      };

      Object.keys(values).forEach(function (unit) {
        var targetNode = box.querySelector('[data-unit="' + unit + '"]');
        if (targetNode) {
          targetNode.textContent = unit === "days" ? String(values[unit]) : String(values[unit]).padStart(2, "0");
        }
      });
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
  });

  document.querySelectorAll("[data-highlight-carousel]").forEach(function (carousel) {
    var viewport = carousel.querySelector(".bsnl-highlights-viewport");
    var cards = Array.from(carousel.querySelectorAll(".bsnl-news-card"));
    var dotsWrap = carousel.querySelector(".bsnl-highlight-dots");
    if (!viewport || !cards.length || !dotsWrap) return;

    var activeIndex = 0;

    function visibleCount() {
      if (window.matchMedia("(max-width: 680px)").matches) return 1;
      if (window.matchMedia("(max-width: 980px)").matches) return 2;
      return 3;
    }

    function pageCount() {
      return Math.max(1, cards.length - visibleCount() + 1);
    }

    function renderDots() {
      dotsWrap.innerHTML = "";
      for (var i = 0; i < pageCount(); i += 1) {
        var dot = document.createElement("button");
        dot.type = "button";
        dot.className = "bsnl-highlight-dot";
        dot.setAttribute("aria-label", "Show highlight group " + (i + 1));
        dot.addEventListener("click", setActive.bind(null, i));
        dotsWrap.appendChild(dot);
      }
    }

    function setActive(index) {
      var count = pageCount();
      activeIndex = (index + count) % count;
      viewport.scrollTo({
        left: cards[activeIndex].offsetLeft - cards[0].offsetLeft,
        behavior: "smooth"
      });
      Array.from(dotsWrap.children).forEach(function (dot, dotIndex) {
        dot.classList.toggle("is-active", dotIndex === activeIndex);
      });
    }

    renderDots();
    setActive(0);
    window.addEventListener("resize", function () {
      renderDots();
      setActive(Math.min(activeIndex, pageCount() - 1));
    });
    setInterval(function () {
      setActive(activeIndex + 1);
    }, 5200);
  });

  document.querySelectorAll(".bsnl-topbar").forEach(function (topbar) {
    var toggle = topbar.querySelector(".bsnl-menu-toggle");
    var menu = topbar.querySelector(".bsnl-menu");
    if (!toggle || !menu) return;

    function setMenuOpen(isOpen) {
      topbar.classList.toggle("is-menu-open", isOpen);
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
      toggle.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
    }

    toggle.addEventListener("click", function () {
      setMenuOpen(!topbar.classList.contains("is-menu-open"));
    });

    menu.addEventListener("click", function (event) {
      if (event.target.closest("a")) {
        setMenuOpen(false);
      }
    });

    document.addEventListener("click", function (event) {
      if (!topbar.contains(event.target)) {
        setMenuOpen(false);
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        setMenuOpen(false);
      }
    });
  });

  document.querySelectorAll(".bsnl-search-wrap").forEach(function (wrap) {
    var button = wrap.querySelector(".bsnl-search-button");
    var form = wrap.querySelector(".bsnl-site-search");
    var input = wrap.querySelector("input[type='search']");
    if (!button || !form || !input) return;

    function setOpen(isOpen) {
      wrap.classList.toggle("is-open", isOpen);
      button.setAttribute("aria-expanded", isOpen ? "true" : "false");
      if (isOpen) {
        window.setTimeout(function () {
          input.focus();
        }, 40);
      }
    }

    button.addEventListener("click", function () {
      setOpen(!wrap.classList.contains("is-open"));
    });

    form.addEventListener("submit", function (event) {
      if (!input.value.trim()) {
        event.preventDefault();
        setOpen(false);
      }
    });

    document.addEventListener("click", function (event) {
      if (!wrap.contains(event.target)) {
        setOpen(false);
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        setOpen(false);
      }
    });
  });

  var eventAnchorAliases = [
    {
      ids: ["upcoming", "upcoming-events", "calendar"],
      labels: ["upcoming events", "upcoming"]
    },
    {
      ids: ["how-it-works", "events-introduction", "event-introduction", "main-event-formats", "event-formats"],
      labels: ["how it works", "events introduction", "main event formats", "event formats", "formats"]
    },
    {
      ids: ["life-science-career-day-lscd", "life-science-career-day", "lscd"],
      labels: ["life science career day", "lscd"]
    },
    {
      ids: ["faces-of-industrial-research-fir", "faces-of-industrial-research", "fir"],
      labels: ["faces of industrial research", "fir"]
    },
    {
      ids: ["famelab", "famelab-switzerland"],
      labels: ["famelab", "famelab switzerland"]
    },
    {
      ids: ["biotech-chats", "biotech-chat"],
      labels: ["biotech chats", "biotech chat"]
    },
    {
      ids: ["workshops", "workshop"],
      labels: ["workshops", "workshop"]
    },
    {
      ids: ["company-visits", "company-visit"],
      labels: ["company visits", "company visit"]
    }
  ];

  function normalizeAnchorLabel(value) {
    return String(value || "")
      .toLowerCase()
      .replace(/&amp;/g, "and")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  function eventAnchorFromValue(value) {
    var normalized = normalizeAnchorLabel(value);
    var match = eventAnchorAliases.find(function (entry) {
      return entry.ids.some(function (id) {
        return normalizeAnchorLabel(id) === normalized;
      }) || entry.labels.some(function (label) {
        return normalizeAnchorLabel(label) === normalized;
      });
    });

    return match ? match.ids[0] : "";
  }

  function findEventAnchorTarget(entry) {
    var candidates = Array.from(document.querySelectorAll(".bsnl-page-content h2, .bsnl-page-content h3, .bsnl-event-format-card"));
    return candidates.find(function (candidate) {
      var text = String(candidate.textContent || "").toLowerCase();
      return entry.labels.some(function (label) {
        return text.indexOf(label) !== -1;
      });
    });
  }

  function ensureAnchorBefore(target, id) {
    if (!target || document.getElementById(id)) return;

    var anchor = document.createElement("span");
    anchor.id = id;
    anchor.className = "bsnl-anchor-target";
    anchor.setAttribute("aria-hidden", "true");
    target.parentNode.insertBefore(anchor, target);
  }

  function ensureEventAnchors() {
    eventAnchorAliases.forEach(function (entry) {
      var target = entry.ids.map(function (id) {
        return document.getElementById(id);
      }).filter(Boolean)[0] || findEventAnchorTarget(entry);

      if (!target) return;

      entry.ids.forEach(function (id) {
        ensureAnchorBefore(target, id);
      });
    });
  }

  function normalizeEventMenuLinks() {
    var eventsUrl = window.bsnlLight && window.bsnlLight.eventsUrl ? window.bsnlLight.eventsUrl : "";
    if (!eventsUrl) return;

    document.querySelectorAll(".bsnl-menu .sub-menu a").forEach(function (link) {
      var rawHref = link.getAttribute("href") || "";
      var hash = "";
      var url = null;

      try {
        url = new URL(rawHref, window.location.href);
        if (url.host && url.host !== window.location.host) return;
        hash = url.hash.replace("#", "");
      } catch (error) {
        hash = rawHref.charAt(0) === "#" ? rawHref.slice(1) : "";
      }

      var anchor = eventAnchorFromValue(hash) || eventAnchorFromValue(link.textContent);
      if (!anchor) return;

      link.setAttribute("href", eventsUrl.split("#")[0] + "#" + anchor);
    });
  }

  function scrollToCurrentHash() {
    var hash = window.location.hash ? window.location.hash.slice(1) : "";
    if (!hash) return;

    var anchor = eventAnchorFromValue(hash) || hash;
    var target = document.getElementById(anchor);
    if (!target) return;

    window.requestAnimationFrame(function () {
      target.scrollIntoView({ block: "start", behavior: "smooth" });
    });
  }

  ensureEventAnchors();
  normalizeEventMenuLinks();
  window.setTimeout(scrollToCurrentHash, 80);
  window.addEventListener("hashchange", scrollToCurrentHash);

  document.querySelectorAll(".bsnl-page-with-nav").forEach(function (layout) {
    var links = Array.from(layout.querySelectorAll(".bsnl-page-nav a[href^='#']"));
    var sections = links
      .map(function (link) {
        return document.querySelector(link.getAttribute("href"));
      })
      .filter(Boolean);

    if (!links.length || !sections.length || !("IntersectionObserver" in window)) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        links.forEach(function (link) {
          link.classList.toggle("is-active", link.getAttribute("href") === "#" + entry.target.id);
        });
      });
    }, { rootMargin: "-35% 0px -55% 0px", threshold: 0.01 });

    sections.forEach(function (section) {
      observer.observe(section);
    });
  });

  document.querySelectorAll("[data-gallery-carousel]").forEach(function (carousel) {
    var viewport = carousel.querySelector(".bsnl-gallery-carousel-viewport");
    var slides = Array.from(carousel.querySelectorAll(".bsnl-gallery-slide"));
    var dotsWrap = carousel.querySelector(".bsnl-gallery-dots");
    if (!viewport || !slides.length || !dotsWrap) return;

    var activeIndex = 0;

    function visibleCount() {
      if (carousel.querySelector(".bsnl-gallery-pair-slide")) return 1;
      if (window.matchMedia("(max-width: 680px)").matches) return 1;
      return 2;
    }

    function pageCount() {
      return Math.max(1, slides.length - visibleCount() + 1);
    }

    function renderDots() {
      dotsWrap.innerHTML = "";
      for (var i = 0; i < pageCount(); i += 1) {
        var dot = document.createElement("button");
        dot.type = "button";
        dot.className = "bsnl-gallery-dot";
        dot.setAttribute("aria-label", "Show gallery image " + (i + 1));
        dot.addEventListener("click", setActive.bind(null, i));
        dotsWrap.appendChild(dot);
      }
    }

    function setActive(index) {
      var count = pageCount();
      activeIndex = (index + count) % count;
      viewport.scrollTo({
        left: slides[activeIndex].offsetLeft - slides[0].offsetLeft,
        behavior: "smooth"
      });
      Array.from(dotsWrap.children).forEach(function (dot, dotIndex) {
        dot.classList.toggle("is-active", dotIndex === activeIndex);
      });
    }

    renderDots();
    setActive(0);
    window.addEventListener("resize", function () {
      renderDots();
      setActive(Math.min(activeIndex, pageCount() - 1));
    });
    setInterval(function () {
      setActive(activeIndex + 1);
    }, 4600);
  });
}());
