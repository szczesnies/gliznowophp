<?php
declare(strict_types=1);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="theme-color" content="#0f0f0f">
  <meta name="application-name" content="Maszyny Gliznowo">
  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" href="/icon-192.png">
  <title>Maszyny Gliznowo</title>
  <style>
    *{box-sizing:border-box}body{margin:0;background:#0f0f0f;color:#f4f4f5;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}button,input,textarea,select{font:inherit}button{cursor:pointer;border:0}.wrap{max-width:1500px;margin:0 auto;padding:12px 12px 80px}.banner{border:1px solid rgba(234,179,8,.35);border-radius:18px;overflow:hidden;background:#0b0b0b;box-shadow:0 20px 50px rgba(0,0,0,.35);margin-bottom:14px}.banner img{display:block;width:100%;max-height:260px;min-height:110px;object-fit:contain;filter:brightness(1.08) contrast(1.08) saturate(1.08)}.panel{background:#171717;border:1px solid rgba(234,179,8,.16);border-radius:18px;padding:12px;box-shadow:0 12px 30px rgba(0,0,0,.22);margin-bottom:14px}.row{display:grid;gap:10px}.topbar{grid-template-columns:1fr;align-items:center}.tabs{display:grid;grid-template-columns:1fr 1fr;gap:8px}.filters{display:grid;grid-template-columns:1fr;gap:8px}.actions{display:flex;gap:8px;align-items:center;justify-content:flex-end;flex-wrap:wrap}.input,.select,.textarea{width:100%;border:1px solid rgba(255,255,255,.1);background:#1b1b1b;color:#f4f4f5;border-radius:12px;padding:10px 12px;outline:0}.textarea{min-height:82px;resize:vertical}.input:focus,.select:focus,.textarea:focus{border-color:rgba(234,179,8,.65);box-shadow:0 0 0 3px rgba(234,179,8,.08)}.btn{border-radius:12px;padding:10px 13px;font-weight:800;font-size:13px;transition:.15s}.btn-main{background:#eab308;color:#111}.btn-main:hover{background:#facc15}.btn-dark{background:#242424;color:#fff;border:1px solid rgba(255,255,255,.1)}.btn-green{background:#16a34a;color:#fff}.btn-red{background:#dc2626;color:#fff}.btn-small{padding:8px 10px;font-size:12px}.active{background:#eab308!important;color:#111!important}.headline{display:flex;justify-content:space-between;align-items:end;gap:12px;border-bottom:1px solid rgba(234,179,8,.12);padding-bottom:14px;margin:12px 0 14px}.headline h1{margin:0;font-size:26px;line-height:1.1}.badge{display:inline-flex;border:1px solid rgba(234,179,8,.25);background:rgba(234,179,8,.1);color:#facc15;border-radius:999px;padding:5px 9px;font-size:12px;font-weight:800}.form-grid{display:grid;grid-template-columns:1fr;gap:10px}.form-block{background:#202020;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:12px}.form-title{margin:0 0 10px;color:#a1a1aa;font-size:11px;font-weight:900;text-transform:uppercase}.tablebox{overflow:hidden;border:1px solid rgba(234,179,8,.16);background:#181818;border-radius:18px;box-shadow:0 15px 35px rgba(0,0,0,.25)}.scroll{overflow-x:auto}table{width:100%;min-width:940px;border-collapse:collapse;text-align:left;font-size:14px}th{background:#202020;color:#a1a1aa;text-transform:uppercase;font-size:12px;padding:12px;border-bottom:1px solid rgba(234,179,8,.14)}td{padding:12px;border-bottom:1px solid rgba(255,255,255,.06);vertical-align:middle}tr:hover{background:#202020}.thumb{width:68px;height:52px;border-radius:12px;background:#242424;object-fit:cover}.namecell{display:flex;align-items:center;gap:10px;min-width:250px}.note{max-width:300px;color:#d4d4d8;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.price-main{color:#facc15;font-weight:900}.grid{display:grid;grid-template-columns:1fr;gap:10px}.card{background:#171717;border:1px solid rgba(234,179,8,.16);border-radius:18px;overflow:hidden}.card-img{height:130px;background:#242424;object-fit:cover;width:100%}.card-body{padding:12px}.prices{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;background:#202020;border-radius:14px;padding:7px;margin:10px 0}.price{background:#181818;border-radius:12px;padding:8px;min-width:0}.price strong{display:block;font-size:11px;color:#a1a1aa;text-transform:uppercase}.price span{font-size:12px;font-weight:800;word-break:break-word}.price.highlight{background:#eab308;color:#111}.price.highlight strong{color:rgba(0,0,0,.6)}.hidden{display:none!important}.login{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px}.login-card{width:100%;max-width:420px;background:#171717;border:1px solid rgba(234,179,8,.18);border-radius:20px;padding:26px;box-shadow:0 18px 55px rgba(0,0,0,.38);text-align:center}.login-card img{width:80px;height:80px;border-radius:18px;border:1px solid rgba(234,179,8,.25)}.login-card h1{margin:14px 0 6px}.toast{position:fixed;right:12px;top:12px;z-index:50;max-width:360px;background:#181818;border:1px solid rgba(234,179,8,.3);border-radius:16px;padding:12px 14px;font-weight:800;box-shadow:0 18px 45px rgba(0,0,0,.4)}.toast.error{border-color:rgba(239,68,68,.45);background:#450a0a}.modal{position:fixed;inset:0;background:rgba(0,0,0,.78);z-index:60;display:flex;align-items:center;justify-content:center;padding:14px}.modal-card{width:100%;max-width:860px;max-height:92vh;overflow:auto;background:#181818;border:1px solid rgba(234,179,8,.24);border-radius:18px}.modal-head{display:flex;justify-content:space-between;gap:12px;padding:14px;border-bottom:1px solid rgba(255,255,255,.08)}.modal-body{padding:14px}.gallery{display:grid;grid-template-columns:1fr;gap:8px}.gallery-main{width:100%;max-height:58vh;object-fit:contain;background:#050505;border-radius:14px}.gallery-thumbs{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}.gallery-thumbs img{width:100%;height:74px;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.08)}.mobile-nav{position:fixed;left:0;right:0;bottom:0;display:grid;grid-template-columns:repeat(4,1fr);gap:6px;padding:8px;background:rgba(24,24,24,.96);border-top:1px solid rgba(234,179,8,.2);z-index:40}.muted{color:#a1a1aa}.error-text{color:#fecaca;font-weight:800}.empty{background:#181818;border:1px solid rgba(234,179,8,.2);border-radius:18px;padding:26px;text-align:center}@media(min-width:700px){.wrap{padding:14px 20px}.topbar{grid-template-columns:auto 1fr auto}.tabs{display:flex}.filters{grid-template-columns:repeat(4,1fr)}.form-grid{grid-template-columns:1fr 1fr}.grid{grid-template-columns:repeat(2,1fr)}.mobile-nav{display:none}}@media(min-width:1100px){.grid{grid-template-columns:repeat(4,1fr)}}@media(min-width:1400px){.grid{grid-template-columns:repeat(5,1fr)}}
  </style>
</head>
<body>
  <div id="loginView" class="login hidden">
    <div class="login-card">
      <img src="/icon-192.png" alt="Maszyny Gliznowo">
      <h1>Maszyny Gliznowo</h1>
      <p class="muted">Wewnętrzny system magazynowy</p>
      <div class="row" style="margin-top:18px">
        <input id="loginEmail" class="input" type="email" autocomplete="username" placeholder="Email">
        <div style="position:relative">
          <input id="loginPassword" class="input" type="password" autocomplete="current-password" placeholder="Hasło" style="padding-right:86px">
          <button id="togglePassword" class="btn btn-dark btn-small" style="position:absolute;right:6px;top:5px" type="button">POKAŻ</button>
        </div>
        <p id="loginError" class="error-text"></p>
        <button id="loginBtn" class="btn btn-main">ZALOGUJ</button>
      </div>
    </div>
  </div>

  <main id="appView" class="wrap hidden">
    <div class="banner"><img src="/banner.png" alt="Maszyny Gliznowo"></div>
    <section class="panel row topbar">
      <div class="tabs">
        <button id="activeTab" class="btn btn-dark active">MAGAZYN</button>
        <button id="archiveTab" class="btn btn-dark">ARCHIWUM</button>
      </div>
      <div class="filters">
        <input id="search" class="input" placeholder="Szukaj: nazwa lub indeks">
        <input id="searchPrice" class="input" placeholder="Szukaj po cenie">
        <select id="sortMode" class="select">
          <option value="newest">Sortuj: najnowsze</option>
          <option value="name">Sortuj: nazwa</option>
          <option value="index">Sortuj: indeks</option>
          <option value="price-desc">Cena malejąco</option>
          <option value="price-asc">Cena rosnąco</option>
        </select>
        <select id="qualityFilter" class="select">
          <option value="all">Filtr: wszystko</option>
          <option value="no-image">Bez zdjęcia</option>
          <option value="no-price">Bez cen</option>
          <option value="no-note">Bez notatki</option>
        </select>
      </div>
      <div class="actions">
        <button id="cardMode" class="btn btn-dark btn-small">Kafelki</button>
        <button id="tableMode" class="btn btn-main btn-small">Tabela</button>
        <button id="logoutBtn" class="btn btn-red">WYLOGUJ</button>
      </div>
    </section>

    <div class="headline">
      <div><h1 id="headline">Maszyny w magazynie</h1><span id="count" class="badge">Ilość: 0</span></div>
      <button id="toggleForm" class="btn btn-main">+ DODAJ MASZYNĘ</button>
    </div>

    <section id="formPanel" class="panel hidden">
      <div class="form-grid">
        <div class="form-block">
          <p class="form-title">Dane maszyny</p>
          <div class="row">
            <input id="name" class="input" placeholder="Nazwa">
            <input id="index_number" class="input" placeholder="Numer indeksu">
            <div class="row" style="grid-template-columns:repeat(3,1fr)">
              <input id="purchase_price" class="input" placeholder="Cena zakupu">
              <input id="vat_price" class="input" placeholder="VAT">
              <input id="gross_price" class="input" placeholder="Cena">
            </div>
            <input id="images" class="input" type="file" accept="image/*" multiple>
          </div>
        </div>
        <div class="form-block">
          <p class="form-title">Opis i notatka</p>
          <textarea id="description" class="textarea" placeholder="Opis"></textarea>
          <textarea id="note" class="textarea" placeholder="Notatka"></textarea>
        </div>
      </div>
      <button id="saveNew" class="btn btn-green" style="width:100%;margin-top:10px">ZAPISZ MASZYNĘ</button>
    </section>

    <div id="tableBox" class="tablebox"><div class="scroll"><table><thead><tr><th>Maszyna</th><th>Indeks</th><th>Cena zakupu</th><th>VAT</th><th>Cena</th><th>Notatka</th><th style="text-align:right">Akcje</th></tr></thead><tbody id="tbody"></tbody></table></div></div>
    <div id="cards" class="grid hidden"></div>
    <div id="empty" class="empty hidden"><h2>Brak maszyn</h2><p class="muted">Zmień filtry albo dodaj nową maszynę.</p></div>

    <nav class="mobile-nav">
      <button id="mobileActive" class="btn btn-main btn-small">Magazyn</button>
      <button id="mobileAdd" class="btn btn-main btn-small">Dodaj</button>
      <button id="mobileArchive" class="btn btn-dark btn-small">Archiwum</button>
      <button id="mobileTop" class="btn btn-dark btn-small">Szukaj</button>
    </nav>
  </main>

  <div id="modal" class="modal hidden"></div>
  <div id="toast" class="toast hidden"></div>

  <script>
    const state = { machines: [], view: 'available', mode: 'table', editingId: null }
    const $ = (id) => document.getElementById(id)
    const price = (v) => v ? `${v} zł` : '-'
    const text = (v) => String(v ?? '')
    const api = (action, options = {}) => fetch(`api.php?action=${action}`, options)

    function toast(message, type = 'ok') {
      const el = $('toast')
      el.textContent = message
      el.className = `toast ${type === 'error' ? 'error' : ''}`
      setTimeout(() => el.classList.add('hidden'), 3200)
    }

    async function init() {
      try {
        const res = await api('me')
        const data = await res.json()
        if (data.authenticated) showApp()
        else showLogin()
      } catch {
        showLogin()
      }
    }

    function showLogin() {
      $('loginView').classList.remove('hidden')
      $('appView').classList.add('hidden')
    }

    function showApp() {
      $('loginView').classList.add('hidden')
      $('appView').classList.remove('hidden')
      loadMachines()
    }

    async function login() {
      $('loginError').textContent = ''
      const res = await api('login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: $('loginEmail').value, password: $('loginPassword').value }),
      })
      if (!res.ok) {
        $('loginError').textContent = 'Nieprawidłowy email lub hasło'
        return
      }
      showApp()
    }

    async function logout() {
      await api('logout', { method: 'POST' })
      showLogin()
    }

    async function loadMachines() {
      const res = await api(`machines&status=${state.view}`)
      if (res.status === 401) return showLogin()
      const data = await res.json()
      state.machines = data.machines || []
      render()
    }

    function filtered() {
      const q = $('search').value.toLowerCase().trim()
      const qp = $('searchPrice').value.toLowerCase().trim()
      const quality = $('qualityFilter').value
      const rows = state.machines.filter((m) => {
        const matchesText = !q || text(m.name).toLowerCase().includes(q) || text(m.index_number).toLowerCase().includes(q)
        const matchesPrice = !qp || text(m.purchase_price).toLowerCase().includes(qp) || text(m.vat_price).toLowerCase().includes(qp) || text(m.gross_price).toLowerCase().includes(qp)
        const matchesQuality = quality === 'all' || (quality === 'no-image' && !m.image1) || (quality === 'no-price' && !m.gross_price && !m.vat_price && !m.purchase_price) || (quality === 'no-note' && !m.note)
        return matchesText && matchesPrice && matchesQuality
      })
      const mode = $('sortMode').value
      const num = (v) => Number(text(v).replace(',', '.').replace(/[^0-9.-]/g, '')) || 0
      rows.sort((a,b) => {
        if (mode === 'name') return text(a.name).localeCompare(text(b.name), 'pl')
        if (mode === 'index') return text(a.index_number).localeCompare(text(b.index_number), 'pl', { numeric: true })
        if (mode === 'price-desc') return num(b.gross_price) - num(a.gross_price)
        if (mode === 'price-asc') return num(a.gross_price) - num(b.gross_price)
        return Number(b.id) - Number(a.id)
      })
      return rows
    }

    function render() {
      $('headline').textContent = state.view === 'available' ? 'Maszyny w magazynie' : 'Archiwum'
      $('activeTab').classList.toggle('active', state.view === 'available')
      $('archiveTab').classList.toggle('active', state.view === 'sold')
      $('mobileActive').classList.toggle('active', state.view === 'available')
      $('mobileArchive').classList.toggle('active', state.view === 'sold')
      $('tableBox').classList.toggle('hidden', state.mode !== 'table')
      $('cards').classList.toggle('hidden', state.mode !== 'cards')
      $('tableMode').classList.toggle('btn-main', state.mode === 'table')
      $('cardMode').classList.toggle('btn-main', state.mode === 'cards')
      const rows = filtered()
      $('count').textContent = `Ilość: ${rows.length}`
      $('empty').classList.toggle('hidden', rows.length > 0)
      renderTable(rows)
      renderCards(rows)
    }

    function renderTable(rows) {
      $('tbody').innerHTML = rows.map((m) => `<tr>
        <td><div class="namecell">${m.image1 ? `<img class="thumb" src="${m.image1}" alt="">` : '<div class="thumb"></div>'}<strong>${escapeHtml(m.name || 'Bez nazwy')}</strong></div></td>
        <td><span class="badge">#${escapeHtml(m.index_number || 'brak')}</span></td>
        <td>${escapeHtml(price(m.purchase_price))}</td><td>${escapeHtml(price(m.vat_price))}</td><td class="price-main">${escapeHtml(price(m.gross_price))}</td>
        <td><span class="note">${escapeHtml(m.note || 'Brak notatki')}</span></td>
        <td style="text-align:right">${actions(m)}</td>
      </tr>`).join('')
    }

    function renderCards(rows) {
      $('cards').innerHTML = rows.map((m) => `<article class="card">
        ${m.image1 ? `<img class="card-img" src="${m.image1}" alt="${escapeHtml(m.name)}">` : '<div class="card-img"></div>'}
        <div class="card-body"><span class="badge">#${escapeHtml(m.index_number || 'brak')}</span><h3>${escapeHtml(m.name || 'Bez nazwy')}</h3>
        <div class="prices"><div class="price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="price highlight"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
        <p class="note">${escapeHtml(m.note || 'Brak notatki')}</p><div class="actions">${actions(m)}</div></div>
      </article>`).join('')
    }

    function actions(m) {
      return `<button class="btn btn-dark btn-small" onclick="openDetails(${m.id})">PODGLĄD</button>
        <button class="btn btn-dark btn-small" onclick="openEdit(${m.id})">EDYTUJ</button>
        ${state.view === 'available'
          ? `<button class="btn btn-main btn-small" onclick="setStatus(${m.id}, 'sold')">ARCHIWIZUJ</button>`
          : `<button class="btn btn-green btn-small" onclick="setStatus(${m.id}, 'available')">PRZYWRÓĆ</button><button class="btn btn-red btn-small" onclick="deleteMachine(${m.id})">USUŃ</button>`}`
    }

    async function createMachine() {
      const form = new FormData()
      for (const id of ['name','index_number','purchase_price','vat_price','gross_price','description','note']) form.append(id, $(id).value)
      Array.from($('images').files || []).slice(0, 4).forEach((file, i) => form.append(`image${i + 1}`, file))
      const res = await api('create', { method: 'POST', body: form })
      const data = await res.json()
      if (!res.ok) return toast(data.error || 'Nie udało się zapisać.', 'error')
      for (const id of ['name','index_number','purchase_price','vat_price','gross_price','description','note']) $(id).value = ''
      $('images').value = ''
      $('formPanel').classList.add('hidden')
      toast('Maszyna dodana.')
      loadMachines()
    }

    async function setStatus(id, status) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      const form = formFromMachine(m)
      form.set('status', status)
      form.set('history_action', status === 'sold' ? 'Archiwizacja' : 'Przywrócenie')
      form.set('history_details', status === 'sold' ? 'Maszyna przeniesiona do archiwum.' : 'Maszyna wróciła do magazynu.')
      const res = await api('update', { method: 'POST', body: form })
      if (!res.ok) return toast('Nie udało się zmienić statusu.', 'error')
      toast('Zmieniono status.')
      loadMachines()
    }

    async function deleteMachine(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!confirm(`Usunąć na stałe: ${m?.name || 'maszyna'}?`)) return
      const res = await api('delete', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) })
      if (!res.ok) return toast('Nie udało się usunąć.', 'error')
      toast('Usunięto maszynę.')
      loadMachines()
    }

    function formFromMachine(m) {
      const form = new FormData()
      for (const key of ['id','name','index_number','purchase_price','vat_price','gross_price','description','note','status']) form.append(key, m?.[key] || '')
      return form
    }

    function openDetails(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      const imgs = [m.image1,m.image2,m.image3,m.image4].filter(Boolean)
      $('modal').innerHTML = `<div class="modal-card"><div class="modal-head"><strong>${escapeHtml(m.name || 'Bez nazwy')}</strong><button class="btn btn-dark btn-small" onclick="closeModal()">X</button></div><div class="modal-body">
        <div class="gallery">${imgs[0] ? `<img id="mainPhoto" class="gallery-main" src="${imgs[0]}" alt="">` : '<div class="empty">Brak zdjęć</div>'}<div class="gallery-thumbs">${imgs.map((img) => `<img src="${img}" onclick="$('mainPhoto').src='${img}'" alt="">`).join('')}</div></div>
        <div class="prices"><div class="price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="price highlight"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
        <p>${escapeHtml(m.description || '')}</p><p class="muted">${escapeHtml(m.note || '')}</p>
      </div></div>`
      $('modal').classList.remove('hidden')
    }

    function openEdit(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      $('modal').innerHTML = `<div class="modal-card"><div class="modal-head"><strong>Edycja</strong><button class="btn btn-dark btn-small" onclick="closeModal()">X</button></div><div class="modal-body row">
        <input id="edit_name" class="input" value="${escapeAttr(m.name)}" placeholder="Nazwa"><input id="edit_index" class="input" value="${escapeAttr(m.index_number)}" placeholder="Indeks">
        <div class="row" style="grid-template-columns:repeat(3,1fr)"><input id="edit_purchase" class="input" value="${escapeAttr(m.purchase_price)}" placeholder="Cena zakupu"><input id="edit_vat" class="input" value="${escapeAttr(m.vat_price)}" placeholder="VAT"><input id="edit_gross" class="input" value="${escapeAttr(m.gross_price)}" placeholder="Cena"></div>
        <textarea id="edit_description" class="textarea" placeholder="Opis">${escapeHtml(m.description)}</textarea><textarea id="edit_note" class="textarea" placeholder="Notatka">${escapeHtml(m.note)}</textarea>
        <p class="muted">Podmień konkretne zdjęcie:</p><div class="row" style="grid-template-columns:repeat(4,1fr)">${[1,2,3,4].map((i) => `<input id="edit_image${i}" class="input" type="file" accept="image/*">`).join('')}</div>
        <button class="btn btn-green" onclick="saveEdit(${m.id})">ZAPISZ</button>
      </div></div>`
      $('modal').classList.remove('hidden')
    }

    async function saveEdit(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      const form = formFromMachine(m)
      form.set('name', $('edit_name').value); form.set('index_number', $('edit_index').value); form.set('purchase_price', $('edit_purchase').value); form.set('vat_price', $('edit_vat').value); form.set('gross_price', $('edit_gross').value); form.set('description', $('edit_description').value); form.set('note', $('edit_note').value)
      for (const i of [1,2,3,4]) if ($(`edit_image${i}`).files[0]) form.append(`image${i}`, $(`edit_image${i}`).files[0])
      form.set('history_action', 'Edycja'); form.set('history_details', 'Zaktualizowano dane maszyny.')
      const res = await api('update', { method: 'POST', body: form })
      const data = await res.json()
      if (!res.ok) return toast(data.error || 'Nie udało się zapisać.', 'error')
      closeModal(); toast('Zapisano.'); loadMachines()
    }

    function closeModal(){ $('modal').classList.add('hidden'); $('modal').innerHTML = '' }
    function escapeHtml(v){ return text(v).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])) }
    function escapeAttr(v){ return escapeHtml(v).replace(/"/g, '&quot;') }

    $('loginBtn').onclick = login
    $('togglePassword').onclick = () => { const p = $('loginPassword'); p.type = p.type === 'password' ? 'text' : 'password'; $('togglePassword').textContent = p.type === 'password' ? 'POKAŻ' : 'UKRYJ' }
    $('logoutBtn').onclick = logout
    $('activeTab').onclick = () => { state.view = 'available'; loadMachines() }
    $('archiveTab').onclick = () => { state.view = 'sold'; loadMachines() }
    $('mobileActive').onclick = $('activeTab').onclick
    $('mobileArchive').onclick = $('archiveTab').onclick
    $('mobileAdd').onclick = () => $('formPanel').classList.toggle('hidden')
    $('mobileTop').onclick = () => scrollTo({ top: 0, behavior: 'smooth' })
    $('toggleForm').onclick = () => $('formPanel').classList.toggle('hidden')
    $('saveNew').onclick = createMachine
    $('tableMode').onclick = () => { state.mode = 'table'; render() }
    $('cardMode').onclick = () => { state.mode = 'cards'; render() }
    for (const id of ['search','searchPrice','sortMode','qualityFilter']) $(id).oninput = render
    init()
  </script>
</body>
</html>

