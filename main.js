/**
 * Энциклопедия по информатике — интерактивность
 */

document.addEventListener('DOMContentLoaded', function() {
  var THEME_STORAGE_KEY = 'infostudy-theme';
  var themeToggle = document.getElementById('theme-toggle');
  var themeToggleIcon = themeToggle ? themeToggle.querySelector('.theme-toggle-icon') : null;
  var themeToggleText = themeToggle ? themeToggle.querySelector('.theme-toggle-text') : null;

  function getPreferredTheme() {
    var savedTheme = null;
    try {
      savedTheme = localStorage.getItem(THEME_STORAGE_KEY);
    } catch (e) {}
    if (savedTheme === 'light' || savedTheme === 'dark') {
      return savedTheme;
    }

    try {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
        return 'light';
      }
    } catch (e) {}
    return 'dark';
  }

  function applyTheme(theme) {
    var isLight = theme === 'light';
    document.body.classList.toggle('light-theme', isLight);
    if (themeToggle) {
      if (themeToggleIcon) {
        themeToggleIcon.textContent = isLight ? '☀️' : '🌙';
      }
      if (themeToggleText) {
        themeToggleText.textContent = isLight ? 'Темная тема' : 'Светлая тема';
      } else {
        themeToggle.textContent = isLight ? 'Темная тема' : 'Светлая тема';
      }
      themeToggle.setAttribute('aria-label', isLight ? 'Включить темную тему' : 'Включить светлую тему');
    }
  }

  function saveTheme(theme) {
    try {
      localStorage.setItem(THEME_STORAGE_KEY, theme);
    } catch (e) {}
  }

  var activeTheme = getPreferredTheme();
  applyTheme(activeTheme);

  if (themeToggle) {
    themeToggle.addEventListener('click', function() {
      activeTheme = activeTheme === 'light' ? 'dark' : 'light';
      applyTheme(activeTheme);
      saveTheme(activeTheme);
    });
  }

  // Запуск видеофона в блоке героя и в карточках разделов (для политики autoplay в браузерах)
  var videos = document.querySelectorAll('.hero-video-bg, .section-card-media');
  videos.forEach(function(v) {
    if (v.tagName === 'VIDEO') {
      v.muted = true;
      v.play().catch(function() {});
    }
  });

  // Плавная прокрутка к якорям (если появятся)
  document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
      var targetId = this.getAttribute('href');
      if (targetId === '#') return;
      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Подсветка активной кнопки навигации при скролле (опционально)
  var navButtons = document.querySelectorAll('.nav-btn');
  navButtons.forEach(function(btn) {
    btn.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px)';
    });
    btn.addEventListener('mouseleave', function() {
      if (!this.classList.contains('active')) {
        this.style.transform = 'translateY(0)';
      }
    });
  });

  // Выпадающие темы в кнопках разделов
  var navExpandLinks = document.querySelectorAll('[data-nav-expand="true"]');
  if (navExpandLinks.length) {
    function closeAllTopicMenus(exceptId) {
      navExpandLinks.forEach(function(link) {
        var groupId = link.getAttribute('data-group-id');
        var topics = document.querySelector('[data-nav-topics="' + groupId + '"]');
        var keepOpen = exceptId && exceptId === groupId;
        if (!keepOpen) {
          link.classList.remove('expanded');
          link.setAttribute('aria-expanded', 'false');
          if (topics) topics.classList.remove('open');
        }
      });
    }

    navExpandLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        var groupId = link.getAttribute('data-group-id');
        var topics = document.querySelector('[data-nav-topics="' + groupId + '"]');
        if (!topics) return;

        var isOpen = topics.classList.contains('open');
        if (!isOpen) {
          e.preventDefault();
          closeAllTopicMenus(groupId);
          topics.classList.add('open');
          link.classList.add('expanded');
          link.setAttribute('aria-expanded', 'true');
        }
      });
    });

    document.addEventListener('click', function(e) {
      var clickedInsideNavGroup = e.target.closest('.nav-group');
      if (!clickedInsideNavGroup) {
        closeAllTopicMenus();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeAllTopicMenus();
      }
    });
  }

  // Плейсхолдер для отсутствующих изображений — не ломать верстку
  document.querySelectorAll('.media-block img').forEach(function(img) {
    img.addEventListener('error', function() {
      this.style.background = 'linear-gradient(135deg, #21262d 0%, #30363d 100%)';
      this.alt = this.alt || 'Изображение (добавьте файл: ' + this.src.split('/').pop() + ')';
    });
  });

  // Анимация появления при скролле: главный блок и карточки разделов
  var scrollTargets = document.querySelectorAll('.hero-video-block, .section-card');
  if (scrollTargets.length && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('scroll-in');
        }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });
    scrollTargets.forEach(function(el) { observer.observe(el); });
  } else {
    scrollTargets.forEach(function(el) { el.classList.add('scroll-in'); });
  }

  // Анимированные "глаза" (страница поиска): следят за курсором + моргают
  var eyes = document.querySelectorAll('[data-eye]');
  if (eyes && eyes.length) {
    var raf = null;
    var lastX = window.innerWidth / 2;
    var lastY = window.innerHeight / 2;

    function clamp(v, min, max) {
      return Math.max(min, Math.min(max, v));
    }

    function updateEyes() {
      raf = null;
      eyes.forEach(function(eye) {
        var pupil = eye.querySelector('[data-pupil]');
        if (!pupil) return;

        var rect = eye.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        var cx = rect.left + rect.width / 2;
        var cy = rect.top + rect.height / 2;
        var dx = lastX - cx;
        var dy = lastY - cy;

        // максимально возможный сдвиг зрачка внутри глаза
        var maxX = Math.max(4, (rect.width - 10) / 2 - 3);
        var maxY = Math.max(3, (rect.height - 10) / 2 - 2);

        var len = Math.sqrt(dx * dx + dy * dy) || 1;
        var nx = dx / len;
        var ny = dy / len;

        var px = clamp(nx * maxX, -maxX, maxX);
        var py = clamp(ny * maxY, -maxY, maxY);

        pupil.style.transform = 'translate(-50%, -50%) translate(' + px.toFixed(1) + 'px, ' + py.toFixed(1) + 'px)';
      });
    }

    function scheduleUpdate() {
      if (!raf) raf = window.requestAnimationFrame(updateEyes);
    }

    function onPointerMove(e) {
      lastX = e.clientX;
      lastY = e.clientY;
      scheduleUpdate();
    }

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('mousemove', onPointerMove, { passive: true }); // fallback
    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('resize', scheduleUpdate, { passive: true });
    scheduleUpdate();

    // Моргалка: редкие рандомные моргания
    var reduceMotion = false;
    try {
      reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch (e) {}

    if (!reduceMotion) {
      function blinkOnce() {
        eyes.forEach(function(eye) {
          eye.classList.add('is-blinking');
          setTimeout(function() { eye.classList.remove('is-blinking'); }, 190);
        });
      }

      (function blinkLoop() {
        var delay = 2200 + Math.random() * 4200; // 2.2–6.4 сек
        setTimeout(function() {
          blinkOnce();
          // иногда двойное моргание
          if (Math.random() < 0.22) {
            setTimeout(blinkOnce, 220);
          }
          blinkLoop();
        }, delay);
      })();
    }
  }
});
