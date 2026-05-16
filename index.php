<?php
declare(strict_types=1);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'");
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
  <link rel="stylesheet" href="/style.css?v=20260516-20">
</head>
<body style="background:#0f0f0f;color:#fafafa;margin:0">
  <div id="loginView" class="login hidden">
    <div class="login-card">
      <img src="/icon-192.png" alt="Maszyny Gliznowo">
      <h1>Maszyny Gliznowo</h1>
      <p class="muted">Wewnętrzny system magazynowy</p>
      <div class="row" style="margin-top:18px">
        <input id="loginEmail" class="input" type="email" autocomplete="username" inputmode="email" autocapitalize="none" autocorrect="off" spellcheck="false" placeholder="Email">
        <div style="position:relative">
          <input id="loginPassword" class="input" type="password" autocomplete="current-password" autocapitalize="none" autocorrect="off" spellcheck="false" placeholder="Hasło" style="padding-right:86px">
          <button id="togglePassword" class="btn btn-dark btn-small" style="position:absolute;right:6px;top:5px" type="button">POKAŻ</button>
        </div>
        <p id="loginError" class="error-text"></p>
        <button id="loginBtn" class="btn btn-main">ZALOGUJ</button>
      </div>
    </div>
  </div>

  <main id="appView" class="wrap hidden">
    <div class="banner"><img src="/banner.png?v=20260516-19" alt="Maszyny Gliznowo"></div>
    <section class="panel row topbar">
      <div class="tabs">
        <button id="activeTab" class="btn btn-dark active">MAGAZYN</button>
        <button id="archiveTab" class="btn btn-dark">ARCHIWUM</button>
      </div>
      <div class="actions">
        <button id="logoutBtn" class="btn btn-red">WYLOGUJ</button>
      </div>
    </section>

    <div class="headline">
      <div class="headline-title"><h1 id="headline">Maszyny w magazynie</h1><span id="count" class="badge">Ilość: 0</span></div>
      <div class="headline-tools">
        <button id="toggleForm" class="btn btn-main">+ DODAJ MASZYNĘ</button>
        <div class="searchbar"><input id="search" class="input" type="search" autocomplete="off" placeholder="Szukaj po nazwie lub indeksie"></div>
      </div>
    </div>

    <section class="stats hidden" aria-hidden="true">
      <div class="stat"><strong id="statVisible">0</strong><span>Widoczne</span></div>
      <div class="stat"><strong id="statAll">0</strong><span>W aktualnym widoku</span></div>
      <div class="stat"><strong id="statNoImage">0</strong><span>Bez zdjęcia</span></div>
      <div class="stat"><strong id="statNoNote">0</strong><span>Bez notatki</span></div>
    </section>

    <section id="formPanel" class="panel hidden">
      <div class="form-grid">
        <div class="form-block">
          <p class="form-title">Dane maszyny</p>
          <div class="row">
            <input id="name" class="input" placeholder="Nazwa">
            <input id="index_number" class="input" placeholder="Numer indeksu">
            <div class="row price-row">
              <input id="purchase_price" class="input" placeholder="Cena zakupu">
              <input id="vat_price" class="input" placeholder="VAT">
              <input id="gross_price" class="input" placeholder="Cena">
            </div>
            <label class="upload-box" for="images"><strong>Dodaj zdjęcia</strong><span>Maksymalnie 4 zdjęcia. Zostaną automatycznie zmniejszone.</span></label>
            <input id="images" class="input file-input" type="file" accept="image/*" multiple>
            <div id="filePreview" class="file-preview hidden"></div>
          </div>
        </div>
        <div class="form-block create-text-block">
          <p class="form-title">Opis i notatka</p>
          <textarea id="description" class="textarea" placeholder="Opis"></textarea>
          <textarea id="note" class="textarea" placeholder="Notatka"></textarea>
        </div>
      </div>
      <button id="saveNew" class="btn btn-green" style="width:100%;margin-top:10px">ZAPISZ MASZYNĘ</button>
    </section>

    <div id="tableBox" class="tablebox"><div class="scroll"><table><thead><tr><th><button class="th-button" onclick="setSort('name')">Maszyna <span id="sortName"></span></button></th><th><button class="th-button" onclick="setSort('index')">Indeks <span id="sortIndex"></span></button></th><th>Cena zakupu</th><th>VAT</th><th><button class="th-button" onclick="setSort('price')">Cena <span id="sortPrice"></span></button></th><th>Notatka</th><th style="text-align:right">Akcje</th></tr></thead><tbody id="tbody"></tbody></table></div></div>
    <div id="cards" class="grid hidden"></div>
    <div id="empty" class="empty hidden"><h2>Brak maszyn</h2><p class="muted">Dodaj nową maszynę albo przełącz widok magazynu.</p></div>

    <nav class="mobile-nav">
      <button id="mobileActive" class="btn btn-main btn-small">Magazyn</button>
      <button id="mobileAdd" class="btn btn-main btn-small">Dodaj</button>
      <button id="mobileArchive" class="btn btn-dark btn-small">Archiwum</button>
    </nav>
  </main>

  <div id="modal" class="modal hidden"></div>
  <div id="toast" class="toast hidden"></div>
  <div id="loading" class="loading hidden"><div class="loading-card">Pracuję...</div></div>

  <script>
    const state = { machines: [], view: 'available', mode: 'table', sortMode: 'newest', search: '', editingId: null, edit: {}, lightboxImages: [], lightboxIndex: 0, lightboxMachineId: null, csrf: '' }
    const selectedCreateImages = []
    const preferredMode = () => window.matchMedia('(max-width: 699px)').matches ? 'cards' : 'table'
    const $ = (id) => document.getElementById(id)
    const price = (v) => v ? `${v} zł` : '-'
    const text = (v) => String(v ?? '')
    function api(action, options = {}) {
      const request = { ...options }
      const method = (request.method || 'GET').toUpperCase()
      const headers = new Headers(request.headers || {})
      if (method !== 'GET' && state.csrf) headers.set('X-CSRF-Token', state.csrf)
      request.headers = headers
      return fetch(`api.php?action=${action}`, request)
    }

    function toast(message, type = 'ok') {
      const el = $('toast')
      el.textContent = message
      el.className = `toast ${type === 'error' ? 'error' : ''}`
      setTimeout(() => el.classList.add('hidden'), 3200)
    }

    function setLoading(value, label = 'Pracuję...') {
      $('loading').querySelector('.loading-card').textContent = label
      $('loading').classList.toggle('hidden', !value)
    }

    async function init() {
      try {
        const res = await api('me')
        const data = await res.json()
        if (data.authenticated) { state.csrf = data.csrf || ''; showApp() }
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
        body: JSON.stringify({ email: $('loginEmail').value.trim(), password: $('loginPassword').value }),
      })
      if (!res.ok) {
        $('loginError').textContent = 'Nieprawidłowy email lub hasło'
        return
      }
      const data = await res.json()
      state.csrf = data.csrf || ''
      showApp()
    }

    async function logout() {
      await api('logout', { method: 'POST' })
      state.csrf = ''
      showLogin()
    }

    async function loadMachines() {
      setLoading(true, 'Pobieram maszyny...')
      try {
        const res = await api(`machines&status=${state.view}`)
        if (res.status === 401) return showLogin()
        const data = await res.json()
        state.machines = data.machines || []
        render()
      } catch {
        toast('Nie udało się pobrać maszyn.', 'error')
      } finally {
        setLoading(false)
      }
    }

    function filtered() {
      const query = state.search.toLowerCase().trim()
      const rows = state.machines.filter((m) => !query || text(m.name).toLowerCase().includes(query) || text(m.index_number).toLowerCase().includes(query))
      const mode = state.sortMode
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

    function setSort(type) {
      if (type === 'name') state.sortMode = state.sortMode === 'name' ? 'newest' : 'name'
      if (type === 'index') state.sortMode = state.sortMode === 'index' ? 'newest' : 'index'
      if (type === 'price') state.sortMode = state.sortMode === 'price-desc' ? 'price-asc' : 'price-desc'
      render()
    }

    function updateSortMarkers() {
      const mode = state.sortMode
      if ($('sortName')) $('sortName').textContent = mode === 'name' ? '↑' : ''
      if ($('sortIndex')) $('sortIndex').textContent = mode === 'index' ? '↑' : ''
      if ($('sortPrice')) $('sortPrice').textContent = mode === 'price-desc' ? '↓' : mode === 'price-asc' ? '↑' : ''
    }

    function startQuickEdit(machine) {
      state.editingId = Number(machine.id)
      state.edit = {
        name: text(machine.name),
        index_number: text(machine.index_number),
        purchase_price: text(machine.purchase_price),
        vat_price: text(machine.vat_price),
        gross_price: text(machine.gross_price),
        note: text(machine.note),
      }
      render()
    }

    function setEditField(field, value) {
      state.edit[field] = value
    }

    function cancelQuickEdit() {
      state.editingId = null
      state.edit = {}
      render()
    }

    async function saveQuickEdit(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!m) return
      setLoading(true, 'Zapisuję zmiany...')
      const form = formFromMachine(m)
      for (const key of ['name','index_number','purchase_price','vat_price','gross_price','note']) form.set(key, state.edit[key] || '')
      form.set('history_action', 'Szybka edycja')
      form.set('history_details', 'Zaktualizowano dane maszyny w tabeli.')
      const res = await api('update', { method: 'POST', body: form })
      if (!res.ok) { setLoading(false); return toast('Nie udało się zapisać szybkiej edycji.', 'error') }
      state.editingId = null
      state.edit = {}
      toast('Zapisano zmiany.')
      await loadMachines()
      setLoading(false)
    }

    function render() {
      $('headline').textContent = state.view === 'available' ? 'Maszyny w magazynie' : 'Archiwum'
      $('activeTab').classList.toggle('active', state.view === 'available')
      $('archiveTab').classList.toggle('active', state.view === 'sold')
      $('mobileActive').classList.toggle('active', state.view === 'available')
      $('mobileArchive').classList.toggle('active', state.view === 'sold')
      state.mode = preferredMode()
      $('tableBox').classList.toggle('hidden', state.mode !== 'table')
      $('cards').classList.toggle('hidden', state.mode !== 'cards')
      updateSortMarkers()
      const rows = filtered()
      $('count').textContent = `Ilość: ${rows.length}`
      $('statVisible').textContent = rows.length
      $('statAll').textContent = state.machines.length
      $('statNoImage').textContent = state.machines.filter((m) => !m.image1).length
      $('statNoNote').textContent = state.machines.filter((m) => !m.note).length
      $('empty').classList.toggle('hidden', rows.length > 0)
      $('empty').querySelector('.muted').textContent = state.search.trim() ? 'Nie znaleziono maszyny o takiej nazwie lub numerze indeksu.' : 'Dodaj nową maszynę albo przełącz widok magazynu.'
      renderTable(rows)
      renderCards(rows)
    }

    function renderTable(rows) {
      $('tbody').innerHTML = rows.map((m) => {
        const editing = state.editingId === Number(m.id)
        if (editing) {
          return `<tr class="editing-row">
            <td><div class="namecell">${m.image1 ? `<img class="thumb" src="${m.image1}" alt="">` : '<div class="thumb"></div>'}<input class="input quick-input" value="${escapeAttr(state.edit.name)}" onclick="event.stopPropagation()" oninput="setEditField('name', this.value)" placeholder="Nazwa"></div></td>
            <td><input class="input quick-input" value="${escapeAttr(state.edit.index_number)}" onclick="event.stopPropagation()" oninput="setEditField('index_number', this.value)" placeholder="Indeks"></td>
            <td><input class="input quick-input" value="${escapeAttr(state.edit.purchase_price)}" onclick="event.stopPropagation()" oninput="setEditField('purchase_price', this.value)" placeholder="Cena zakupu"></td>
            <td><input class="input quick-input" value="${escapeAttr(state.edit.vat_price)}" onclick="event.stopPropagation()" oninput="setEditField('vat_price', this.value)" placeholder="VAT"></td>
            <td><input class="input quick-input" value="${escapeAttr(state.edit.gross_price)}" onclick="event.stopPropagation()" oninput="setEditField('gross_price', this.value)" placeholder="Cena"></td>
            <td><textarea class="input quick-note" onclick="event.stopPropagation()" oninput="setEditField('note', this.value)" placeholder="Notatka">${escapeHtml(state.edit.note)}</textarea></td>
            <td><div class="row-actions"><button class="btn btn-green btn-small" onclick="event.stopPropagation(); saveQuickEdit(${m.id})">ZAPISZ</button><button class="btn btn-dark btn-small" onclick="event.stopPropagation(); cancelQuickEdit()">ANULUJ</button></div></td>
          </tr>`
        }
        return `<tr class="clickable-row" onclick="openDetails(${m.id})">
          <td><div class="namecell">${m.image1 ? `<img class="thumb" src="${m.image1}" alt="">` : '<div class="thumb"></div>'}<strong>${escapeHtml(m.name || 'Bez nazwy')}</strong></div></td>
          <td><span class="badge">#${escapeHtml(m.index_number || 'brak')}</span></td>
          <td>${escapeHtml(price(m.purchase_price))}</td><td>${escapeHtml(price(m.vat_price))}</td><td class="price-main">${escapeHtml(price(m.gross_price))}</td>
          <td><span class="note">${escapeHtml(m.note || 'Brak notatki')}</span></td>
          <td style="text-align:right">${actions(m)}</td>
        </tr>`
      }).join('')
    }

    function renderCards(rows) {
      $('cards').innerHTML = rows.map((m) => {
        const editing = state.editingId === Number(m.id)
        if (editing) {
          return `<article class="card quick-card" onclick="event.stopPropagation()">
            ${m.image1 ? `<img class="card-img" src="${m.image1}" alt="${escapeHtml(m.name)}">` : '<div class="card-img"></div>'}
            <div class="card-body quick-card-body">
              <div class="quick-card-grid">
                <input class="input" value="${escapeAttr(state.edit.name)}" oninput="setEditField('name', this.value)" placeholder="Nazwa">
                <input class="input" value="${escapeAttr(state.edit.index_number)}" oninput="setEditField('index_number', this.value)" placeholder="Indeks">
                <input class="input" value="${escapeAttr(state.edit.purchase_price)}" oninput="setEditField('purchase_price', this.value)" placeholder="Cena zakupu">
                <input class="input" value="${escapeAttr(state.edit.vat_price)}" oninput="setEditField('vat_price', this.value)" placeholder="VAT">
                <input class="input" value="${escapeAttr(state.edit.gross_price)}" oninput="setEditField('gross_price', this.value)" placeholder="Cena">
                <textarea class="textarea quick-card-note" oninput="setEditField('note', this.value)" placeholder="Notatka">${escapeHtml(state.edit.note)}</textarea>
              </div>
              <div class="quick-card-actions"><button class="btn btn-green" onclick="saveQuickEdit(${m.id})">ZAPISZ</button><button class="btn btn-dark" onclick="cancelQuickEdit()">ANULUJ</button></div>
            </div>
          </article>`
        }
        return `<article class="card clickable-card" onclick="openDetails(${m.id})">
          ${m.image1 ? `<img class="card-img" src="${m.image1}" alt="${escapeHtml(m.name)}">` : '<div class="card-img"></div>'}
          <div class="card-body"><span class="badge">#${escapeHtml(m.index_number || 'brak')}</span><h3>${escapeHtml(m.name || 'Bez nazwy')}</h3>
          <div class="prices"><div class="price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="price highlight"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
          <p class="note">${escapeHtml(m.note || 'Brak notatki')}</p><div class="actions">${actions(m)}</div></div>
        </article>`
      }).join('')
    }

    function actions(m) {
      return `<button class="btn btn-dark btn-small" onclick="event.stopPropagation(); startQuickEdit(mById(${m.id}))">EDYTUJ</button>
        ${state.view === 'available'
          ? `<button class="btn btn-main btn-small" onclick="event.stopPropagation(); setStatus(${m.id}, 'sold')">ARCHIWIZUJ</button>`
          : `<button class="btn btn-green btn-small" onclick="event.stopPropagation(); setStatus(${m.id}, 'available')">PRZYWRÓĆ</button><button class="btn btn-red btn-small" onclick="event.stopPropagation(); deleteMachine(${m.id})">USUŃ</button>`}
      `
    }

    function mById(id) {
      return state.machines.find((machine) => Number(machine.id) === Number(id))
    }

    async function createMachine() {
      if (!$('name').value.trim()) return toast('Wpisz nazwę maszyny.', 'error')
      setLoading(true, 'Zapisuję maszynę...')
      try {
        const form = new FormData()
        for (const id of ['name','index_number','purchase_price','vat_price','gross_price','description','note']) form.append(id, $(id).value)
        await appendCompressedImages(form, selectedCreateImages, 'image')
        const res = await api('create', { method: 'POST', body: form })
        const data = await res.json()
        if (!res.ok) return toast(data.error || 'Nie udało się zapisać.', 'error')
        for (const id of ['name','index_number','purchase_price','vat_price','gross_price','description','note']) $(id).value = ''
        $('images').value = ''
        selectedCreateImages.length = 0
        renderCreateImagePreview()
        $('formPanel').classList.add('hidden')
        toast('Maszyna dodana.')
        await loadMachines()
      } catch {
        toast('Nie udało się zapisać maszyny.', 'error')
      } finally {
        setLoading(false)
      }
    }

    async function setStatus(id, status) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!m) return
      setLoading(true, 'Zmieniam status...')
      const form = formFromMachine(m)
      form.set('status', status)
      form.set('history_action', status === 'sold' ? 'Archiwizacja' : 'Przywrócenie')
      form.set('history_details', status === 'sold' ? 'Maszyna przeniesiona do archiwum.' : 'Maszyna wróciła do magazynu.')
      const res = await api('update', { method: 'POST', body: form })
      if (!res.ok) { setLoading(false); return toast('Nie udało się zmienić statusu.', 'error') }
      toast('Zmieniono status.')
      await loadMachines()
      setLoading(false)
    }

    async function deleteMachine(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!confirm(`Usunąć na stałe: ${m?.name || 'maszyna'}?`)) return
      setLoading(true, 'Usuwam maszynę...')
      const res = await api('delete', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) })
      if (!res.ok) { setLoading(false); return toast('Nie udało się usunąć.', 'error') }
      toast('Usunięto maszynę.')
      await loadMachines()
      setLoading(false)
    }

    function formFromMachine(m) {
      const form = new FormData()
      for (const key of ['id','name','index_number','purchase_price','vat_price','gross_price','description','note','status']) form.append(key, m?.[key] || '')
      return form
    }

    function openDetails(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!m) return
      const imgs = [m.image1,m.image2,m.image3,m.image4].filter(Boolean)
      const main = imgs[0] || ''
      $('modal').innerHTML = `<div class="product-modal"><div class="product-wrap">
        <div class="product-banner"><img src="/banner.png?v=20260516-19" alt="Maszyny Gliznowo"></div>
        <div class="product-toolbar">
          <div class="product-toolbar-left">
            <button class="btn btn-main" onclick="closeModal()">WRÓĆ</button>
          </div>
          <div class="product-meta">${imgs.length} zdjęć / ID ${m.id}</div>
        </div>
        <div class="product-grid product-grid-view">
          <section class="product-gallery">
            <div class="product-main-photo">
              ${main ? `<img id="mainPhoto" src="${main}" onclick="openLightbox(${id}, 0)" alt="${escapeAttr(m.name || 'Maszyna')}">` : '<div class="product-no-photo">Brak zdjęcia</div>'}
              <div class="photo-count">${imgs.length ? '1 / ' + imgs.length : '0 / 4'}</div>
            </div>
            ${imgs.length ? `<div class="product-thumbs">${imgs.map((img, index) => `<img class="${index === 0 ? 'active' : ''}" src="${img}" onclick="selectProductPhoto(this, '${img}', ${id}, ${index}, ${imgs.length})" alt="Zdjęcie ${index + 1}">`).join('')}</div>` : ''}
          </section>
          <section class="product-panel product-summary-panel">
            <div class="product-head">
              <div class="product-badges"><span class="badge">#${escapeHtml(m.index_number || 'brak indeksu')}</span><button class="status-pill status-action ${m.status === 'sold' ? 'archive' : ''}" onclick="setStatus(${m.id}, '${m.status === 'sold' ? 'available' : 'sold'}'); closeModal()"><span class="status-current">${m.status === 'sold' ? 'Archiwum' : 'W magazynie'}</span><span class="status-hover">${m.status === 'sold' ? 'Przywróć do magazynu' : 'Przenieś do archiwum'}</span></button></div>
              <h1 class="product-title">${escapeHtml(m.name || 'Bez nazwy')}</h1>
            </div>
            <div class="product-section">
              <div class="product-prices"><div class="product-price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="product-price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="product-price main"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
            </div>
            <div class="product-section"><div class="product-actions">${m.status === 'sold' ? `<button class="btn btn-green" onclick="setStatus(${m.id}, 'available'); closeModal()">PRZYWRÓĆ DO MAGAZYNU</button>` : `<button class="btn btn-main" onclick="setStatus(${m.id}, 'sold'); closeModal()">PRZENIEŚ DO ARCHIWUM</button>`}<button class="btn btn-dark" onclick="openEdit(${m.id})">EDYTUJ</button><button class="btn btn-dark" onclick="toggleHistory(${id})">Historia zmian</button><div id="historyList" class="history-list hidden"></div></div></div>
          </section>
        </div>
        <section class="product-text-panel">
          <div class="product-section"><h2 class="section-title">Opis</h2><p class="product-copy">${escapeHtml(m.description || 'Brak opisu')}</p></div>
          <div class="product-section"><h2 class="section-title">Notatka</h2><p class="product-copy">${escapeHtml(m.note || 'Brak notatki')}</p></div>
        </section>
      </div></div>`
      $('modal').classList.remove('hidden')
    }

    async function toggleHistory(id) {
      const list = $('historyList')
      if (!list.classList.contains('hidden')) {
        list.classList.add('hidden')
        return
      }
      list.classList.remove('hidden')
      list.innerHTML = '<div class="history-item">Pobieram historię...</div>'
      const res = await api(`history&id=${id}`)
      const data = await res.json()
      const rows = data.history || []
      list.innerHTML = rows.length
        ? rows.map((h) => `<div class="history-item"><small>${escapeHtml(h.created_at || '')} · ${escapeHtml(h.action || '')}</small>${escapeHtml(h.details || '')}</div>`).join('')
        : '<div class="history-item">Brak historii.</div>'
    }

    function selectProductPhoto(thumb, img, id, index, total) {
      const main = $('mainPhoto')
      if (main) {
        main.src = img
        main.onclick = () => openLightbox(id, index)
      }
      document.querySelectorAll('.product-thumbs img').forEach((item) => item.classList.remove('active'))
      thumb.classList.add('active')
      const counter = document.querySelector('.photo-count')
      if (counter) counter.textContent = (index + 1) + ' / ' + total
    }

    function openLightbox(id, index) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      state.lightboxImages = [m.image1,m.image2,m.image3,m.image4].filter(Boolean)
      state.lightboxIndex = index
      state.lightboxMachineId = id
      renderLightbox()
    }

    function renderLightbox() {
      const img = state.lightboxImages[state.lightboxIndex]
      $('modal').innerHTML = `<div class="modal lightbox-layer" onclick="closeLightbox()"><button class="btn btn-dark lightbox-close" onclick="event.stopPropagation();closeLightbox()">X</button><button class="btn btn-dark lightbox-arrow lightbox-prev" onclick="event.stopPropagation();prevPhoto()">‹</button><img class="lightbox-img" src="${img}" onclick="event.stopPropagation()" alt=""><button class="btn btn-dark lightbox-arrow lightbox-next" onclick="event.stopPropagation();nextPhoto()">›</button><div class="badge lightbox-counter">${state.lightboxIndex + 1}/${state.lightboxImages.length}</div></div>`
      $('modal').classList.remove('hidden')
    }

    function closeLightbox() {
      const id = state.lightboxMachineId
      state.lightboxImages = []
      state.lightboxIndex = 0
      state.lightboxMachineId = null
      if (id) openDetails(id)
      else closeModal()
    }

    function nextPhoto(){ state.lightboxIndex = (state.lightboxIndex + 1) % state.lightboxImages.length; renderLightbox() }
    function prevPhoto(){ state.lightboxIndex = (state.lightboxIndex - 1 + state.lightboxImages.length) % state.lightboxImages.length; renderLightbox() }

    function openEdit(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!m) return
      const slots = [m.image1,m.image2,m.image3,m.image4]
      const main = slots.find(Boolean) || ''
      $('modal').innerHTML = `<div class="product-modal"><div class="product-wrap">
        <div class="product-banner"><img src="/banner.png?v=20260516-19" alt="Maszyny Gliznowo"></div>
        <div class="product-toolbar"><div class="product-toolbar-left"><button class="btn btn-main" onclick="openDetails(${m.id})">WRÓĆ</button><button class="btn btn-dark" onclick="closeModal()">ZAMKNIJ</button></div><div class="product-meta">EDYCJA / ID ${m.id}</div></div>
        <div class="product-grid product-grid-view product-grid-edit">
          <section class="product-gallery product-edit-gallery"><div class="product-main-photo">${main ? `<img id="editMainPhoto" src="${main}" alt="${escapeAttr(m.name || 'Maszyna')}">` : '<div id="editMainPhoto" class="product-no-photo">Brak zdjęcia</div>'}<div class="photo-count">${slots.filter(Boolean).length} / 4</div></div><div class="product-thumbs edit-photo-slots">${[1,2,3,4].map((slot) => { const img = slots[slot-1]; return `<label class="edit-photo-slot" for="edit_image${slot}"><span class="edit-slot-number">Zdjęcie ${slot}</span><div id="edit_slot_preview_${slot}" class="edit-slot-preview">${img ? `<img src="${img}" alt="Zdjęcie ${slot}">` : '<div class="slot-empty">Pusty slot</div>'}</div><span class="edit-slot-action">Dotknij, aby podmienić</span><input id="edit_image${slot}" class="input edit-slot-input" type="file" accept="image/*" onchange="renderEditSlotPreview(${slot})"><div id="edit_image_name_${slot}" class="slot-file-name hidden"></div></label>` }).join('')} </div></section>
          <section class="product-panel product-summary-panel">
            <div class="product-head"><div class="product-badges"><span class="badge">#${escapeHtml(m.index_number || 'brak indeksu')}</span><span class="status-pill ${m.status === 'sold' ? 'archive' : ''}">${m.status === 'sold' ? 'Archiwum' : 'W magazynie'}</span></div><h1 class="product-title">Edycja maszyny</h1></div>
            <div class="product-section"><div class="product-edit-grid"><input id="edit_name" class="input" value="${escapeAttr(m.name)}" placeholder="Nazwa"><input id="edit_index" class="input" value="${escapeAttr(m.index_number)}" placeholder="Indeks"><div class="row price-row"><input id="edit_purchase" class="input" value="${escapeAttr(m.purchase_price)}" placeholder="Cena zakupu"><input id="edit_vat" class="input" value="${escapeAttr(m.vat_price)}" placeholder="VAT"><input id="edit_gross" class="input" value="${escapeAttr(m.gross_price)}" placeholder="Cena"></div></div></div>
            <div class="product-section"><div class="product-edit-actions"><button class="btn btn-green" onclick="saveEdit(${m.id})">ZAPISZ ZMIANY</button><button class="btn btn-dark" onclick="openDetails(${m.id})">ANULUJ</button></div></div>
          </section>
        </div>
        <section class="product-text-panel product-edit-text-panel"><div class="product-section"><h2 class="section-title">Opis</h2><textarea id="edit_description" class="textarea edit-long-textarea" placeholder="Opis">${escapeHtml(m.description)}</textarea></div><div class="product-section"><h2 class="section-title">Notatka</h2><textarea id="edit_note" class="textarea edit-long-textarea" placeholder="Notatka">${escapeHtml(m.note)}</textarea></div></section>
      </div></div>`
      $('modal').classList.remove('hidden')
    }
    async function saveEdit(id) {
      setLoading(true, 'Zapisuję zmiany...')
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      const form = formFromMachine(m)
      form.set('name', $('edit_name').value); form.set('index_number', $('edit_index').value); form.set('purchase_price', $('edit_purchase').value); form.set('vat_price', $('edit_vat').value); form.set('gross_price', $('edit_gross').value); form.set('description', $('edit_description').value); form.set('note', $('edit_note').value)
      for (const i of [1,2,3,4]) await appendCompressedImages(form, $(`edit_image${i}`), 'image', i)
      form.set('history_action', 'Edycja'); form.set('history_details', 'Zaktualizowano dane maszyny.')
      const res = await api('update', { method: 'POST', body: form })
      const data = await res.json()
      if (!res.ok) { setLoading(false); return toast(data.error || 'Nie udało się zapisać.', 'error') }
      closeModal(); toast('Zapisano.'); await loadMachines(); setLoading(false)
    }

    async function appendCompressedImages(form, input, prefix, fixedIndex = null) {
      const sourceFiles = Array.isArray(input) ? input : Array.from(input?.files || [])
      const files = sourceFiles.slice(0, fixedIndex ? 1 : 4)
      for (let i = 0; i < files.length; i++) {
        const slot = fixedIndex || i + 1
        const file = await compressImage(files[i])
        form.set(`${prefix}${slot}`, file, file.name)
      }
    }

    async function compressImage(file) {
      if (!file || !file.type.startsWith('image/')) return file
      const maxSide = 1600
      const quality = 0.82
      let sourceHandle = null
      try {
        const originalOrientation = await readJpegOrientation(file)
        const loaded = await loadCanvasSource(file)
        sourceHandle = loaded.source
        const sourceWidth = sourceHandle.width || sourceHandle.naturalWidth
        const sourceHeight = sourceHandle.height || sourceHandle.naturalHeight
        const browserAlreadyRotated = originalOrientation >= 5 && originalOrientation <= 8 && sourceHeight > sourceWidth
        const orientation = browserAlreadyRotated || loaded.alreadyOriented ? 1 : originalOrientation
        const rotated = orientation >= 5 && orientation <= 8
        const orientedWidth = rotated ? sourceHeight : sourceWidth
        const orientedHeight = rotated ? sourceWidth : sourceHeight
        const scale = Math.min(1, maxSide / Math.max(orientedWidth, orientedHeight))
        const width = Math.max(1, Math.round(orientedWidth * scale))
        const height = Math.max(1, Math.round(orientedHeight * scale))
        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height
        const ctx = canvas.getContext('2d')
        applyExifTransform(ctx, orientation, width, height)
        const drawWidth = rotated ? height : width
        const drawHeight = rotated ? width : height
        ctx.drawImage(sourceHandle, 0, 0, sourceWidth, sourceHeight, 0, 0, drawWidth, drawHeight)
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', quality))
        if (!blob) return file
        const base = file.name.replace(/\.[^.]+$/, '') || 'zdjecie'
        return new File([blob], `${base}.webp`, { type: 'image/webp', lastModified: Date.now() })
      } catch {
        return file
      } finally {
        if (sourceHandle && typeof sourceHandle.close === 'function') sourceHandle.close()
      }
    }
    async function loadCanvasSource(file) {
      if ('createImageBitmap' in window) {
        try {
          return { source: await createImageBitmap(file, { imageOrientation: 'none' }), alreadyOriented: false }
        } catch {
          return { source: await createImageBitmap(file), alreadyOriented: true }
        }
      }
      return { source: await loadImageElement(file), alreadyOriented: true }
    }

    async function loadImageElement(file) {
      const url = URL.createObjectURL(file)
      try {
        const img = await new Promise((resolve, reject) => {
          const image = new Image()
          image.onload = () => resolve(image)
          image.onerror = reject
          image.src = url
        })
        return img
      } finally {
        URL.revokeObjectURL(url)
      }
    }

    async function readJpegOrientation(file) {
      if (!file || !/^image\/jpe?g$/i.test(file.type)) return 1
      const buffer = await file.slice(0, 65536).arrayBuffer()
      const view = new DataView(buffer)
      if (view.byteLength < 4 || view.getUint16(0, false) !== 0xffd8) return 1
      let offset = 2
      while (offset + 4 < view.byteLength) {
        const marker = view.getUint16(offset, false)
        offset += 2
        if (marker === 0xffe1) {
          const exifStart = offset + 2
          if (getAscii(view, exifStart, 4) !== 'Exif') return 1
          const tiff = exifStart + 6
          const little = view.getUint16(tiff, false) === 0x4949
          const firstIfd = tiff + view.getUint32(tiff + 4, little)
          if (firstIfd < 0 || firstIfd + 2 > view.byteLength) return 1
          const entries = view.getUint16(firstIfd, little)
          for (let i = 0; i < entries; i++) {
            const entry = firstIfd + 2 + i * 12
            if (entry + 12 > view.byteLength) break
            if (view.getUint16(entry, little) === 0x0112) return view.getUint16(entry + 8, little)
          }
          return 1
        }
        if ((marker & 0xff00) !== 0xff00) break
        offset += view.getUint16(offset, false)
      }
      return 1
    }

    function getAscii(view, start, length) {
      let value = ''
      for (let i = 0; i < length; i++) value += String.fromCharCode(view.getUint8(start + i))
      return value
    }

    function applyExifTransform(ctx, orientation, width, height) {
      switch (orientation) {
        case 2:
          ctx.translate(width, 0)
          ctx.scale(-1, 1)
          break
        case 3:
          ctx.translate(width, height)
          ctx.rotate(Math.PI)
          break
        case 4:
          ctx.translate(0, height)
          ctx.scale(1, -1)
          break
        case 5:
          ctx.rotate(Math.PI / 2)
          ctx.scale(1, -1)
          break
        case 6:
          ctx.translate(width, 0)
          ctx.rotate(Math.PI / 2)
          break
        case 7:
          ctx.translate(width, height)
          ctx.rotate(Math.PI / 2)
          ctx.scale(-1, 1)
          break
        case 8:
          ctx.translate(0, height)
          ctx.rotate(-Math.PI / 2)
          break
      }
    }
    function closeModal(){ state.lightboxImages = []; state.lightboxIndex = 0; state.lightboxMachineId = null; $('modal').classList.add('hidden'); $('modal').innerHTML = '' }
    function escapeHtml(v){ return text(v).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])) }
    function escapeAttr(v){ return escapeHtml(v).replace(/"/g, '&quot;') }

    $('loginBtn').onclick = login
    ;[$('loginEmail'), $('loginPassword')].forEach((field) => field.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault()
        login()
      }
    }))
    $('togglePassword').onclick = () => { const p = $('loginPassword'); p.type = p.type === 'password' ? 'text' : 'password'; $('togglePassword').textContent = p.type === 'password' ? 'POKAŻ' : 'UKRYJ' }
    $('logoutBtn').onclick = logout
    $('activeTab').onclick = () => { state.view = 'available'; loadMachines() }
    $('archiveTab').onclick = () => { state.view = 'sold'; loadMachines() }
    $('mobileActive').onclick = $('activeTab').onclick
    $('mobileArchive').onclick = $('archiveTab').onclick
    $('mobileAdd').onclick = () => $('formPanel').classList.toggle('hidden')
    $('toggleForm').onclick = () => $('formPanel').classList.toggle('hidden')
    $('saveNew').onclick = createMachine
    $('images').onchange = () => {
      addCreateImages(Array.from($('images').files || []))
      $('images').value = ''
    }

    function addCreateImages(files) {
      for (const file of files) {
        if (selectedCreateImages.length >= 4) break
        selectedCreateImages.push(file)
      }
      if (files.length && selectedCreateImages.length >= 4) toast('Wybrano maksymalnie 4 zdjęcia.')
      renderCreateImagePreview()
    }

    function removeCreateImage(index) {
      selectedCreateImages.splice(index, 1)
      renderCreateImagePreview()
    }

    function renderCreateImagePreview() {
      $('filePreview').classList.toggle('hidden', selectedCreateImages.length === 0)
      $('filePreview').innerHTML = selectedCreateImages.map((file, index) => {
        const url = URL.createObjectURL(file)
        return `<div class="create-photo-preview"><img src="${url}" alt="Wybrane zdjęcie ${index + 1}" onload="URL.revokeObjectURL(this.src)"><div><strong>Zdjęcie ${index + 1}</strong><span>${escapeHtml(file.name)}</span></div><button type="button" class="file-remove" onclick="removeCreateImage(${index})">USUŃ</button></div>`
      }).join('')
    }


    function renderEditSlotPreview(slot) {
      const input = $(`edit_image${slot}`)
      const preview = $(`edit_slot_preview_${slot}`)
      const name = $(`edit_image_name_${slot}`)
      const file = input?.files?.[0]
      if (!file || !preview || !name) return
      const url = URL.createObjectURL(file)
      preview.innerHTML = `<img src="${url}" alt="Wybrane zdjęcie ${slot}">`
      const main = $('editMainPhoto')
      if (slot === 1 && main?.tagName === 'IMG') main.src = url
      name.textContent = `Wybrano: ${file.name}`
      name.classList.remove('hidden')
    }
    $('search').oninput = () => { state.search = $('search').value; render() }
    window.addEventListener('resize', () => render())
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { if (state.lightboxImages.length) closeLightbox(); else closeModal(); return } if (!$('modal').classList.contains('hidden') && state.lightboxImages.length) { if (event.key === 'ArrowRight') nextPhoto(); if (event.key === 'ArrowLeft') prevPhoto() } })
    init()
  </script>
</body>
</html>
