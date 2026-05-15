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
  <link rel="stylesheet" href="/style.css?v=20260515-10">
</head>
<body style="background:#0f0f0f;color:#fafafa;margin:0">
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
      <div class="searchbar">
        <input id="search" class="input" type="search" autocomplete="off" placeholder="Szukaj po nazwie lub indeksie">
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
            <div class="row" style="grid-template-columns:repeat(3,1fr)">
              <input id="purchase_price" class="input" placeholder="Cena zakupu">
              <input id="vat_price" class="input" placeholder="VAT">
              <input id="gross_price" class="input" placeholder="Cena">
            </div>
            <input id="images" class="input" type="file" accept="image/*" multiple>
            <div id="filePreview" class="file-preview hidden"></div>
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
    const state = { machines: [], view: 'available', mode: 'table', sortMode: 'newest', search: '', editingId: null, edit: {}, lightboxImages: [], lightboxIndex: 0 }
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

    function setLoading(value, label = 'Pracuję...') {
      $('loading').querySelector('.loading-card').textContent = label
      $('loading').classList.toggle('hidden', !value)
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
      state.mode = 'table'
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
      $('tableBox').classList.toggle('hidden', state.mode !== 'table')
      $('cards').classList.toggle('hidden', state.mode !== 'cards')
      $('tableMode').classList.toggle('btn-main', state.mode === 'table')
      $('cardMode').classList.toggle('btn-main', state.mode === 'cards')
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
      $('cards').innerHTML = rows.map((m) => `<article class="card clickable-card" onclick="openDetails(${m.id})">
        ${m.image1 ? `<img class="card-img" src="${m.image1}" alt="${escapeHtml(m.name)}">` : '<div class="card-img"></div>'}
        <div class="card-body"><span class="badge">#${escapeHtml(m.index_number || 'brak')}</span><h3>${escapeHtml(m.name || 'Bez nazwy')}</h3>
        <div class="prices"><div class="price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="price highlight"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
        <p class="note">${escapeHtml(m.note || 'Brak notatki')}</p><div class="actions">${actions(m)}</div></div>
      </article>`).join('')
    }

    function actions(m) {
      return `<button class="btn btn-dark btn-small" onclick="event.stopPropagation(); openDetails(${m.id})">PODGLĄD</button>
        <button class="btn btn-dark btn-small" onclick="event.stopPropagation(); startQuickEdit(mById(${m.id}))">EDYTUJ</button>
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
        Array.from($('images').files || []).slice(0, 4).forEach((file, i) => form.append(`image${i + 1}`, file))
        const res = await api('create', { method: 'POST', body: form })
        const data = await res.json()
        if (!res.ok) return toast(data.error || 'Nie udało się zapisać.', 'error')
        for (const id of ['name','index_number','purchase_price','vat_price','gross_price','description','note']) $(id).value = ''
        $('images').value = ''
        $('filePreview').innerHTML = ''
        $('filePreview').classList.add('hidden')
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
        <div class="product-banner"><img src="/banner.png" alt="Maszyny Gliznowo"></div>
        <div class="product-toolbar">
          <div class="product-toolbar-left">
            <button class="btn btn-main" onclick="closeModal()">WRÓĆ</button>
            <button class="btn btn-dark" onclick="openEdit(${m.id})">EDYTUJ</button>
          </div>
          <div class="product-meta">${m.status === 'sold' ? 'ARCHIWUM' : 'MAGAZYN'} / ${imgs.length} zdjęć / ID ${m.id}</div>
        </div>
        <div class="product-grid">
          <section class="product-gallery">
            <div class="product-main-photo">
              ${main ? `<img id="mainPhoto" src="${main}" onclick="openLightbox(${id}, 0)" alt="${escapeAttr(m.name || 'Maszyna')}">` : '<div class="product-no-photo">Brak zdjęcia</div>'}
              <div class="photo-count">${imgs.length ? '1 / ' + imgs.length : '0 / 4'}</div>
            </div>
            ${imgs.length ? `<div class="product-thumbs">${imgs.map((img, index) => `<img class="${index === 0 ? 'active' : ''}" src="${img}" onclick="selectProductPhoto(this, '${img}', ${id}, ${index}, ${imgs.length})" alt="Zdjęcie ${index + 1}">`).join('')}</div>` : ''}
          </section>
          <section class="product-panel">
            <div class="product-head">
              <div class="product-badges"><span class="badge">#${escapeHtml(m.index_number || 'brak indeksu')}</span><span class="status-pill ${m.status === 'sold' ? 'archive' : ''}">${m.status === 'sold' ? 'Archiwum' : 'W magazynie'}</span></div>
              <h1 class="product-title">${escapeHtml(m.name || 'Bez nazwy')}</h1>
            </div>
            <div class="product-section">
              <div class="product-prices"><div class="product-price"><strong>Cena zakupu</strong><span>${escapeHtml(price(m.purchase_price))}</span></div><div class="product-price"><strong>VAT</strong><span>${escapeHtml(price(m.vat_price))}</span></div><div class="product-price main"><strong>Cena</strong><span>${escapeHtml(price(m.gross_price))}</span></div></div>
            </div>
            <div class="product-section"><h2 class="section-title">Opis</h2><p class="product-copy">${escapeHtml(m.description || 'Brak opisu')}</p></div>
            <div class="product-section"><h2 class="section-title">Notatka</h2><p class="product-copy">${escapeHtml(m.note || 'Brak notatki')}</p></div>
            <div class="product-section"><div class="product-actions"><button class="btn btn-dark" onclick="toggleHistory(${id})">Historia zmian</button><div id="historyList" class="history-list hidden"></div>${m.status === 'sold' ? `<button class="btn btn-green" onclick="setStatus(${m.id}, 'available'); closeModal()">PRZYWRÓĆ DO MAGAZYNU</button>` : `<button class="btn btn-main" onclick="setStatus(${m.id}, 'sold'); closeModal()">PRZENIEŚ DO ARCHIWUM</button>`}</div></div>
          </section>
        </div>
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
      renderLightbox()
    }

    function renderLightbox() {
      const img = state.lightboxImages[state.lightboxIndex]
      $('modal').innerHTML = `<div class="modal" onclick="closeModal()"><button class="btn btn-dark" style="position:fixed;top:14px;right:14px;z-index:2" onclick="event.stopPropagation();closeModal()">X</button><button class="btn btn-dark" style="position:fixed;left:14px;top:50%;z-index:2" onclick="event.stopPropagation();prevPhoto()">‹</button><img class="lightbox-img" src="${img}" onclick="event.stopPropagation()" alt=""><button class="btn btn-dark" style="position:fixed;right:14px;top:50%;z-index:2" onclick="event.stopPropagation();nextPhoto()">›</button><div class="badge" style="position:fixed;bottom:18px;left:50%;transform:translateX(-50%)">${state.lightboxIndex + 1}/${state.lightboxImages.length}</div></div>`
      $('modal').classList.remove('hidden')
    }

    function nextPhoto(){ state.lightboxIndex = (state.lightboxIndex + 1) % state.lightboxImages.length; renderLightbox() }
    function prevPhoto(){ state.lightboxIndex = (state.lightboxIndex - 1 + state.lightboxImages.length) % state.lightboxImages.length; renderLightbox() }

    function openEdit(id) {
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      if (!m) return
      const slots = [m.image1,m.image2,m.image3,m.image4]
      $('modal').innerHTML = `<div class="product-modal"><div class="product-wrap">
        <div class="product-banner"><img src="/banner.png" alt="Maszyny Gliznowo"></div>
        <div class="product-toolbar"><div class="product-toolbar-left"><button class="btn btn-main" onclick="openDetails(${m.id})">WRÓĆ</button><button class="btn btn-dark" onclick="closeModal()">ZAMKNIJ</button></div><div class="product-meta">EDYCJA / ID ${m.id}</div></div>
        <div class="product-grid">
          <section class="product-gallery"><div class="product-main-photo">${slots.find(Boolean) ? `<img src="${slots.find(Boolean)}" alt="${escapeAttr(m.name || 'Maszyna')}">` : '<div class="product-no-photo">Brak zdjęcia</div>'}</div><div class="product-thumbs">${slots.map((img, index) => img ? `<img src="${img}" alt="Zdjęcie ${index + 1}">` : '<div class="slot-empty">Pusty slot</div>').join('')}</div></section>
          <section class="product-panel"><div class="product-head"><h1 class="product-title">Edycja maszyny</h1></div><div class="product-section"><div class="product-edit-grid">
            <input id="edit_name" class="input" value="${escapeAttr(m.name)}" placeholder="Nazwa"><input id="edit_index" class="input" value="${escapeAttr(m.index_number)}" placeholder="Indeks">
            <div class="row" style="grid-template-columns:repeat(3,1fr)"><input id="edit_purchase" class="input" value="${escapeAttr(m.purchase_price)}" placeholder="Cena zakupu"><input id="edit_vat" class="input" value="${escapeAttr(m.vat_price)}" placeholder="VAT"><input id="edit_gross" class="input" value="${escapeAttr(m.gross_price)}" placeholder="Cena"></div>
            <textarea id="edit_description" class="textarea" placeholder="Opis">${escapeHtml(m.description)}</textarea><textarea id="edit_note" class="textarea" placeholder="Notatka">${escapeHtml(m.note)}</textarea>
          </div></div><div class="product-section"><h2 class="section-title">Podmień konkretne zdjęcie</h2><div class="slot-grid">${[1,2,3,4].map((i) => `<div class="slot-card">${slots[i-1] ? `<img src="${slots[i-1]}" alt="Zdjęcie ${i}">` : '<div class="slot-empty">Brak zdjęcia</div>'}<label>Zdjęcie ${i}</label><input id="edit_image${i}" class="input" type="file" accept="image/*"></div>`).join('')}</div></div><div class="product-section"><div class="product-edit-actions"><button class="btn btn-green" onclick="saveEdit(${m.id})">ZAPISZ ZMIANY</button><button class="btn btn-dark" onclick="openDetails(${m.id})">ANULUJ</button></div></div></section>
        </div>
      </div></div>`
      $('modal').classList.remove('hidden')
    }

    async function saveEdit(id) {
      setLoading(true, 'Zapisuję zmiany...')
      const m = state.machines.find((x) => Number(x.id) === Number(id))
      const form = formFromMachine(m)
      form.set('name', $('edit_name').value); form.set('index_number', $('edit_index').value); form.set('purchase_price', $('edit_purchase').value); form.set('vat_price', $('edit_vat').value); form.set('gross_price', $('edit_gross').value); form.set('description', $('edit_description').value); form.set('note', $('edit_note').value)
      for (const i of [1,2,3,4]) if ($(`edit_image${i}`).files[0]) form.append(`image${i}`, $(`edit_image${i}`).files[0])
      form.set('history_action', 'Edycja'); form.set('history_details', 'Zaktualizowano dane maszyny.')
      const res = await api('update', { method: 'POST', body: form })
      const data = await res.json()
      if (!res.ok) { setLoading(false); return toast(data.error || 'Nie udało się zapisać.', 'error') }
      closeModal(); toast('Zapisano.'); await loadMachines(); setLoading(false)
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
    $('toggleForm').onclick = () => $('formPanel').classList.toggle('hidden')
    $('saveNew').onclick = createMachine
    $('images').onchange = () => {
      const files = Array.from($('images').files || []).slice(0, 4)
      $('filePreview').classList.toggle('hidden', files.length === 0)
      $('filePreview').innerHTML = files.map((file, index) => `<div class="file-pill">${index + 1}. ${escapeHtml(file.name)}</div>`).join('')
    }
    $('tableMode').onclick = () => { state.mode = 'table'; render() }
    $('cardMode').onclick = () => { state.mode = 'cards'; render() }
    $('search').oninput = () => { state.search = $('search').value; render() }
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); if (!$('modal').classList.contains('hidden') && state.lightboxImages.length) { if (event.key === 'ArrowRight') nextPhoto(); if (event.key === 'ArrowLeft') prevPhoto() } })
    init()
  </script>
</body>
</html>
