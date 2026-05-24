(function () {
  var cfg = window.INFST_READING;
  if (!cfg || !cfg.section || !cfg.csrf) return;

  var section = cfg.section;
  var csrf = cfg.csrf;
  var lastSent = 0;
  var t = null;

  function deepestPercent() {
    var doc = document.documentElement;
    var total = doc.scrollHeight;
    if (total <= 0) return 0;
    var viewed = window.scrollY + window.innerHeight;
    if (total <= window.innerHeight) return 100;
    return Math.min(100, Math.round((viewed / total) * 100));
  }

  function send(percent) {
    percent = Math.min(100, Math.max(0, percent | 0));
    if (percent <= lastSent) return;
    if (percent < 100 && percent - lastSent < 5) return;
    lastSent = percent;
    fetch('save_progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        section: section,
        percent: percent,
        csrf_token: csrf
      }),
      credentials: 'same-origin',
      keepalive: true
    }).catch(function () {});
  }

  function schedule() {
    if (t) clearTimeout(t);
    t = setTimeout(function () {
      t = null;
      send(deepestPercent());
    }, 1200);
  }

  window.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('resize', schedule, { passive: true });
  window.addEventListener('load', function () {
    schedule();
    setTimeout(function () { send(deepestPercent()); }, 2000);
  });
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') {
      send(deepestPercent());
    }
  });
})();
