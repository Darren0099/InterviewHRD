/* Interview HRD — interaksi UI (form, popup TOP, hapus, filter, konfetti) */
(function () {
  'use strict';

  var csrf = document.querySelector('meta[name="csrf-token"]').content;
  var H = window.HRD || { base: '', regional: '', regionals: [], divisi: {} };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  var NAVY = '#1b1432';

  /* ---------- helper fetch JSON ---------- */
  function api(path, method, body) {
    return fetch(H.base + path, {
      method: method || 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: body ? JSON.stringify(body) : undefined
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (data) {
        if (!res.ok) {
          var msg = 'Terjadi kesalahan (' + res.status + ')';
          if (data.errors) { msg = Object.values(data.errors)[0][0]; }
          else if (data.message) { msg = data.message; }
          throw new Error(msg);
        }
        return data;
      });
    });
  }

  /* ---------- konfetti ---------- */
  function confetti() {
    var c = document.createElement('canvas');
    c.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99999';
    document.body.appendChild(c);
    var ctx = c.getContext('2d');
    var W = c.width = window.innerWidth, Hh = c.height = window.innerHeight;
    var cols = ['#a78bfa', '#7c3aed', NAVY, '#c4b5fd', '#f0abfc', '#818cf8'];
    var ps = [];
    for (var i = 0; i < 150; i++) {
      ps.push({
        x: Math.random() * W, y: -20 - Math.random() * Hh * 0.5,
        r: 4 + Math.random() * 6, c: cols[(Math.random() * cols.length) | 0],
        vx: -2 + Math.random() * 4, vy: 2 + Math.random() * 4,
        rot: Math.random() * 6, vr: -0.2 + Math.random() * 0.4
      });
    }
    var f = 0;
    (function tick() {
      ctx.clearRect(0, 0, W, Hh);
      ps.forEach(function (p) {
        p.x += p.vx; p.y += p.vy; p.rot += p.vr;
        ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.rot);
        ctx.fillStyle = p.c; ctx.fillRect(-p.r, -p.r / 2, p.r * 2, p.r);
        ctx.restore();
      });
      if (++f < 160) { requestAnimationFrame(tick); } else { c.remove(); }
    })();
  }

  /* ---------- count-up angka statistik ---------- */
  $$('[data-count]').forEach(function (el) {
    var target = parseInt(el.dataset.count, 10) || 0;
    if (target === 0) return;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / 700, 1);
      el.textContent = Math.round(target * p);
      if (p < 1) requestAnimationFrame(step);
    }
    el.textContent = '0';
    requestAnimationFrame(step);
  });

  /* ---------- modal form penilaian ---------- */
  var modal = $('#formModal');
  var form = $('#penilaianForm');
  var aspects = ['aspek_teknis', 'aspek_komunikasi', 'aspek_sikap', 'aspek_motivasi'];
  var MAXS = 25;

  function tierInfo(t) {
    if (t >= 90) return { l: 'S', m: 'Luar biasa, kandidat sangat menonjol' };
    if (t >= 80) return { l: 'A', m: 'Sangat bagus, layak diperhitungkan' };
    if (t >= 70) return { l: 'B', m: 'Bagus, potensi kuat' };
    if (t >= 60) return { l: 'C', m: 'Cukup, masih perlu dikembangkan' };
    return { l: 'D', m: 'Geser slider untuk memberi nilai.' };
  }

  function paint() {
    var total = 0;
    aspects.forEach(function (n) {
      var r = form.elements[n];
      var v = parseInt(r.value, 10) || 0;
      total += v;
      r.style.setProperty('--v', (v / MAXS * 100) + '%');
      $('[data-out="' + n + '"]').textContent = v;
    });
    $('#totalNum').textContent = total;
    $('#totalBar').style.width = Math.min(100, total) + '%';
    var t = tierInfo(total);
    $('#tierBadge').textContent = 'Tier ' + t.l;
    $('#tierMsg').textContent = t.m;
  }

  function openForm(d) {
    form.reset();
    form.dataset.id = d && d.id ? d.id : '';
    $('#formTitle').textContent = d ? 'Edit Penilaian' : 'Nilai Kandidat';
    $('#saveBtn').textContent = d ? 'Simpan Perubahan' : 'Simpan Penilaian';

    form.elements.regional.value = (d && d.regional) || H.regional;
    if (d) {
      ['divisi', 'nama_kandidat', 'nama_hrd', 'catatan'].forEach(function (k) { form.elements[k].value = d[k] || ''; });
      aspects.forEach(function (k) { form.elements[k].value = d[k]; });
    } else {
      try { form.elements.nama_hrd.value = localStorage.getItem('hrd_name') || ''; } catch (e) {}
    }
    paint();
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    setTimeout(function () {
      (d ? form.elements.nama_kandidat : (form.elements.nama_hrd.value ? form.elements.nama_kandidat : form.elements.nama_hrd)).focus();
    }, 150);
  }

  function closeForm() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (form) {
    aspects.forEach(function (n) { form.elements[n].addEventListener('input', paint); });

    modal.addEventListener('click', function (e) { if (e.target === modal) closeForm(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeForm(); });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = $('#saveBtn');
      var label = btn.textContent;
      var payload = {};
      new FormData(form).forEach(function (v, k) { payload[k] = v; });
      aspects.forEach(function (k) { payload[k] = parseInt(payload[k], 10) || 0; });

      var id = form.dataset.id;
      btn.disabled = true; btn.textContent = 'Menyimpan…';

      api(id ? '/penilaian/' + id : '/penilaian', id ? 'PUT' : 'POST', payload)
        .then(function (res) {
          try { localStorage.setItem('hrd_name', payload.nama_hrd); } catch (e) {}
          closeForm();
          var html = 'Total skor <b>' + res.total + '</b> · Tier <b>' + res.tier + '</b><br>Peringkat <b>#' + res.rank + '</b> di ' + res.divisi;
          if (res.in_top) { confetti(); html += '<br>Masuk <b>TOP ' + res.kuota + '</b>!'; }
          return Swal.fire({
            icon: 'success', title: res.message, html: html,
            confirmButtonText: 'Lanjut', confirmButtonColor: NAVY
          });
        })
        .then(function () { window.location.reload(); })
        .catch(function (err) {
          Swal.fire({ icon: 'error', title: 'Gagal menyimpan', text: err.message, confirmButtonColor: NAVY });
        })
        .then(function () { btn.disabled = false; btn.textContent = label; });
    });
  }

  /* ---------- aksi klik (delegasi) ---------- */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-action]');
    if (!el) return;
    var act = el.dataset.action;

    if (act === 'open-form') { openForm(null); }
    else if (act === 'close-form') { closeForm(); }
    else if (act === 'edit') { openForm(JSON.parse(el.dataset.edit)); }

    else if (act === 'delete') {
      Swal.fire({
        icon: 'warning', title: 'Hapus kandidat?', html: 'Data <b>' + escapeHtml(el.dataset.nama) + '</b> akan dihapus permanen.',
        showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal',
        confirmButtonColor: '#ef5350', cancelButtonColor: '#8a8a99'
      }).then(function (r) {
        if (!r.isConfirmed) return;
        api('/penilaian/' + el.dataset.id, 'DELETE')
          .then(function (res) { return Swal.fire({ icon: 'success', title: res.message, timer: 1100, showConfirmButton: false }); })
          .then(function () { window.location.reload(); })
          .catch(function (err) { Swal.fire({ icon: 'error', title: 'Gagal', text: err.message }); });
      });
    }

    else if (act === 'delete-regional') {
      var reg = el.dataset.regional;
      Swal.fire({
        icon: 'warning', title: 'Hapus SEMUA data ' + reg + '?',
        html: 'Semua penilaian regional ini akan hilang permanen.<br>Ketik <b>HAPUS</b> untuk konfirmasi.',
        input: 'text', inputPlaceholder: 'HAPUS',
        showCancelButton: true, confirmButtonText: 'Hapus semua', cancelButtonText: 'Batal',
        confirmButtonColor: '#ef5350',
        preConfirm: function (v) {
          if (v !== 'HAPUS') { Swal.showValidationMessage('Ketik HAPUS (huruf besar) untuk lanjut'); return false; }
          return true;
        }
      }).then(function (r) {
        if (!r.isConfirmed) return;
        api('/regional', 'DELETE', { regional: reg })
          .then(function (res) { return Swal.fire({ icon: 'success', title: res.message }); })
          .then(function () { window.location.reload(); })
          .catch(function (err) { Swal.fire({ icon: 'error', title: 'Gagal', text: err.message }); });
      });
    }

    else if (act === 'top') {
      var url = H.base + '/top?regional=' + encodeURIComponent(el.dataset.regional) + '&divisi=' + encodeURIComponent(el.dataset.divisi);
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(function (r) { if (!r.ok) throw new Error('Gagal memuat data'); return r.text(); })
        .then(function (html) {
          Swal.fire({ html: html, width: '40rem', showConfirmButton: false, showCloseButton: true });
        })
        .catch(function (err) { Swal.fire({ icon: 'error', title: 'Gagal', text: err.message }); });
    }
  });

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  /* ---------- pencarian + filter divisi (halaman kandidat) ---------- */
  var q = $('#q');
  var grid = $('#candGrid');
  if (grid) {
    var filter = '';
    var apply = function () {
      var term = (q ? q.value : '').trim().toLowerCase();
      var shown = 0;
      $$('.cand', grid).forEach(function (c) {
        var ok = (!filter || c.dataset.divisi === filter) && (!term || c.dataset.search.indexOf(term) !== -1);
        c.hidden = !ok;
        if (ok) shown++;
      });
      var nr = $('#noResult');
      if (nr) nr.hidden = shown !== 0;
    };
    if (q) q.addEventListener('input', apply);
    $$('#divisiFilter .chip').forEach(function (b) {
      b.addEventListener('click', function () {
        $$('#divisiFilter .chip').forEach(function (x) { x.classList.remove('active'); });
        b.classList.add('active');
        filter = b.dataset.filter;
        apply();
      });
    });
  }
})();
