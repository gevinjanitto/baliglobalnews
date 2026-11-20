document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();

  const pre = document.getElementById('preloader');
  if (pre) {
    if (sessionStorage.getItem('bgn-seen')) pre.remove();
    else { sessionStorage.setItem('bgn-seen', '1'); window.addEventListener('load', () => setTimeout(() => pre.classList.add('done'), 500)); setTimeout(() => pre.classList.add('done'), 2500); }
  }

  const $ = s => document.querySelector(s), $$ = s => document.querySelectorAll(s);
  const openSearch = () => { $('#searchOverlay')?.classList.toggle('open'); setTimeout(() => $('#searchOverlay input')?.focus(), 80); };
  const toggleDrawer = () => { $('#drawer')?.classList.toggle('open'); $('#drawerBg')?.classList.toggle('open'); document.body.style.overflow = $('#drawer')?.classList.contains('open') ? 'hidden' : ''; };
  $$('[data-open-search]').forEach(b => b.addEventListener('click', openSearch));
  $$('[data-open-drawer]').forEach(b => b.addEventListener('click', toggleDrawer));
  $('#drawerBg')?.addEventListener('click', toggleDrawer);
  $('#searchOverlay')?.addEventListener('click', e => { if (e.target.id === 'searchOverlay') e.target.classList.remove('open'); });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { $$('.overlay.open').forEach(el => el.classList.remove('open')); if ($('#drawer')?.classList.contains('open')) toggleDrawer(); }
    if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) { e.preventDefault(); openSearch(); }
  });

  const header = $('#siteHeader');
  // mega menu positioned under its item (nav scrolls horizontally)
  $$('.has-mega').forEach(li => li.addEventListener('mouseenter', () => {
    const m = li.querySelector('.mega'), r = li.getBoundingClientRect();
    const hr = header.getBoundingClientRect();
    m.style.top = (r.bottom - hr.top + 6) + 'px';
    m.style.left = (Math.min(Math.max(r.left + r.width / 2, 300), window.innerWidth - 300) - hr.left) + 'px';
  }));

  // header compact + back to top + reading progress
  const toTop = $('#toTop'), bar = $('#progress');
  const onScroll = () => {
    const y = window.scrollY, h = document.documentElement;
    header?.classList.toggle('compact', y > 60);
    toTop?.classList.toggle('show', y > 600);
    if (bar) bar.style.width = (y / (h.scrollHeight - h.clientHeight) * 100) + '%';
  };
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
  toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // active nav pill into view on mobile
  const act = $('.mainnav a.active'), navUl = $('.mainnav ul');
  if (act && navUl) navUl.scrollLeft = act.parentElement.offsetLeft - navUl.clientWidth / 2 + act.offsetWidth / 2;

  const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .1, rootMargin: '0px 0px -40px 0px' });
  $$('.reveal').forEach(el => io.observe(el));

  // hero slider
  const hs = $('#heroSlider');
  if (hs) {
    const slides = hs.querySelectorAll('.hs-slide'), bars = hs.querySelectorAll('[data-hs-go]');
    let cur = 0, timer;
    const go = i => {
      cur = (i + slides.length) % slides.length;
      slides.forEach((s, k) => s.classList.toggle('on', k === cur));
      bars.forEach((b, k) => { b.classList.remove('on'); b.classList.toggle('done', k < cur); void b.offsetWidth; if (k === cur) b.classList.add('on'); });
      clearTimeout(timer); if (!hs.classList.contains('paused')) timer = setTimeout(() => go(cur + 1), 6000);
    };
    hs.querySelectorAll('[data-hs]').forEach(b => b.addEventListener('click', e => { e.preventDefault(); go(cur + (b.dataset.hs === 'next' ? 1 : -1)); }));
    bars.forEach(b => b.addEventListener('click', e => { e.preventDefault(); go(+b.dataset.hsGo); }));
    hs.addEventListener('mouseenter', () => { hs.classList.add('paused'); clearTimeout(timer); });
    hs.addEventListener('mouseleave', () => { hs.classList.remove('paused'); go(cur); });
    let sx = null;
    hs.addEventListener('touchstart', e => sx = e.touches[0].clientX, { passive: true });
    hs.addEventListener('touchend', e => { if (sx === null) return; const dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 40) go(cur + (dx < 0 ? 1 : -1)); sx = null; });
    if (slides.length > 1) go(0);
  }

  const track = $('#ecTrack');
  $$('[data-ec]').forEach(b => b.addEventListener('click', () => {
    const w = track.querySelector('.ec-item:not(.hide)')?.offsetWidth || 300;
    track.scrollBy({ left: (b.dataset.ec === 'next' ? 1 : -1) * (w + 24), behavior: 'smooth' });
  }));
  $$('[data-ec-filter]').forEach(c => c.addEventListener('click', () => {
    $$('[data-ec-filter]').forEach(x => x.classList.toggle('on', x === c));
    track.querySelectorAll('.ec-item').forEach(i => i.classList.toggle('hide', c.dataset.ecFilter !== 'all' && i.dataset.cat !== c.dataset.ecFilter));
    track.scrollTo({ left: 0 });
  }));

  $$('[data-region-tab]').forEach(c => c.addEventListener('click', () => {
    $$('[data-region-tab]').forEach(x => x.classList.toggle('on', x === c));
    $$('[data-region-list]').forEach(l => l.classList.toggle('hide', l.dataset.regionList !== c.dataset.regionTab));
  }));

  $$('[data-copy]').forEach(b => b.addEventListener('click', () => {
    if (navigator.clipboard) navigator.clipboard.writeText(b.dataset.copy);
    else { const t = document.createElement('textarea'); t.value = b.dataset.copy; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove(); }
    const t = $('#copyToast'); if (t) { t.classList.add('show'); setTimeout(() => t.classList.remove('show'), 1600); }
  }));
});
