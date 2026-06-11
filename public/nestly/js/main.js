// ============================================================
// NESTLY REAL ESTATE - TRUE INFINITE SCROLL (FIXED)
// ============================================================

// ============================================================
// 1. GLOBAL STATE
// ============================================================
let allProperties = [];
let currentPage = 1;
let isLoading = false;
let hasMorePages = true;
let observer = null;
let loopCount = 0;
let hasTriggeredLoop = false;
let scrollCheckInterval = null;
let scrollTimeout = null;

// Chat system
let conversations = [];
let activeChatId = null;

// User data
let currentUser = null;
let isAuthenticated = false;
let currentUserRole = 'tenant';
let currentPlan = 'Free';

// DOM Elements
let container = null;
let searchInput = null;
let clearBtn = null;
let noToast = null;
let searchHeader = null;
let bottomNav = null;
let detailOverlay = null;
let detailScroll = null;
let profileModal = null;

// ============================================================
// 2. WAIT FOR DOM
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    container = document.getElementById('reelsContainer');
    searchInput = document.getElementById('searchInput');
    clearBtn = document.getElementById('searchClearBtn');
    noToast = document.getElementById('noResultsToast');
    searchHeader = document.getElementById('searchHeader');
    bottomNav = document.getElementById('bottomNav');
    detailOverlay = document.getElementById('detailOverlay');
    detailScroll = document.getElementById('detailScroll');
    profileModal = document.getElementById('profileModal');
    
    // Verify critical elements exist
    if (!container) {
        console.error('CRITICAL: reelsContainer not found!');
        return;
    }
    
    // Check container has proper scroll styling
    const containerStyle = window.getComputedStyle(container);
    if (containerStyle.overflowY !== 'auto' && containerStyle.overflowY !== 'scroll') {
        console.warn('WARNING: reelsContainer missing overflow-y:auto - adding it now');
        container.style.height = '100vh';
        container.style.overflowY = 'auto';
        container.style.scrollSnapType = 'y mandatory';
    }
    
    if (typeof window.currentUser !== 'undefined') {
        currentUser = window.currentUser;
        isAuthenticated = window.isAuthenticated || false;
        currentUserRole = currentUser?.role || 'tenant';
        currentPlan = currentUser?.subscription_plan || 'Free';
        console.log('User data loaded');
    }
    
    setupEventListeners();
    
    if (container) {
        fetchProperties(true);
    }
});

// ============================================================
// 3. UTILITY FUNCTIONS
// ============================================================

function showDemoToast(msg) {
    const toast = document.getElementById('demoToast');
    if (!toast) return;
    toast.innerText = msg;
    toast.classList.add('show-toast');
    setTimeout(() => toast.classList.remove('show-toast'), 2000);
}

function formatKES(price) {
    return "KES " + Number(price).toLocaleString('en-KE');
}

function getMainVideo(slide) {
    return slide.querySelector('.hero-media video');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function getImageUrl(url) {
    if (!url) return 'https://placehold.co/800x600/1a1a2e/facc15?text=No+Image';
    if (url.startsWith('http')) return url;
    if (url.startsWith('/storage')) return url;
    return '/storage/' + url.replace(/^\/?storage\//, '');
}

// ============================================================
// 4. INFINITE LOOP - ADD MORE PROPERTIES (FIXED)
// ============================================================

function addMorePropertiesForInfiniteScroll() {
    if (isLoading) {
        console.log('⏸️ Already loading, skipping loop');
        return;
    }
    
    if (allProperties.length === 0) {
        console.log('⚠️ No properties to clone');
        return;
    }
    
    isLoading = true;
    loopCount++;
    hasTriggeredLoop = true;
    
    console.log(`🔄 INFINITE LOOP #${loopCount} - Adding more properties...`);
    
    // FIXED: Take more properties and handle edge cases
    const propertiesToClone = allProperties.length >= 6 
        ? allProperties.slice(0, 6) 
        : allProperties; // Take all if less than 6
    
    if (propertiesToClone.length === 0) {
        console.error('❌ No properties to clone after check');
        isLoading = false;
        return;
    }
    
    const newProperties = [];
    const timestamp = Date.now();
    
    propertiesToClone.forEach((prop, index) => {
        // Create 2 copies of each property for better infinite feel
        for (let copy = 0; copy < 2; copy++) {
            newProperties.push({
                ...prop,
                id: prop.id + timestamp + index + copy + (loopCount * 10000),
                title: prop.title,
                badge: prop.badge,
                priceFormatted: prop.priceFormatted,
                description: prop.description,
                main_image: prop.main_image,
                mainMedia: prop.mainMedia,
                gallery: prop.gallery,
                location: prop.location,
                beds: prop.beds,
                baths: prop.baths,
                bedrooms: prop.bedrooms,
                bathrooms: prop.bathrooms,
                area: prop.area,
                price: prop.price,
                landlord: prop.landlord,
                is_verified: prop.is_verified,
                landlord_name: prop.landlord_name,
            });
        }
    });
    
    // Add to allProperties
    allProperties.push(...newProperties);
    
    // Render new slides in batches to prevent UI freeze
    const batchSize = 5;
    let index = 0;
    
    function renderBatch() {
        const end = Math.min(index + batchSize, newProperties.length);
        for (let i = index; i < end; i++) {
            const slide = createSlide(newProperties[i]);
            container.appendChild(slide);
        }
        index = end;
        
        if (index < newProperties.length) {
            setTimeout(renderBatch, 10);
        } else {
            // Attach observers to new slides
            attachObserversToNewSlides();
            isLoading = false;
            console.log(`✅ Added ${newProperties.length} more properties. Total: ${allProperties.length}`);
            showDemoToast(`✨ ${newProperties.length} more properties loaded!`);
        }
    }
    
    renderBatch();
}

// ============================================================
// 5. API CALLS (FIXED)
// ============================================================

async function fetchProperties(reset = false) {
    if (isLoading) {
        console.log('Already loading, skipping fetch');
        return;
    }
    
    if (!hasMorePages && !reset) {
        console.log('No more API pages');
        return;
    }
    
    isLoading = true;
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loadingIndicator';
    loadingDiv.innerHTML = '🏠 Loading properties...';
    loadingDiv.style.cssText = 'text-align:center; padding:20px; color:#facc15; background:rgba(0,0,0,0.5); position:sticky; bottom:0; z-index:50;';
    if (container) container.appendChild(loadingDiv);
    
    try {
        const url = `/api/properties?page=${currentPage}&per_page=6`;
        console.log('Fetching page:', currentPage);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (loadingDiv) loadingDiv.remove();
        
        if (data.success && data.data && data.data.length > 0) {
            if (reset) {
                allProperties = [];
                currentPage = 1;
                hasMorePages = true;
                loopCount = 0;
                hasTriggeredLoop = false;
                if (container) container.innerHTML = '';
            }
            
            // Format properties
            const newProperties = data.data.map(prop => {
                let mainImage = prop.mainMedia?.url || prop.main_image;
                if (mainImage && !mainImage.startsWith('http') && !mainImage.startsWith('/storage')) {
                    mainImage = '/storage/' + mainImage.replace(/^\/?storage\//, '');
                }
                
                const gallery = (prop.gallery || []).map(img => {
                    let imgUrl = img.url;
                    if (imgUrl && !imgUrl.startsWith('http') && !imgUrl.startsWith('/storage')) {
                        imgUrl = '/storage/' + imgUrl.replace(/^\/?storage\//, '');
                    }
                    return { ...img, url: imgUrl };
                });
                
                return {
                    ...prop,
                    main_image: mainImage,
                    mainMedia: { url: mainImage, type: 'image' },
                    gallery: gallery.length ? gallery : [{ url: mainImage, type: 'image' }],
                    priceFormatted: prop.priceFormatted || formatKES(prop.price),
                    badge: prop.badge || (prop.location?.toLowerCase().includes('nairobi') ? 'NAIROBI' : 'KENYA')
                };
            });
            
            allProperties.push(...newProperties);
            
            // FIXED: Proper pagination logic
            if (data.current_page && data.last_page) {
                hasMorePages = data.current_page < data.last_page;
            } else {
                hasMorePages = newProperties.length === 6; // Assume more if got full page
            }
            
            currentPage++;
            
            if (reset) {
                renderAllSlides();
                console.log(`✅ Loaded ${newProperties.length} properties from API`);
                showDemoToast(`✨ ${newProperties.length} properties loaded`);
            } else {
                appendNewSlides(newProperties);
                console.log(`✅ Loaded ${newProperties.length} more properties from API`);
            }
        } else if (reset && container && window.initialProperties?.length) {
            console.log('Using initial properties fallback');
            allProperties = window.initialProperties;
            renderAllSlides();
            showDemoToast(`✨ ${allProperties.length} properties loaded`);
            hasMorePages = false;
        } else if (reset && container) {
            container.innerHTML = '<div style="text-align:center; padding:60px; color:white;">🏠 No properties found.</div>';
            hasMorePages = false;
        }
        
        // FIXED: Only trigger loop if we actually exhausted API pages
        if (!hasMorePages && allProperties.length > 0 && !hasTriggeredLoop && !reset) {
            console.log('📱 No more API pages - Starting infinite loop mode...');
            setTimeout(() => {
                addMorePropertiesForInfiniteScroll();
            }, 500);
        }
        
    } catch (error) {
        console.error('Error fetching properties:', error);
        if (loadingDiv) loadingDiv.remove();
        if (reset && container) {
            container.innerHTML = `<div style="text-align:center; padding:60px; color:white;">
                ⚠️ Failed to load properties.<br>
                <button onclick="location.reload()" style="margin-top:20px; background:#facc15; padding:10px 20px; border-radius:40px; border:none; cursor:pointer;">
                    Retry
                </button>
            </div>`;
        }
    } finally {
        isLoading = false;
    }
}

async function loadMoreProperties() {
    if (!hasMorePages || isLoading) {
        console.log(`Cannot load: hasMorePages=${hasMorePages}, isLoading=${isLoading}`);
        return;
    }
    await fetchProperties(false);
}

// ============================================================
// 6. RENDER SLIDES
// ============================================================

function renderAllSlides() {
    if (!container) return;
    container.innerHTML = '';
    allProperties.forEach(property => {
        container.appendChild(createSlide(property));
    });
    attachObservers();
}

function appendNewSlides(newProperties) {
    if (!container) return;
    newProperties.forEach(property => {
        container.appendChild(createSlide(property));
    });
    attachObserversToNewSlides();
}

// ============================================================
// 7. CREATE SLIDE - WITH IMMEDIATE IMAGE LOADING
// ============================================================

function createSlide(property) {
    const slideDiv = document.createElement('div');
    slideDiv.className = 'slide';
    slideDiv.setAttribute('data-city', (property.location || '').toLowerCase());
    slideDiv.setAttribute('data-title', (property.title || '').toLowerCase());
    slideDiv.setAttribute('data-price', property.price || 0);
    
    let imageUrl = property.main_image || property.mainMedia?.url;
    if (!imageUrl || imageUrl === 'null' || imageUrl === 'undefined') {
        imageUrl = 'https://placehold.co/800x600/1a1a2e/facc15?text=No+Image';
    }
    
    const heroDiv = document.createElement('div');
    heroDiv.className = 'hero-media';
    const img = document.createElement('img');
    img.alt = property.title || 'Property';
    img.style.width = '100%';
    img.style.height = '100%';
    img.style.objectFit = 'cover';
    img.loading = 'lazy'; // Add lazy loading for performance
    
    // Force immediate load
    const preloader = new Image();
    preloader.onload = function() { img.src = imageUrl; };
    preloader.onerror = function() { img.src = 'https://placehold.co/800x600/1a1a2e/facc15?text=No+Image'; };
    preloader.src = imageUrl;
    img.src = imageUrl;
    
    heroDiv.appendChild(img);
    slideDiv.appendChild(heroDiv);
    
    // Landlord button
    const landlordBtn = document.createElement('div');
    landlordBtn.className = 'landlord-btn';
    landlordBtn.innerHTML = `
        <div class="landlord-avatar">👤</div>
        <div>
            <div class="landlord-name">${escapeHtml(property.landlord?.name || 'Owner')}</div>
            <div class="landlord-badge">${property.landlord?.verified ? '✓ Verified' : 'Owner'}</div>
        </div>
    `;
    landlordBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!isAuthenticated) {
            showDemoToast('🔐 Please login to chat');
            setTimeout(() => window.location.href = '/login', 1500);
            return;
        }
        showDemoToast(`💬 Chat with ${property.landlord?.name}`);
    });
    slideDiv.appendChild(landlordBtn);
    
    // Overlay
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.innerHTML = `
        <div class="badge">🇰🇪 ${property.badge || 'KENYA'}</div>
        <div class="property-title">${escapeHtml(property.title)}</div>
        <div class="property-detail">📍 ${escapeHtml(property.location)} | ${property.beds || property.bedrooms || 0} bed · ${property.baths || property.bathrooms || 0} bath</div>
        <div class="price">${property.priceFormatted || formatKES(property.price)}</div>
    `;
    slideDiv.appendChild(overlay);
    
    overlay.querySelector('.property-title').addEventListener('click', () => openDetailView(property));
    
    // Thumbnail strip
    const thumbStrip = document.createElement('div');
    thumbStrip.className = 'thumbnail-strip';
    const gallery = property.gallery || [];
    if (gallery.length > 0) {
        gallery.forEach((item, idx) => {
            const thumb = document.createElement('div');
            thumb.className = 'thumbnail' + (idx === 0 ? ' active-thumb' : '');
            const thumbImg = document.createElement('img');
            thumbImg.src = item.url;
            thumbImg.alt = 'thumbnail';
            thumb.appendChild(thumbImg);
            thumb.addEventListener('click', (e) => {
                e.stopPropagation();
                img.src = item.url;
                thumbStrip.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active-thumb'));
                thumb.classList.add('active-thumb');
            });
            thumbStrip.appendChild(thumb);
        });
    }
    slideDiv.appendChild(thumbStrip);
    
    return slideDiv;
}

// ============================================================
// 8. DETAIL VIEW (Simplified)
// ============================================================

function openDetailView(property) {
    if (!detailOverlay || !detailScroll) {
        showDemoToast(`📱 ${property.title}`);
        return;
    }
    
    let mainImageUrl = property.main_image || property.mainMedia?.url;
    if (!mainImageUrl) mainImageUrl = 'https://placehold.co/800x600/1a1a2e/facc15?text=No+Image';
    
    let galleryHtml = '';
    const gallery = property.gallery || [];
    if (gallery.length > 0) {
        galleryHtml = '<div style="display:flex; gap:12px; overflow-x:auto; padding:8px 0; margin-bottom:20px;">';
        gallery.forEach((item, idx) => {
            galleryHtml += `
                <div class="detail-thumb" data-url="${item.url}" style="width:70px; height:70px; border-radius:16px; overflow:hidden; cursor:pointer; border:2px solid ${idx === 0 ? '#facc15' : 'rgba(255,255,255,0.3)'}; flex-shrink:0;">
                    <img src="${item.url}" style="width:100%; height:100%; object-fit:cover;">
                </div>
            `;
        });
        galleryHtml += '</div>';
    }
    
    detailScroll.innerHTML = `
        <div style="border-radius:24px; overflow:hidden; margin-bottom:20px; aspect-ratio:16/9; background:#111;">
            <img id="detailMainImage" src="${mainImageUrl}" style="width:100%; height:100%; object-fit:cover;">
        </div>
        ${galleryHtml}
        <div>
            <div style="font-size:1.6rem; font-weight:700; color:white;">${escapeHtml(property.title)}</div>
            <div style="color:#aaa; margin:8px 0">📍 ${escapeHtml(property.location)}</div>
            <div style="display:flex; gap:16px; margin:12px 0; padding:12px 0; border-top:1px solid rgba(255,255,255,0.1); border-bottom:1px solid rgba(255,255,255,0.1);">
                <span>🛏️ ${property.beds || property.bedrooms || 0} bed${property.beds != 1 ? 's' : ''}</span>
                <span>🛁 ${property.baths || property.bathrooms || 0} bath${property.baths != 1 ? 's' : ''}</span>
            </div>
            <div style="font-size:1.7rem; font-weight:800; color:#facc15; margin:16px 0">${property.priceFormatted || formatKES(property.price)}</div>
            <div style="color:#bbb; line-height:1.5;">${escapeHtml(property.description || 'No description available.')}</div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button id="detailChatBtn" style="flex:1; padding:14px; border-radius:60px; background:#facc15; color:#1a1e24; border:none; font-weight:600; cursor:pointer;">💬 Chat with ${escapeHtml(property.landlord?.name || 'Owner')}</button>
                <button id="closeDetailBtn" style="flex:1; padding:14px; border-radius:60px; background:rgba(255,255,255,0.15); color:white; border:none; cursor:pointer;">← Back</button>
            </div>
        </div>
    `;
    
    const mainImg = document.getElementById('detailMainImage');
    if (mainImg) {
        const preloader = new Image();
        preloader.onload = () => mainImg.src = mainImageUrl;
        preloader.src = mainImageUrl;
        mainImg.src = mainImageUrl;
    }
    
    const thumbs = document.querySelectorAll('.detail-thumb');
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const newUrl = this.getAttribute('data-url');
            if (mainImg && newUrl) mainImg.src = newUrl;
            thumbs.forEach(t => t.style.borderColor = 'rgba(255,255,255,0.3)');
            this.style.borderColor = '#facc15';
        });
    });
    
    document.getElementById('closeDetailBtn')?.addEventListener('click', () => detailOverlay.classList.remove('active'));
    document.getElementById('detailChatBtn')?.addEventListener('click', () => {
        detailOverlay.classList.remove('active');
        if (!isAuthenticated) {
            showDemoToast('🔐 Please login to chat');
            setTimeout(() => window.location.href = '/login', 1500);
        } else {
            showDemoToast(`💬 Chat with ${property.landlord?.name}`);
        }
    });
    
    detailOverlay.classList.add('active');
}

if (detailOverlay) {
    detailOverlay.addEventListener('click', (e) => {
        if (e.target === detailOverlay) detailOverlay.classList.remove('active');
    });
}

// ============================================================
// 9. OBSERVERS (FIXED - Added scroll-based infinite scroll)
// ============================================================

function attachObservers() {
    const slides = document.querySelectorAll('.slide');
    if (observer) observer.disconnect();
    
    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const vid = getMainVideo(entry.target);
            if (vid) {
                if (entry.isIntersecting) vid.play().catch(e => {});
                else if (!vid.paused) vid.pause();
            }
        });
    }, { root: container, threshold: 0.55 });
    
    slides.forEach(s => observer.observe(s));
}

function attachObserversToNewSlides() {
    const newSlides = document.querySelectorAll('.slide:not([data-attached])');
    newSlides.forEach(slide => {
        slide.setAttribute('data-attached', 'true');
        const vid = getMainVideo(slide);
        if (vid && observer) observer.observe(slide);
    });
}

// ============================================================
// 10. IMPROVED INFINITE SCROLL - SCROLL EVENT (FIXED)
// ============================================================

function setupInfiniteScroll() {
    if (!container) {
        console.error('Cannot setup infinite scroll: container missing');
        return;
    }
    
    let ticking = false;
    let lastScrollTop = 0;
    
    // Use scroll event instead of interval (more reliable)
    container.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(() => {
                const currentScrollTop = container.scrollTop;
                const scrollBottom = container.scrollTop + container.clientHeight;
                const scrollHeight = container.scrollHeight;
                const isScrollingDown = currentScrollTop > lastScrollTop;
                
                // FIXED: More generous trigger (300px instead of 200)
                // Also removed the isScrollingDown check - load even when bouncing at bottom
                if (scrollBottom >= scrollHeight - 300 && !isLoading) {
                    console.log('📜 Near bottom, loading more...');
                    
                    if (hasMorePages) {
                        loadMoreProperties();
                    } else if (allProperties.length > 0) {
                        addMorePropertiesForInfiniteScroll();
                    }
                }
                
                lastScrollTop = currentScrollTop;
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Optional: Add debounced resize handler
    window.addEventListener('resize', () => {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            // Re-check scroll position on resize
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 300 && !isLoading) {
                if (hasMorePages) {
                    loadMoreProperties();
                } else if (allProperties.length > 0) {
                    addMorePropertiesForInfiniteScroll();
                }
            }
        }, 250);
    });
    
    console.log('✅ Infinite scroll setup complete');
}

// ============================================================
// 11. SEARCH FILTER
// ============================================================

function filterProps() {
    if (!searchInput) return;
    const term = searchInput.value.trim().toLowerCase();
    let visible = 0;
    document.querySelectorAll('.slide').forEach(slide => {
        const city = (slide.getAttribute('data-city') || '').toLowerCase();
        const title = (slide.getAttribute('data-title') || '').toLowerCase();
        const match = term === '' || city.includes(term) || title.includes(term);
        slide.style.display = match ? 'flex' : 'none';
        if (match) visible++;
    });
    if (clearBtn) clearBtn.classList.toggle('visible', searchInput.value.length > 0);
    
    // Show no results message
    if (noToast) {
        if (visible === 0 && term !== '') {
            noToast.classList.add('show-toast');
            setTimeout(() => noToast.classList.remove('show-toast'), 2000);
        }
    }
}

// ============================================================
// 12. NAVIGATION & UI
// ============================================================

function setupEventListeners() {
    const exploreBtn = document.querySelector('.nav-item[data-nav="explore"]');
    if (exploreBtn) {
        exploreBtn.addEventListener('click', () => {
            if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            exploreBtn.classList.add('active');
        });
        exploreBtn.classList.add('active');
    }
    
    const profileBtn = document.querySelector('.nav-item[data-nav="profile"]');
    if (profileBtn) {
        profileBtn.addEventListener('click', () => {
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            profileBtn.classList.add('active');
            if (profileModal) profileModal.classList.add('active');
        });
    }
    
    document.getElementById('closeProfileBtn')?.addEventListener('click', () => {
        if (profileModal) profileModal.classList.remove('active');
    });
    
    document.getElementById('supportTicketBtn')?.addEventListener('click', () => {
        if (!isAuthenticated) {
            showDemoToast('🔐 Please login');
            setTimeout(() => window.location.href = '/login', 1500);
            return;
        }
        window.location.href = '/support/create';
    });
    
    document.querySelectorAll('.plan-card').forEach(card => {
        card.addEventListener('click', () => {
            const plan = card.getAttribute('data-plan');
            showDemoToast(`✨ ${plan.toUpperCase()} plan - Coming soon!`);
        });
    });
    
    if (searchInput) {
        searchInput.addEventListener('input', filterProps);
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                filterProps();
                clearBtn.classList.remove('visible');
                searchInput.focus();
            }
        });
    }
}

// ============================================================
// 13. DOUBLE TAP
// ============================================================

let lastTap = 0;
let uiVisible = true;

function toggleUI() {
    uiVisible = !uiVisible;
    if (searchHeader) searchHeader.classList.toggle('hide', !uiVisible);
    if (bottomNav) bottomNav.classList.toggle('hide-nav', !uiVisible);
    showDemoToast(uiVisible ? '🔓 UI visible' : '🔒 UI hidden');
}

document.body.addEventListener('touchend', (e) => {
    if (e.target.closest('.detail-card') || e.target.closest('.landlord-btn') || e.target.closest('.thumbnail') || e.target.closest('.nav-item')) return;
    const now = Date.now();
    if (now - lastTap < 300 && now - lastTap > 0) {
        e.preventDefault();
        toggleUI();
    }
    lastTap = now;
});

if (container) {
    container.addEventListener('dblclick', (e) => {
        if (!e.target.closest('.detail-card')) toggleUI();
    });
}

// ============================================================
// 14. ADD LISTING BUTTON
// ============================================================

document.getElementById('addListingBtn')?.addEventListener('click', () => {
    if (!isAuthenticated) {
        showDemoToast('🔐 Please login to add a listing');
        setTimeout(() => window.location.href = '/login', 1500);
        return;
    }
    window.location.href = '/landlord/listings/create';
});

// ============================================================
// 15. CHAT EVENT HANDLERS
// ============================================================

document.getElementById('closeChatBtn')?.addEventListener('click', () => {
    document.getElementById('chatModal')?.classList.remove('active');
});

document.getElementById('chatlistBackBtn')?.addEventListener('click', () => {
    document.getElementById('chatlistModal')?.classList.remove('active');
    showDemoToast('🏠 Continue exploring properties');
});

document.getElementById('chatSendBtn')?.addEventListener('click', () => {
    const input = document.getElementById('chatInput');
    if (input && input.value.trim()) {
        showDemoToast('📤 Message sent! (Demo)');
        input.value = '';
    }
});

const notifBtn = document.getElementById('notificationBtn');
const chatlistModal = document.getElementById('chatlistModal');
notifBtn?.addEventListener('click', () => {
    if (chatlistModal) chatlistModal.classList.add('active');
});
chatlistModal?.addEventListener('click', (e) => { 
    if (e.target === chatlistModal) chatlistModal.classList.remove('active'); 
});

// ============================================================
// 16. INITIALIZE
// ============================================================

setTimeout(() => {
    const hint = document.getElementById('doubleTapHint');
    if (hint) hint.style.opacity = '0.5';
}, 5000);

// FIXED: Setup scroll after a slight delay to ensure everything is rendered
setTimeout(() => {
    setupInfiniteScroll();
    console.log('✅ Nestly main.js loaded - INFINITE SCROLL WILL NEVER END');
    console.log('📱 Scroll down - new properties will be added automatically!');
}, 100);