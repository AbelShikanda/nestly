<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Kenya Real Estate · Pure Glass UI</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    .reels-container {
      height: 100vh;
      width: 100%;
      overflow-y: scroll;
      scroll-snap-type: y mandatory;
      scroll-behavior: smooth;
      background: #000;
    }
    .slide {
      scroll-snap-align: start;
      height: 100vh;
      width: 100%;
      position: relative;
      background: #0b0f15;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .hero-media {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }
    .hero-media video, .hero-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 70%);
      padding: 2rem 1.5rem 1.8rem 1.5rem;
      z-index: 10;
      color: white;
      font-family: system-ui, sans-serif;
      pointer-events: none;
    }
    .property-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 0.4rem; }
    .property-detail { font-size: 1rem; opacity: 0.9; margin-bottom: 0.5rem; display: flex; flex-wrap: wrap; gap: 1rem; }
    .price { font-size: 1.4rem; font-weight: 700; color: #facc15; }
    .badge {
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(6px);
      border-radius: 40px;
      padding: 0.2rem 0.9rem;
      font-size: 0.75rem;
      display: inline-block;
      width: fit-content;
    }
    .landlord-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 25;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(12px);
      border-radius: 60px;
      padding: 8px 18px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      border: 1px solid rgba(250,204,21,0.4);
      pointer-events: auto;
      transition: opacity 0.2s ease;
    }
    .landlord-avatar { width: 28px; height: 28px; background: #facc15; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #1a1e24; }
    .landlord-name { font-size: 0.8rem; font-weight: 600; color: white; }
    .landlord-badge { font-size: 0.65rem; color: #facc15; }
    .thumbnail-strip {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 20;
      max-height: 60vh;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
      padding: 8px 4px;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(12px);
      border-radius: 32px;
      transition: opacity 0.2s ease;
    }
    .thumbnail {
      width: 68px;
      height: 68px;
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      border: 2px solid rgba(255,255,255,0.5);
    }
    .thumbnail.active-thumb { border: 3px solid #facc15; transform: scale(1.05); }
    .thumbnail img, .thumbnail video { width: 100%; height: 100%; object-fit: cover; }
    .sound-toggle {
      position: absolute;
      bottom: 90px;
      right: 16px;
      z-index: 15;
      background: rgba(0,0,0,0.55);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: none;
      color: white;
    }
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.85);
      backdrop-filter: blur(12px);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      visibility: hidden;
      opacity: 0;
      transition: all 0.2s ease;
    }
    .modal-overlay.active { visibility: visible; opacity: 1; }
    .modal-card {
      background: rgba(20,20,30,0.95);
      backdrop-filter: blur(24px);
      border-radius: 32px;
      width: 90%;
      max-width: 400px;
      border: 1px solid rgba(250,204,21,0.3);
      overflow: hidden;
    }
    .modal-header { padding: 20px; background: linear-gradient(135deg, #1e293b, #0f172a); display: flex; align-items: center; gap: 16px; }
    .modal-avatar { width: 60px; height: 60px; background: #facc15; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
    .modal-info h3 { color: white; }
    .modal-body { padding: 20px; }
    .contact-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 10px; background: rgba(255,255,255,0.08); border-radius: 20px; }
    .modal-close { width: 100%; padding: 14px; background: #facc15; border: none; font-weight: bold; cursor: pointer; }
    .badge-verified { background: #10b981; display: inline-block; padding: 2px 8px; border-radius: 40px; font-size: 0.65rem; margin-left: 8px; }
    
    /* ========== BOTTOM NAVIGATION - PURE GLASS (NO BLACK BACKGROUND) ========== */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: transparent;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 10px 20px 12px;
      z-index: 200;
      display: flex;
      justify-content: space-around;
      transition: transform 0.3s ease, opacity 0.2s ease;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .bottom-nav.hide-nav { transform: translateY(100%); }
    
    /* ========== SEARCH HEADER - PURE GLASS (NO BLACK BACKGROUND) ========== */
    .search-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      background: transparent;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 0.75rem 1rem;
      transition: transform 0.3s ease, opacity 0.2s ease;
      transform: translateY(0);
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .search-header.hide { transform: translateY(-100%); }
    
    .nav-item { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; padding: 4px 12px; border-radius: 40px; transition: all 0.1s; }
    .nav-item:active { transform: scale(0.95); }
    .nav-icon { font-size: 1.6rem; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3)); }
    .nav-label { font-size: 0.65rem; color: rgba(255,255,255,0.9); letter-spacing: 0.3px; font-weight: 500; }
    .nav-item.active .nav-label, .nav-item.active .nav-icon { color: #facc15; text-shadow: 0 0 4px rgba(250,204,21,0.3); }
    
    .search-container { position: relative; max-width: 600px; margin: 0 auto; }
    .search-input { 
      width: 100%; 
      padding: 0.85rem 1rem 0.85rem 2.6rem; 
      border-radius: 44px; 
      background: rgba(255,255,255,0.15); 
      backdrop-filter: blur(8px);
      color: white; 
      border: 1px solid rgba(255,255,255,0.25); 
      outline: none; 
      font-size: 1rem;
    }
    .search-input:focus { background: rgba(0,0,0,0.4); border-color: #facc15; }
    .search-input::placeholder { color: rgba(255,255,255,0.7); }
    .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); pointer-events: none; font-size: 1rem; }
    .search-clear { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); display: none; cursor: pointer; color: rgba(255,255,255,0.8); background: none; border: none; font-size: 1rem; padding: 4px; }
    .search-clear.visible { display: block; }
    
    .demo-toast, .no-results-toast { position: fixed; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.85); backdrop-filter: blur(12px); color: #facc15; padding: 6px 18px; border-radius: 40px; font-size: 0.8rem; z-index: 201; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap; }
    .demo-toast { bottom: 80px; }
    .no-results-toast { top: 80px; }
    .show-toast { opacity: 1 !important; }
    .loading-indicator { text-align: center; padding: 20px; color: #facc15; background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); position: sticky; bottom: 0; z-index: 50; }
    
    /* Double tap hint */
    .doubletap-hint {
      position: fixed;
      bottom: 100px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(8px);
      padding: 4px 12px;
      border-radius: 40px;
      font-size: 10px;
      color: rgba(255,255,255,0.7);
      z-index: 202;
      pointer-events: none;
      font-family: monospace;
      white-space: nowrap;
      transition: opacity 0.3s;
    }
    
    @media (max-width: 480px) { .thumbnail { width: 54px; height: 54px; } .property-title { font-size: 1.4rem; } .doubletap-hint { bottom: 90px; font-size: 8px; } .nav-icon { font-size: 1.3rem; } .nav-label { font-size: 0.55rem; } }
  </style>
</head>
<body>

<div class="search-header" id="searchHeader">
  <div class="search-container">
    <span class="search-icon">🔍</span>
    <input type="text" class="search-input" id="searchInput" placeholder="Search Nairobi, Mombasa, Kisumu..." autocomplete="off">
    <button class="search-clear" id="searchClearBtn">✕</button>
  </div>
</div>

<div class="reels-container" id="reelsContainer"></div>

<div class="bottom-nav" id="bottomNav">
  <div class="nav-item" data-nav="home"><div class="nav-icon">🏠</div><div class="nav-label">Home</div></div>
  <div class="nav-item" data-nav="explore"><div class="nav-icon">🔍</div><div class="nav-label">Explore</div></div>
  <div class="nav-item" data-nav="add"><div class="nav-icon">➕</div><div class="nav-label">Add</div></div>
  <div class="nav-item" data-nav="chat"><div class="nav-icon">💬</div><div class="nav-label">Chat</div></div>
  <div class="nav-item" data-nav="profile"><div class="nav-icon">👤</div><div class="nav-label">Profile</div></div>
</div>

<div class="doubletap-hint" id="doubleTapHint">✨ Double tap anywhere to hide/show glass menu & search</div>
<div class="demo-toast" id="demoToast">🇰🇪 Kenya Real Estate · Pure Glass UI</div>
<div class="no-results-toast" id="noResultsToast">✨ No matching properties</div>

<div class="modal-overlay" id="landlordModal">
  <div class="modal-card">
    <div class="modal-header" id="modalHeader"></div>
    <div class="modal-body" id="modalBody"></div>
    <button class="modal-close" id="closeModalBtn">Close</button>
  </div>
</div>

<script>
  (function() {
    // ========== KENYAN DATA ==========
    const landlordNames = ["James Mwangi", "Grace Achieng", "Peter Omondi", "Fatma Hassan", "John Kariuki", "Lucy Wanjiku", "Mohamed Ali"];
    const landlordSuffix = ["Properties", "Real Estate", "Homes Ltd"];
    const kenyanLocations = ["Nairobi, Kilimani", "Nairobi, Westlands", "Nairobi, Karen", "Mombasa, Nyali", "Kisumu, Milimani", "Kiambu, Thika Road", "Nakuru, Milimani"];
    const propertyTypes = ["Modern Apartment", "Spacious Villa", "Executive Townhouse", "Cozy Bungalow"];
    const bedrooms = ["2 bed", "3 bed", "4 bed"];
    const bathrooms = ["2 bath", "3 bath"];
    const sqft = ["1,200 sqft", "1,800 sqft", "2,500 sqft"];
    const priceRanges = [18500000, 22000000, 28500000, 7500000, 12500000];
    const imagePool = [
      "https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg",
      "https://images.pexels.com/photos/2587054/pexels-photo-2587054.jpeg",
      "https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg"
    ];
    const videoPool = [
      "https://storage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4",
      "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFunflies.mp4"
    ];
    
    function getRandomItem(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
    function formatKES(price) { return "KES " + price.toLocaleString('en-KE'); }
    
    function getRandomLandlord() {
      const name = getRandomItem(landlordNames);
      return {
        name: name,
        company: `${name.split(' ')[0]} ${getRandomItem(landlordSuffix)}`,
        phone: `+2547${Math.floor(Math.random() * 90000000 + 10000000)}`,
        email: `${name.toLowerCase().replace(' ', '.')}@${getRandomItem(['ke.realestate', 'property.co.ke'])}`,
        verified: Math.random() > 0.3,
        responseTime: getRandomItem(["< 1 hour", "< 2 hours", "< 30 min"]),
        memberSince: 2020 + Math.floor(Math.random() * 4)
      };
    }
    
    function generateRandomProperty(idx) {
      const location = getRandomItem(kenyanLocations);
      const price = getRandomItem(priceRanges);
      const mainMedia = Math.random() > 0.6 ? { type: 'video', url: getRandomItem(videoPool) } : { type: 'image', url: getRandomItem(imagePool) };
      const gallery = [{ type: mainMedia.type, url: mainMedia.url, label: 'main' }];
      for (let i = 1; i < 5; i++) {
        gallery.push(Math.random() > 0.7 ? { type: 'video', url: getRandomItem(videoPool), label: 'tour' } : { type: 'image', url: getRandomItem(imagePool), label: 'photo' });
      }
      return {
        id: Date.now() + idx + Math.random(),
        location: location,
        title: `${getRandomItem(propertyTypes)} in ${location.split(',')[0]}`,
        beds: getRandomItem(bedrooms),
        baths: getRandomItem(bathrooms),
        area: getRandomItem(sqft),
        price: price,
        priceFormatted: formatKES(price),
        mainMedia: mainMedia,
        gallery: gallery,
        landlord: getRandomLandlord()
      };
    }
    
    let allProperties = [];
    for (let i = 0; i < 6; i++) allProperties.push(generateRandomProperty(i));
    
    const container = document.getElementById('reelsContainer');
    let isLoading = false;
    let observer = null;
    
    function showDemoToast(msg) {
      const toast = document.getElementById('demoToast');
      toast.innerText = msg;
      toast.classList.add('show-toast');
      setTimeout(() => toast.classList.remove('show-toast'), 1500);
    }
    
    // Modal
    const modal = document.getElementById('landlordModal');
    const modalHeader = document.getElementById('modalHeader');
    const modalBody = document.getElementById('modalBody');
    document.getElementById('closeModalBtn').addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('active'); });
    
    function showLandlordModal(landlord, propertyTitle) {
      modalHeader.innerHTML = `
        <div class="modal-avatar">🏠</div>
        <div class="modal-info">
          <h3>${landlord.name} ${landlord.verified ? '<span class="badge-verified">✓ Verified</span>' : ''}</h3>
          <p>${landlord.company}</p>
        </div>
      `;
      modalBody.innerHTML = `
        <div class="contact-row"><div class="contact-icon">📞</div><div class="contact-details"><div class="contact-label">Phone (SMS/WhatsApp)</div><div class="contact-value">${landlord.phone}</div></div></div>
        <div class="contact-row"><div class="contact-icon">✉️</div><div class="contact-details"><div class="contact-label">Email</div><div class="contact-value">${landlord.email}</div></div></div>
        <div class="contact-row"><div class="contact-icon">⏱️</div><div class="contact-details"><div class="contact-label">Typical response</div><div class="contact-value">${landlord.responseTime}</div></div></div>
        <div class="contact-row"><div class="contact-icon">🏘️</div><div class="contact-details"><div class="contact-label">Property Listed</div><div class="contact-value">${propertyTitle}</div></div></div>
        <div class="contact-row"><div class="contact-icon">📅</div><div class="contact-details"><div class="contact-label">Member since</div><div class="contact-value">${landlord.memberSince}</div></div></div>
      `;
      modal.classList.add('active');
    }
    
    function createSlide(property) {
      const slideDiv = document.createElement('div');
      slideDiv.className = 'slide';
      slideDiv.setAttribute('data-city', property.location.toLowerCase());
      slideDiv.setAttribute('data-property', property.title.toLowerCase());
      slideDiv.setAttribute('data-price', property.price.toString());
      
      const heroDiv = document.createElement('div');
      heroDiv.className = 'hero-media';
      if (property.mainMedia.type === 'video') {
        const video = document.createElement('video');
        video.autoplay = true; video.muted = true; video.loop = true; video.playsInline = true;
        const source = document.createElement('source');
        source.src = property.mainMedia.url;
        video.appendChild(source);
        heroDiv.appendChild(video);
      } else {
        const img = document.createElement('img');
        img.src = property.mainMedia.url;
        heroDiv.appendChild(img);
      }
      slideDiv.appendChild(heroDiv);
      
      const landlordBtn = document.createElement('div');
      landlordBtn.className = 'landlord-btn';
      landlordBtn.innerHTML = `<div class="landlord-avatar">👤</div><div><div class="landlord-name">${property.landlord.name}</div><div class="landlord-badge">${property.landlord.verified ? '✓ Verified' : 'Owner'}</div></div>`;
      landlordBtn.addEventListener('click', (e) => { e.stopPropagation(); showLandlordModal(property.landlord, property.title); });
      slideDiv.appendChild(landlordBtn);
      
      const overlay = document.createElement('div');
      overlay.className = 'overlay';
      overlay.innerHTML = `<div class="badge">🇰🇪 ${property.location.includes('Nairobi') ? 'NAIROBI' : property.location.includes('Mombasa') ? 'MOMBASA' : 'KENYA'}</div><div class="property-title">${property.title}</div><div class="property-detail">📍 ${property.location} | ${property.beds} · ${property.baths} · ${property.area}</div><div class="price">${property.priceFormatted}</div>`;
      slideDiv.appendChild(overlay);
      
      const thumbStrip = document.createElement('div');
      thumbStrip.className = 'thumbnail-strip';
      slideDiv.appendChild(thumbStrip);
      
      return { slideDiv, heroDiv, thumbStrip };
    }
    
    function buildThumbnails(thumbStrip, gallery, slideElement, heroDiv) {
      if (!gallery.length) return;
      const labelDiv = document.createElement('div'); labelDiv.className = 'thumb-label'; labelDiv.innerText = '📸'; labelDiv.style.cssText = 'text-align:center;font-size:9px;color:#facc15;margin-bottom:4px;';
      thumbStrip.appendChild(labelDiv);
      let activeThumb = null;
      function setMainMedia(item, thumbEl) {
        heroDiv.innerHTML = '';
        if (item.type === 'image') {
          const img = document.createElement('img'); img.src = item.url; img.style.cssText = 'width:100%;height:100%;object-fit:cover';
          heroDiv.appendChild(img);
        } else {
          const vid = document.createElement('video'); vid.src = item.url; vid.autoplay = true; vid.muted = true; vid.loop = true;
          vid.style.cssText = 'width:100%;height:100%;object-fit:cover';
          heroDiv.appendChild(vid);
          vid.play().catch(e=>{});
          let toggle = slideElement.querySelector('.sound-toggle');
          if (!toggle) { const btn = document.createElement('button'); btn.className = 'sound-toggle'; btn.innerHTML = '🔇'; slideElement.appendChild(btn); toggle = btn; }
          const updateIcon = () => { toggle.innerHTML = vid.muted ? '🔇' : '🔊'; };
          const handler = (e) => { e.stopPropagation(); vid.muted = !vid.muted; updateIcon(); };
          toggle.replaceWith(toggle.cloneNode(true)); const fresh = slideElement.querySelector('.sound-toggle'); if(fresh) fresh.addEventListener('click', handler);
          vid.muted = true; updateIcon();
        }
        if(activeThumb) activeThumb.classList.remove('active-thumb');
        if(thumbEl) thumbEl.classList.add('active-thumb');
        activeThumb = thumbEl;
      }
      gallery.forEach((item, idx) => {
        const thumb = document.createElement('div'); thumb.className = 'thumbnail';
        if(idx===0) thumb.classList.add('active-thumb');
        if(item.type === 'image') {
          const img = document.createElement('img'); img.src = item.url; thumb.appendChild(img);
        } else {
          const vid = document.createElement('video'); vid.src = item.url; vid.muted = true; vid.loop = true;
          vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; thumb.appendChild(vid);
          thumb.addEventListener('mouseenter', () => vid.play().catch(e=>{}));
          thumb.addEventListener('mouseleave', () => { vid.pause(); vid.currentTime=0; });
        }
        thumb.addEventListener('click', (e) => { e.stopPropagation(); setMainMedia(item, thumb); });
        thumbStrip.appendChild(thumb);
      });
      if(gallery.length) setMainMedia(gallery[0], thumbStrip.querySelector('.thumbnail'));
    }
    
    function loadMoreProperties() {
      if(isLoading) return;
      isLoading = true;
      const loadingDiv = document.createElement('div');
      loadingDiv.className = 'loading-indicator';
      loadingDiv.innerText = '🏠 Loading more properties...';
      container.appendChild(loadingDiv);
      
      setTimeout(() => {
        const newCount = Math.floor(Math.random() * 3) + 3;
        for(let i=0; i<newCount; i++) allProperties.push(generateRandomProperty(allProperties.length + i));
        loadingDiv.remove();
        renderSlides();
        isLoading = false;
        showDemoToast(`✨ ${newCount} new properties loaded — keep scrolling!`);
      }, 400);
    }
    
    function getMainVideo(slide) { return slide.querySelector('.hero-media video'); }
    
    function renderSlides() {
      const oldScrollTop = container.scrollTop;
      container.innerHTML = '';
      allProperties.forEach(prop => {
        const { slideDiv, heroDiv, thumbStrip } = createSlide(prop);
        container.appendChild(slideDiv);
        buildThumbnails(thumbStrip, prop.gallery, slideDiv, heroDiv);
      });
      attachObservers();
      setTimeout(() => { container.scrollTop = oldScrollTop; }, 50);
    }
    
    function attachObservers() {
      const slides = document.querySelectorAll('.slide');
      if(observer) observer.disconnect();
      observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          const vid = getMainVideo(entry.target);
          if(!vid) return;
          entry.isIntersecting ? vid.play().catch(e=>{}) : (!vid.paused && vid.pause());
        });
      }, { root: container, threshold: 0.55 });
      slides.forEach(s => observer.observe(s));
      
      let scrollTimeout;
      const onScroll = () => {
        if(scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          let bestSlide = null, bestRatio = 0;
          slides.forEach(slide => {
            const rect = slide.getBoundingClientRect();
            const cr = container.getBoundingClientRect();
            const visible = Math.min(rect.bottom, cr.bottom) - Math.max(rect.top, cr.top);
            const ratio = visible / rect.height;
            if(ratio > bestRatio) { bestRatio = ratio; bestSlide = slide; }
          });
          if(bestSlide && bestRatio > 0.5) {
            const vid = getMainVideo(bestSlide);
            if(vid && vid.paused) vid.play().catch(e=>{});
            const idx = Array.from(slides).indexOf(bestSlide);
            if(idx >= slides.length - 3 && !isLoading) loadMoreProperties();
          }
        }, 100);
      };
      container.removeEventListener('scroll', onScroll);
      container.addEventListener('scroll', onScroll);
    }
    
    // ========== DOUBLE TAP TO TOGGLE SEARCH & BOTTOM NAV ==========
    const searchHeader = document.getElementById('searchHeader');
    const bottomNav = document.getElementById('bottomNav');
    let lastTap = 0;
    let uiVisible = true;
    
    function toggleUI() {
      uiVisible = !uiVisible;
      if (uiVisible) {
        searchHeader.classList.remove('hide');
        bottomNav.classList.remove('hide-nav');
        showDemoToast('🔓 Glass UI visible — double tap to hide');
        const hint = document.getElementById('doubleTapHint');
        if (hint) hint.style.opacity = '0.6';
      } else {
        searchHeader.classList.add('hide');
        bottomNav.classList.add('hide-nav');
        showDemoToast('🔒 Glass UI hidden — double tap to show');
        const hint = document.getElementById('doubleTapHint');
        if (hint) hint.style.opacity = '0.3';
      }
    }
    
    // Touch double tap
    document.body.addEventListener('touchend', (e) => {
      const target = e.target;
      const isInteractive = target.closest('.landlord-btn') || target.closest('.thumbnail') || target.closest('.sound-toggle') || target.closest('.nav-item') || target.closest('.search-input') || target.closest('.search-clear') || target.closest('.modal-card');
      if (isInteractive) return;
      
      const now = Date.now();
      const timeSince = now - lastTap;
      if (timeSince < 300 && timeSince > 0) {
        e.preventDefault();
        toggleUI();
      }
      lastTap = now;
    });
    
    // Desktop double click
    container.addEventListener('dblclick', (e) => {
      const isInteractive = e.target.closest('.landlord-btn') || e.target.closest('.thumbnail') || e.target.closest('.sound-toggle') || e.target.closest('.nav-item');
      if (!isInteractive) {
        e.preventDefault();
        toggleUI();
      }
    });
    
    // Search filter
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('searchClearBtn');
    const noToast = document.getElementById('noResultsToast');
    function filterProps() {
      const term = searchInput.value.trim().toLowerCase();
      let visible = 0;
      document.querySelectorAll('.slide').forEach(slide => {
        const city = (slide.getAttribute('data-city') || '').toLowerCase();
        const prop = (slide.getAttribute('data-property') || '').toLowerCase();
        const price = slide.getAttribute('data-price') || '';
        const title = slide.querySelector('.property-title')?.innerText.toLowerCase() || '';
        let match = term === '' || city.includes(term) || prop.includes(term) || title.includes(term) || price.includes(term);
        slide.style.display = match ? 'flex' : 'none';
        if(match) visible++;
      });
      if(term !== '' && visible === 0) { noToast.classList.add('show-toast'); setTimeout(() => noToast.classList.remove('show-toast'), 2000); }
    }
    searchInput.addEventListener('input', () => { filterProps(); clearBtn.classList.toggle('visible', searchInput.value.length > 0); });
    clearBtn.addEventListener('click', () => { searchInput.value = ''; filterProps(); clearBtn.classList.remove('visible'); searchInput.focus(); });
    
    // Navigation clicks
    document.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('click', () => {
        const type = item.getAttribute('data-nav');
        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
        item.classList.add('active');
        if(type === 'home') container.scrollTo({ top: 0, behavior: 'smooth' });
        showDemoToast(type === 'home' ? '🏠 Infinite scroll active' : type === 'chat' ? '💬 Chat with landlords' : `✨ ${type} section`);
      });
    });
    document.querySelector('.nav-item[data-nav="home"]')?.classList.add('active');
    
    // Auto-fade hint
    setTimeout(() => {
      const hint = document.getElementById('doubleTapHint');
      if (hint) hint.style.opacity = '0.5';
    }, 5000);
    
    renderSlides();
    setTimeout(() => { const fv = document.querySelector('.hero-media video'); if(fv) fv.play().catch(e=>{}); }, 500);
  })();
</script>
</body>
</html>