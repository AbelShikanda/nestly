// ========== KENYAN DATA GENERATION ==========
const landlordNames = ["James Mwangi", "Grace Achieng", "Peter Omondi", "Fatma Hassan", "John Kariuki", "Lucy Wanjiku"];
const landlordSuffix = ["Properties", "Real Estate", "Homes Ltd"];
const kenyanLocations = ["Nairobi, Kilimani", "Nairobi, Westlands", "Nairobi, Karen", "Mombasa, Nyali", "Kisumu, Milimani", "Kiambu, Thika Road"];
const propertyTypes = ["Modern Apartment", "Spacious Villa", "Executive Townhouse", "Cozy Bungalow"];
const bedrooms = ["2 bed", "3 bed", "4 bed"];
const bathrooms = ["2 bath", "3 bath"];
const sqft = ["1,200 sqft", "1,800 sqft", "2,500 sqft"];
const priceRanges = [18500000, 22000000, 28500000, 7500000, 12500000];
const imagePool = ["https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg", "https://images.pexels.com/photos/2587054/pexels-photo-2587054.jpeg", "https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg"];
const videoPool = ["https://storage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4", "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFunflies.mp4"];

function getRandomItem(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
function formatKES(price) { return "KES " + price.toLocaleString('en-KE'); }

function getRandomLandlord() {
    const name = getRandomItem(landlordNames);
    return {
        name: name,
        company: `${name.split(' ')[0]} ${getRandomItem(landlordSuffix)}`,
        phone: `+2547${Math.floor(Math.random() * 90000000 + 10000000)}`,
        email: `${name.toLowerCase().replace(' ', '.')}@ke.realestate`,
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
        landlord: getRandomLandlord(),
        description: `Beautiful ${getRandomItem(propertyTypes).toLowerCase()} located in ${location}. Features modern finishes, ample parking, 24/7 security.`
    };
}

// Global state
let allProperties = [];
for (let i = 0; i < 6; i++) allProperties.push(generateRandomProperty(i));
let conversations = [];
let activeChatId = null;
let currentUserRole = "tenant";
let currentPlan = "Free";

// DOM Elements
const container = document.getElementById('reelsContainer');
const searchInput = document.getElementById('searchInput');
const clearBtn = document.getElementById('searchClearBtn');
const noToast = document.getElementById('noResultsToast');
const searchHeader = document.getElementById('searchHeader');
const bottomNav = document.getElementById('bottomNav');

// Utility functions
function showDemoToast(msg) {
    const toast = document.getElementById('demoToast');
    toast.innerText = msg;
    toast.classList.add('show-toast');
    setTimeout(() => toast.classList.remove('show-toast'), 1500);
}

// ========== SLIDE GENERATION ==========
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
    landlordBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!isAuthenticated) {
            showDemoToast('🔐 Please login to chat with landlords');
            setTimeout(() => window.location.href = '/login', 1500);
            return;
        }
        let conv = conversations.find(c => c.landlordName === property.landlord.name);
        if (!conv) { 
            const newId = conversations.length; 
            conversations.push({ id: newId, landlordName: property.landlord.name, landlordAvatar: property.landlord.name.charAt(0), landlordPhone: property.landlord.phone, messages: [] }); 
            conv = conversations[newId]; 
            updateChatBadge(); 
        }
        openChatWindow(conv.id);
    });
    slideDiv.appendChild(landlordBtn);
    
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.innerHTML = `<div class="badge">🇰🇪 ${property.location.includes('Nairobi') ? 'NAIROBI' : property.location.includes('Mombasa') ? 'MOMBASA' : 'KENYA'}</div><div class="property-title">${property.title}</div><div class="property-detail">📍 ${property.location} | ${property.beds} · ${property.baths} · ${property.area}</div><div class="price">${property.priceFormatted}</div>`;
    slideDiv.appendChild(overlay);
    
    overlay.querySelector('.property-title').addEventListener('click', () => openDetailView(property));
    
    const thumbStrip = document.createElement('div');
    thumbStrip.className = 'thumbnail-strip';
    slideDiv.appendChild(thumbStrip);
    return { slideDiv, heroDiv, thumbStrip };
}

function buildThumbnails(thumbStrip, gallery, slideElement, heroDiv) {
    if (!gallery.length) return;
    const labelDiv = document.createElement('div'); 
    labelDiv.className = 'thumb-label'; 
    labelDiv.innerText = '📸'; 
    labelDiv.style.cssText = 'text-align:center;font-size:9px;color:#facc15;margin-bottom:4px;';
    thumbStrip.appendChild(labelDiv);
    let activeThumb = null;
    
    function setMainMedia(item, thumbEl) {
        heroDiv.innerHTML = '';
        if (item.type === 'image') { 
            const img = document.createElement('img'); 
            img.src = item.url; 
            img.style.cssText = 'width:100%;height:100%;object-fit:cover'; 
            heroDiv.appendChild(img); 
        } else { 
            const vid = document.createElement('video'); 
            vid.src = item.url; 
            vid.autoplay = true; 
            vid.muted = true; 
            vid.loop = true; 
            vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; 
            heroDiv.appendChild(vid); 
            vid.play().catch(e=>{}); 
            let toggle = slideElement.querySelector('.sound-toggle'); 
            if (!toggle) { 
                const btn = document.createElement('button'); 
                btn.className = 'sound-toggle'; 
                btn.innerHTML = '🔇'; 
                slideElement.appendChild(btn); 
                toggle = btn; 
            } 
            const updateIcon = () => { toggle.innerHTML = vid.muted ? '🔇' : '🔊'; }; 
            const handler = (e) => { 
                e.stopPropagation(); 
                vid.muted = !vid.muted; 
                updateIcon(); 
            }; 
            toggle.replaceWith(toggle.cloneNode(true)); 
            const fresh = slideElement.querySelector('.sound-toggle'); 
            if(fresh) fresh.addEventListener('click', handler); 
            vid.muted = true; 
            updateIcon(); 
        }
        if(activeThumb) activeThumb.classList.remove('active-thumb');
        if(thumbEl) thumbEl.classList.add('active-thumb');
        activeThumb = thumbEl;
    }
    
    gallery.forEach((item, idx) => {
        const thumb = document.createElement('div'); 
        thumb.className = 'thumbnail';
        if(idx===0) thumb.classList.add('active-thumb');
        if(item.type === 'image') {
            const img = document.createElement('img'); 
            img.src = item.url; 
            thumb.appendChild(img);
        } else {
            const vid = document.createElement('video'); 
            vid.src = item.url; 
            vid.muted = true; 
            vid.loop = true; 
            vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; 
            thumb.appendChild(vid);
            thumb.addEventListener('mouseenter', () => vid.play().catch(e=>{}));
            thumb.addEventListener('mouseleave', () => { vid.pause(); vid.currentTime=0; });
        }
        thumb.addEventListener('click', (e) => { 
            e.stopPropagation(); 
            setMainMedia(item, thumb); 
        });
        thumbStrip.appendChild(thumb);
    });
    if(gallery.length) setMainMedia(gallery[0], thumbStrip.querySelector('.thumbnail'));
}

// ========== CHAT SYSTEM ==========
function initConversations() {
    const uniqueLandlords = [];
    allProperties.forEach(prop => {
        if (!uniqueLandlords.find(l => l.name === prop.landlord.name)) {
            uniqueLandlords.push({ name: prop.landlord.name, avatar: prop.landlord.name.charAt(0), phone: prop.landlord.phone });
        }
    });
    conversations = uniqueLandlords.map((l, idx) => ({
        id: idx,
        landlordName: l.name,
        landlordAvatar: l.avatar,
        landlordPhone: l.phone,
        messages: [{ text: `Hi! I'm ${l.name}. Interested in my property?`, sender: 'landlord', time: new Date().toLocaleTimeString() }]
    }));
    updateChatBadge();
}

function updateChatBadge() {
    const badge = document.getElementById('chatBadge');
    if (badge) badge.textContent = conversations.length;
}

function renderChatList() {
    const listContainer = document.getElementById('chatListContainer');
    if (!listContainer) return;
    listContainer.innerHTML = '';
    conversations.forEach(conv => {
        const lastMsg = conv.messages[conv.messages.length - 1];
        const div = document.createElement('div');
        div.className = 'chatlist-item';
        div.innerHTML = `<div class="chatlist-avatar">${conv.landlordAvatar}</div><div class="chatlist-info"><div class="chatlist-name">${conv.landlordName}</div><div class="chatlist-preview">${lastMsg.text.substring(0, 40)}</div></div><div class="chatlist-time">${lastMsg.time}</div>`;
        div.addEventListener('click', () => openChatWindow(conv.id));
        listContainer.appendChild(div);
    });
}

function openChatWindow(convId) {
    activeChatId = convId;
    const conv = conversations.find(c => c.id === convId);
    if (!conv) return;
    document.getElementById('chatName').innerText = conv.landlordName;
    document.getElementById('chatAvatar').innerHTML = conv.landlordAvatar;
    renderChatMessages(convId);
    document.getElementById('chatlistModal').classList.remove('active');
    document.getElementById('chatModal').classList.add('active');
}

function renderChatMessages(convId) {
    const conv = conversations.find(c => c.id === convId);
    if (!conv) return;
    const containerMsg = document.getElementById('chatMessages');
    containerMsg.innerHTML = '';
    conv.messages.forEach(msg => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${msg.sender === 'user' ? 'sent' : 'received'}`;
        msgDiv.innerText = msg.text;
        containerMsg.appendChild(msgDiv);
    });
    containerMsg.scrollTop = containerMsg.scrollHeight;
}

function sendMessage(convId, text) {
    if (!text.trim()) return;
    const conv = conversations.find(c => c.id === convId);
    if (conv) {
        conv.messages.push({ text: text, sender: 'user', time: new Date().toLocaleTimeString() });
        renderChatMessages(convId);
        setTimeout(() => {
            conv.messages.push({ text: `Thanks for your interest! I'll get back to you shortly.`, sender: 'landlord', time: new Date().toLocaleTimeString() });
            renderChatMessages(convId);
            showDemoToast(`📩 New reply from ${conv.landlordName}`);
            updateChatBadge();
        }, 1000);
    }
    document.getElementById('chatInput').value = '';
}

// ========== DETAIL VIEW ==========
const detailOverlay = document.getElementById('detailOverlay');
const detailScroll = document.getElementById('detailScroll');

function openDetailView(property) {
    detailScroll.innerHTML = `
        <div class="detail-hero" id="detailHero"></div>
        <div class="detail-thumbnails" id="detailThumbnails"></div>
        <div class="detail-info">
          <div class="detail-title">${property.title}</div>
          <div class="detail-location" style="color:#aaa; margin:8px 0">📍 ${property.location}</div>
          <div class="detail-specs" style="display:flex; gap:16px; margin:12px 0">🛏️ ${property.beds} &nbsp; 🛁 ${property.baths} &nbsp; 📏 ${property.area}</div>
          <div class="detail-price">${property.priceFormatted}</div>
          <div class="detail-desc" style="color:#bbb">${property.description}</div>
          <div class="action-buttons"><button class="chat-btn" id="detailChatBtn">💬 Chat with ${property.landlord.name}</button><button class="close-detail-btn" id="closeDetailBtn">← Back</button></div>
        </div>
    `;
    const hero = document.getElementById('detailHero');
    const thumbsDiv = document.getElementById('detailThumbnails');
    let activeThumb = null;
    
    function setMedia(item, el) {
        hero.innerHTML = '';
        if (item.type === 'image') { 
            const img = document.createElement('img'); 
            img.src = item.url; 
            hero.appendChild(img); 
        } else { 
            const vid = document.createElement('video'); 
            vid.src = item.url; 
            vid.autoplay = true; 
            vid.muted = true; 
            vid.loop = true; 
            vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; 
            hero.appendChild(vid); 
            vid.play().catch(e=>{}); 
        }
        if (activeThumb) activeThumb.classList.remove('active-detail-thumb');
        if (el) el.classList.add('active-detail-thumb');
        activeThumb = el;
    }
    
    property.gallery.forEach((item, idx) => {
        const thumb = document.createElement('div'); 
        thumb.className = 'detail-thumb';
        if (idx === 0) thumb.classList.add('active-detail-thumb');
        if (item.type === 'image') { 
            const img = document.createElement('img'); 
            img.src = item.url; 
            thumb.appendChild(img); 
        } else { 
            const vid = document.createElement('video'); 
            vid.src = item.url; 
            vid.muted = true; 
            vid.loop = true; 
            vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; 
            thumb.appendChild(vid); 
            thumb.addEventListener('mouseenter', () => vid.play().catch(e=>{}));
            thumb.addEventListener('mouseleave', () => { vid.pause(); vid.currentTime=0; });
        }
        thumb.addEventListener('click', () => setMedia(item, thumb));
        thumbsDiv.appendChild(thumb);
    });
    if (property.gallery.length) setMedia(property.gallery[0], thumbsDiv.children[0]);
    
    document.getElementById('closeDetailBtn').addEventListener('click', () => detailOverlay.classList.remove('active'));
    document.getElementById('detailChatBtn').addEventListener('click', () => {
        detailOverlay.classList.remove('active');
        if (!isAuthenticated) {
            showDemoToast('🔐 Please login to chat with landlords');
            setTimeout(() => window.location.href = '/login', 1500);
            return;
        }
        let conv = conversations.find(c => c.landlordName === property.landlord.name);
        if (!conv) {
            const newId = conversations.length;
            conversations.push({ id: newId, landlordName: property.landlord.name, landlordAvatar: property.landlord.name.charAt(0), landlordPhone: property.landlord.phone, messages: [{ text: `Hello! I'm interested in ${property.title}`, sender: 'user', time: new Date().toLocaleTimeString() }] });
            conv = conversations[newId];
            updateChatBadge();
        }
        openChatWindow(conv.id);
    });
    detailOverlay.classList.add('active');
}

detailOverlay.addEventListener('click', (e) => {
    if (e.target === detailOverlay) {
        detailOverlay.classList.remove('active');
        const videos = detailScroll.querySelectorAll('video');
        videos.forEach(v => v.pause());
    }
});

// ========== RENDER SLIDES ==========
let isLoading = false;
let observer = null;

function getMainVideo(slide) { return slide.querySelector('.hero-media video'); }

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
        showDemoToast(`✨ ${newCount} new properties loaded`);
    }, 400);
}

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

// ========== PROFILE MODAL ==========
const profileModal = document.getElementById('profileModal');
function openProfile() {
    document.getElementById('profileName').innerText = currentUserRole === 'landlord' ? 'Landlord User' : 'Tenant User';
    document.getElementById('profileRole').innerHTML = currentUserRole === 'landlord' ? 'Landlord · Premium ready' : 'Tenant · Looking for home';
    document.getElementById('currentPlan').innerText = currentPlan;
    profileModal.classList.add('active');
}
function closeProfile() { profileModal.classList.remove('active'); }
document.getElementById('closeProfileBtn')?.addEventListener('click', closeProfile);
document.getElementById('supportTicketBtn')?.addEventListener('click', () => { showDemoToast('📧 Support ticket submitted! We\'ll respond within 24 hours.'); closeProfile(); });
document.querySelectorAll('.plan-card').forEach(card => {
    card.addEventListener('click', () => {
        const plan = card.getAttribute('data-plan');
        let price = '';
        if(plan === 'standard') price = 'KES 499/month';
        else if(plan === 'gold') price = 'KES 999/month';
        else price = 'KES 1,999/month';
        showDemoToast(`✨ Upgraded to ${plan.toUpperCase()} plan (${price}) — M-Pesa Paybill: 123456`);
        currentPlan = plan.charAt(0).toUpperCase() + plan.slice(1);
        document.getElementById('currentPlan').innerText = currentPlan;
        setTimeout(() => closeProfile(), 1000);
    });
});

// ========== NAVIGATION ==========
document.querySelector('.nav-item[data-nav="explore"]')?.addEventListener('click', () => {
    container.scrollTo({ top: 0, behavior: 'smooth' });
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.querySelector('.nav-item[data-nav="explore"]').classList.add('active');
});
document.querySelector('.nav-item[data-nav="profile"]')?.addEventListener('click', () => {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.querySelector('.nav-item[data-nav="profile"]').classList.add('active');
    openProfile();
});
document.querySelector('.nav-item[data-nav="explore"]')?.classList.add('active');

// ========== DOUBLE TAP TO TOGGLE UI ==========
let lastTap = 0;
let uiVisible = true;

function toggleUI() {
    uiVisible = !uiVisible;
    if (uiVisible) {
        searchHeader.classList.remove('hide');
        bottomNav.classList.remove('hide-nav');
        showDemoToast('🔓 UI visible — double tap to hide');
        const hint = document.getElementById('doubleTapHint');
        if (hint) hint.style.opacity = '0.6';
    } else {
        searchHeader.classList.add('hide');
        bottomNav.classList.add('hide-nav');
        showDemoToast('🔒 UI hidden — double tap to show');
        const hint = document.getElementById('doubleTapHint');
        if (hint) hint.style.opacity = '0.3';
    }
}

document.body.addEventListener('touchend', (e) => {
    if (e.target.closest('.detail-card') || e.target.closest('.landlord-btn') || e.target.closest('.thumbnail') || e.target.closest('.nav-item') || e.target.closest('.chat-modal') || e.target.closest('.chatlist-modal') || e.target.closest('.profile-modal')) return;
    const now = Date.now();
    if (now - lastTap < 300 && now - lastTap > 0) {
        e.preventDefault();
        toggleUI();
    }
    lastTap = now;
});

container.addEventListener('dblclick', (e) => {
    if (!e.target.closest('.detail-card') && !e.target.closest('.chat-modal') && !e.target.closest('.chatlist-modal') && !e.target.closest('.profile-modal')) {
        e.preventDefault();
        toggleUI();
    }
});

// ========== SEARCH FILTER ==========
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

// ========== CHAT EVENT HANDLERS ==========
document.getElementById('closeChatBtn')?.addEventListener('click', () => {
    document.getElementById('chatModal').classList.remove('active');
    renderChatList();
    document.getElementById('chatlistModal').classList.add('active');
});
document.getElementById('chatlistBackBtn')?.addEventListener('click', () => {
    document.getElementById('chatlistModal').classList.remove('active');
    showDemoToast('🏠 Continue exploring properties');
});
document.getElementById('chatSendBtn')?.addEventListener('click', () => {
    if (activeChatId !== null) sendMessage(activeChatId, document.getElementById('chatInput').value);
});
document.getElementById('chatInput')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); if (activeChatId !== null) sendMessage(activeChatId, document.getElementById('chatInput').value); }
});

const notifBtn = document.getElementById('notificationBtn');
const chatlistModal = document.getElementById('chatlistModal');
notifBtn?.addEventListener('click', () => {
    renderChatList();
    chatlistModal.classList.add('active');
});
chatlistModal?.addEventListener('click', (e) => { if (e.target === chatlistModal) chatlistModal.classList.remove('active'); });

// ========== ADD LISTING BUTTON ==========
const addBtn = document.getElementById('addListingBtn');
addBtn?.addEventListener('click', () => { showDemoToast('➕ Add new listing form (coming soon)'); });

// ========== INITIALIZE ==========
setTimeout(() => {
    const hint = document.getElementById('doubleTapHint');
    if (hint) hint.style.opacity = '0.5';
}, 5000);

initConversations();
renderSlides();
setTimeout(() => { const fv = document.querySelector('.hero-media video'); if(fv) fv.play().catch(e=>{}); }, 500);