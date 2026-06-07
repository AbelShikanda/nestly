<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Kenya Real Estate · Explore & Profile</title>
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
    .property-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 0.4rem;
      cursor: pointer;
      pointer-events: auto;
      transition: opacity 0.2s;
      display: inline-block;
    }
    .property-title:active { opacity: 0.7; }
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
    
    /* ========== DETAIL CARD MODAL ========== */
    .detail-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.85);
      z-index: 300;
      display: flex;
      align-items: center;
      justify-content: center;
      visibility: hidden;
      opacity: 0;
      transition: all 0.3s ease;
      backdrop-filter: blur(4px);
    }
    .detail-overlay.active { visibility: visible; opacity: 1; }
    .detail-card {
      width: 90%;
      max-width: 500px;
      height: 85vh;
      background: #0a0a0f;
      border-radius: 32px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transform: scale(0.92);
      transition: transform 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
      border: 1px solid rgba(250,204,21,0.2);
      position: relative;
    }
    .detail-overlay.active .detail-card { transform: scale(1); }
    .detail-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 40px;
      background: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);
      z-index: 5;
      pointer-events: none;
      border-radius: 32px 32px 0 0;
    }
    .detail-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 60px;
      background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
      z-index: 5;
      pointer-events: none;
      border-radius: 0 0 32px 32px;
    }
    .detail-scroll { flex: 1; overflow-y: auto; padding: 20px 18px 30px; scrollbar-width: thin; }
    .detail-hero { border-radius: 24px; overflow: hidden; margin-bottom: 20px; aspect-ratio: 16 / 9; background: #111; }
    .detail-hero video, .detail-hero img { width: 100%; height: 100%; object-fit: cover; }
    .detail-thumbnails { display: flex; gap: 12px; overflow-x: auto; padding: 8px 0 16px; margin-bottom: 20px; }
    .detail-thumb { width: 70px; height: 70px; border-radius: 16px; overflow: hidden; cursor: pointer; border: 2px solid rgba(255,255,255,0.3); flex-shrink: 0; }
    .detail-thumb.active-detail-thumb { border-color: #facc15; }
    .detail-thumb img, .detail-thumb video { width: 100%; height: 100%; object-fit: cover; }
    .detail-title { font-size: 1.6rem; font-weight: 700; color: white; }
    .detail-price { font-size: 1.7rem; font-weight: 800; color: #facc15; margin: 16px 0; }
    .action-buttons { display: flex; gap: 12px; margin-top: 20px; }
    .chat-btn, .close-detail-btn { flex: 1; padding: 14px; border-radius: 60px; font-weight: 600; cursor: pointer; border: none; text-align: center; }
    .chat-btn { background: #facc15; color: #1a1e24; }
    .close-detail-btn { background: rgba(255,255,255,0.15); color: white; }
    
    /* ========== DARK THEME CHAT MODAL (WhatsApp style) ========== */
    .chat-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: #0a0a0f;
      z-index: 500;
      display: flex;
      flex-direction: column;
      transform: translateX(100%);
      transition: transform 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .chat-modal.active { transform: translateX(0); }
    .chat-header {
      background: #121218;
      padding: 16px 16px 12px;
      display: flex;
      align-items: center;
      gap: 12px;
      color: white;
      position: sticky;
      top: 0;
      z-index: 10;
      border-bottom: 1px solid #1f1f2a;
    }
    .chat-back-btn {
      background: none;
      border: none;
      color: #facc15;
      font-size: 1.6rem;
      cursor: pointer;
      padding: 0 8px;
    }
    .chat-avatar {
      width: 40px;
      height: 40px;
      background: #facc15;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: #121218;
      font-weight: bold;
    }
    .chat-name { font-weight: 600; flex: 1; font-size: 1rem; color: white; }
    .chat-status { font-size: 0.7rem; opacity: 0.7; color: #aaa; }
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      background: #0d0d12;
    }
    .message {
      max-width: 80%;
      padding: 10px 14px;
      border-radius: 20px;
      font-size: 0.9rem;
      word-wrap: break-word;
    }
    .message.sent {
      background: #1e2a2a;
      color: #facc15;
      align-self: flex-end;
      border-bottom-right-radius: 4px;
    }
    .message.received {
      background: #1a1f2a;
      color: #e0e0e0;
      align-self: flex-start;
      border-bottom-left-radius: 4px;
    }
    .chat-input-area {
      background: #121218;
      padding: 12px 16px;
      display: flex;
      gap: 12px;
      align-items: center;
      border-top: 1px solid #1f1f2a;
    }
    .chat-input {
      flex: 1;
      background: #1e1e2a;
      border: none;
      padding: 10px 16px;
      border-radius: 30px;
      color: white;
      font-size: 0.9rem;
      outline: none;
    }
    .chat-send-btn {
      background: #facc15;
      border: none;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      font-size: 1.2rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #121218;
    }
    
    /* ========== CHAT LIST MODAL (INBOX) ========== */
    .chatlist-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: #0a0a0f;
      z-index: 500;
      display: flex;
      flex-direction: column;
      transform: translateX(100%);
      transition: transform 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .chatlist-modal.active { transform: translateX(0); }
    .chatlist-header {
      background: #121218;
      padding: 16px 16px 12px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid #1f1f2a;
    }
    .chatlist-back-btn {
      background: none;
      border: none;
      color: #facc15;
      font-size: 1.6rem;
      cursor: pointer;
      padding: 0 8px;
    }
    .chatlist-header-title {
      color: white;
      font-size: 1.2rem;
      font-weight: 600;
      flex: 1;
    }
    .chatlist-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 16px;
      border-bottom: 1px solid #1a1a22;
      cursor: pointer;
      background: #0d0d12;
      transition: 0.1s;
    }
    .chatlist-item:active { background: #1a1f2a; }
    .chatlist-avatar {
      width: 52px;
      height: 52px;
      background: #facc15;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: #121218;
      font-weight: bold;
    }
    .chatlist-info { flex: 1; }
    .chatlist-name { color: white; font-weight: 600; margin-bottom: 4px; }
    .chatlist-preview { color: #aaa; font-size: 0.8rem; }
    .chatlist-time { color: #666; font-size: 0.7rem; }
    
    /* ========== UNIFIED HEADER ========== */
    .search-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: transparent;
      backdrop-filter: blur(20px);
      padding: 0.75rem 1rem;
      transition: transform 0.3s ease;
      transform: translateY(0);
      border-bottom: 1px solid rgba(255,255,255,0.1);
      z-index: 100;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .search-header.hide { transform: translateY(-100%); }
    .search-container { flex: 1; position: relative; }
    .search-input {
      width: 100%;
      padding: 0.85rem 1rem 0.85rem 2.6rem;
      border-radius: 44px;
      background: rgba(255,255,255,0.15);
      color: white;
      border: 1px solid rgba(255,255,255,0.25);
      outline: none;
    }
    .search-input:focus { background: rgba(0,0,0,0.4); border-color: #facc15; }
    .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); }
    .search-clear { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); display: none; cursor: pointer; background: none; border: none; color: white; }
    .search-clear.visible { display: block; }
    .header-notification-btn {
      background: rgba(250,204,21,0.95);
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: none;
      font-size: 1.3rem;
      flex-shrink: 0;
      position: relative;
    }
    .header-notification-badge {
      position: absolute;
      top: -4px;
      right: -4px;
      background: #e53935;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* ========== BOTTOM NAVIGATION - Only Explore & Profile ========== */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: transparent;
      backdrop-filter: blur(20px);
      padding: 10px 20px 12px;
      z-index: 200;
      display: flex;
      justify-content: center;
      gap: 60px;
      transition: transform 0.3s ease;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .bottom-nav.hide-nav { transform: translateY(100%); }
    .nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      cursor: pointer;
      padding: 4px 24px;
      border-radius: 40px;
    }
    .nav-icon { font-size: 1.6rem; }
    .nav-label { font-size: 0.65rem; color: rgba(255,255,255,0.9); }
    .nav-item.active .nav-label, .nav-item.active .nav-icon { color: #facc15; }
    
    /* Role-based Add Button (floating, only visible for landlord) */
    .add-listing-btn {
      position: fixed;
      bottom: 80px;
      right: 20px;
      background: #facc15;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: none;
      font-size: 1.8rem;
      z-index: 210;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      transition: transform 0.1s;
      display: none;
    }
    .add-listing-btn:active { transform: scale(0.94); }
    .add-listing-btn.visible { display: flex; }
    
    /* ========== PROFILE PAGE MODAL ========== */
    .profile-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.85);
      z-index: 400;
      display: flex;
      align-items: center;
      justify-content: center;
      visibility: hidden;
      opacity: 0;
      transition: all 0.3s ease;
      backdrop-filter: blur(4px);
    }
    .profile-modal.active { visibility: visible; opacity: 1; }
    .profile-card {
      width: 90%;
      max-width: 480px;
      max-height: 85vh;
      background: #0a0a0f;
      border-radius: 32px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(250,204,21,0.2);
    }
    .profile-header {
      background: linear-gradient(135deg, #121218, #1a1f2a);
      padding: 24px;
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 1px solid #1f1f2a;
    }
    .profile-avatar {
      width: 70px;
      height: 70px;
      background: #facc15;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.2rem;
      color: #121218;
      font-weight: bold;
    }
    .profile-info h3 { color: white; font-size: 1.3rem; }
    .profile-info p { color: #aaa; font-size: 0.8rem; margin-top: 4px; }
    .profile-scroll { flex: 1; overflow-y: auto; padding: 20px; }
    .section-title { color: #facc15; font-size: 1rem; margin: 16px 0 12px; font-weight: 600; }
    .plan-card {
      background: #121218;
      border-radius: 20px;
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #1f1f2a;
      cursor: pointer;
      transition: 0.1s;
    }
    .plan-card:active { background: #1a1f2a; }
    .plan-name { font-weight: bold; color: white; }
    .plan-price { color: #facc15; font-weight: bold; }
    .plan-features { font-size: 0.7rem; color: #888; margin-top: 4px; }
    .support-ticket-btn {
      background: #1a1f2a;
      border: 1px solid #facc15;
      border-radius: 40px;
      padding: 14px;
      width: 100%;
      text-align: center;
      color: #facc15;
      font-weight: 600;
      cursor: pointer;
      margin-top: 16px;
    }
    .close-profile-btn {
      background: #facc15;
      border: none;
      padding: 14px;
      width: 100%;
      font-weight: bold;
      cursor: pointer;
      margin-top: 16px;
      border-radius: 40px;
    }
    
    .demo-toast, .no-results-toast { position: fixed; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.85); color: #facc15; padding: 6px 18px; border-radius: 40px; font-size: 0.8rem; z-index: 201; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap; }
    .demo-toast { bottom: 80px; }
    .show-toast { opacity: 1 !important; }
    .doubletap-hint { position: fixed; bottom: 100px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); padding: 4px 12px; border-radius: 40px; font-size: 10px; color: rgba(255,255,255,0.7); z-index: 202; pointer-events: none; }
    
    @media (max-width: 480px) {
      .thumbnail { width: 54px; height: 54px; }
      .property-title { font-size: 1.4rem; }
      .bottom-nav { gap: 30px; }
      .nav-item { padding: 4px 16px; }
    }
  </style>
</head>
<body>

<div class="search-header" id="searchHeader">
  <div class="search-container">
    <span class="search-icon">🔍</span>
    <input type="text" class="search-input" id="searchInput" placeholder="Search Nairobi, Mombasa, Kisumu..." autocomplete="off">
    <button class="search-clear" id="searchClearBtn">✕</button>
  </div>
  <button class="header-notification-btn" id="notificationBtn">💬<span class="header-notification-badge" id="chatBadge">0</span></button>
</div>

<div class="reels-container" id="reelsContainer"></div>

<div class="bottom-nav" id="bottomNav">
  <div class="nav-item" data-nav="explore"><div class="nav-icon">🔍</div><div class="nav-label">Explore</div></div>
  <div class="nav-item" data-nav="profile"><div class="nav-icon">👤</div><div class="nav-label">Profile</div></div>
</div>

<button class="add-listing-btn" id="addListingBtn">➕</button>
<div class="doubletap-hint" id="doubleTapHint">✨ Double tap to hide/show UI · Tap title for details</div>
<div class="demo-toast" id="demoToast">🇰🇪 Kenya Real Estate</div>
<div class="no-results-toast" id="noResultsToast">✨ No matching properties</div>

<!-- Detail Card Modal -->
<div class="detail-overlay" id="detailOverlay"><div class="detail-card"><div class="detail-scroll" id="detailScroll"></div></div></div>

<!-- Profile Modal -->
<div class="profile-modal" id="profileModal">
  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-avatar" id="profileAvatar">👤</div>
      <div class="profile-info"><h3 id="profileName">Alex Okoth</h3><p id="profileRole">Tenant · Member since 2024</p></div>
    </div>
    <div class="profile-scroll">
      <div class="section-title">📋 Account Type</div>
      <div style="background:#121218; padding:12px; border-radius:20px; margin-bottom:16px;"><span style="color:#aaa">Current plan:</span> <span id="currentPlan" style="color:#facc15; font-weight:bold">Free</span></div>
      
      <div class="section-title">💎 Subscription Plans</div>
      <div class="plan-card" data-plan="standard"><div><div class="plan-name">Standard</div><div class="plan-features">✓ 10 active listings · ✓ SMS replies · ✓ Basic analytics</div></div><div class="plan-price">KES 499/mo</div></div>
      <div class="plan-card" data-plan="gold"><div><div class="plan-name">Gold</div><div class="plan-features">✓ 25 active listings · ✓ Priority support · ✓ Featured badge</div></div><div class="plan-price">KES 999/mo</div></div>
      <div class="plan-card" data-plan="platinum"><div><div class="plan-name">Platinum</div><div class="plan-features">✓ Unlimited listings · ✓ Verified badge · ✓ Top placement</div></div><div class="plan-price">KES 1,999/mo</div></div>
      
      <div class="section-title">🛠️ Support</div>
      <button class="support-ticket-btn" id="supportTicketBtn">📧 Raise a Support Ticket</button>
      <button class="close-profile-btn" id="closeProfileBtn">Close</button>
    </div>
  </div>
</div>

<!-- Chat Modals -->
<div class="chat-modal" id="chatModal"><div class="chat-header"><button class="chat-back-btn" id="closeChatBtn">←</button><div class="chat-avatar" id="chatAvatar">👤</div><div><div class="chat-name" id="chatName">Landlord</div><div class="chat-status">online</div></div></div><div class="chat-messages" id="chatMessages"></div><div class="chat-input-area"><input type="text" class="chat-input" id="chatInput" placeholder="Type a message..."><button class="chat-send-btn" id="chatSendBtn">📤</button></div></div>
<div class="chatlist-modal" id="chatlistModal"><div class="chatlist-header"><button class="chatlist-back-btn" id="chatlistBackBtn">←</button><span class="chatlist-header-title">Chats</span></div><div id="chatListContainer" style="flex:1; overflow-y:auto;"></div></div>

<script>
  (function() {
    // ========== USER ROLE STATE ==========
    let currentUserRole = "tenant"; // "tenant" or "landlord" - change to "landlord" to test add button
    let currentUserName = "Alex Okoth";
    let currentPlan = "Free";
    
    // ========== KENYAN DATA ==========
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
    
    // ========== CHAT SYSTEM ==========
    let conversations = [];
    let activeChatId = null;
    
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
    
    // Chat event handlers
    document.getElementById('closeChatBtn').addEventListener('click', () => {
      document.getElementById('chatModal').classList.remove('active');
      renderChatList();
      document.getElementById('chatlistModal').classList.add('active');
    });
    document.getElementById('chatlistBackBtn').addEventListener('click', () => {
      document.getElementById('chatlistModal').classList.remove('active');
      showDemoToast('🏠 Continue exploring properties');
    });
    document.getElementById('chatSendBtn').addEventListener('click', () => {
      if (activeChatId !== null) sendMessage(activeChatId, document.getElementById('chatInput').value);
    });
    document.getElementById('chatInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); if (activeChatId !== null) sendMessage(activeChatId, document.getElementById('chatInput').value); }
    });
    
    const notifBtn = document.getElementById('notificationBtn');
    const chatlistModal = document.getElementById('chatlistModal');
    notifBtn.addEventListener('click', () => {
      renderChatList();
      chatlistModal.classList.add('active');
    });
    chatlistModal.addEventListener('click', (e) => { if (e.target === chatlistModal) chatlistModal.classList.remove('active'); });
    
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
        if (item.type === 'image') { const img = document.createElement('img'); img.src = item.url; hero.appendChild(img); }
        else { const vid = document.createElement('video'); vid.src = item.url; vid.autoplay = true; vid.muted = true; vid.loop = true; vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; hero.appendChild(vid); vid.play().catch(e=>{}); }
        if (activeThumb) activeThumb.classList.remove('active-detail-thumb');
        if (el) el.classList.add('active-detail-thumb');
        activeThumb = el;
      }
      property.gallery.forEach((item, idx) => {
        const thumb = document.createElement('div'); thumb.className = 'detail-thumb';
        if (idx === 0) thumb.classList.add('active-detail-thumb');
        if (item.type === 'image') { const img = document.createElement('img'); img.src = item.url; thumb.appendChild(img); }
        else { const vid = document.createElement('video'); vid.src = item.url; vid.muted = true; vid.loop = true; vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; thumb.appendChild(vid); thumb.addEventListener('mouseenter', () => vid.play().catch(e=>{})); thumb.addEventListener('mouseleave', () => { vid.pause(); vid.currentTime=0; }); }
        thumb.addEventListener('click', () => setMedia(item, thumb));
        thumbsDiv.appendChild(thumb);
      });
      if (property.gallery.length) setMedia(property.gallery[0], thumbsDiv.children[0]);
      document.getElementById('closeDetailBtn').addEventListener('click', () => detailOverlay.classList.remove('active'));
      document.getElementById('detailChatBtn').addEventListener('click', () => {
        detailOverlay.classList.remove('active');
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
        let conv = conversations.find(c => c.landlordName === property.landlord.name);
        if (!conv) { const newId = conversations.length; conversations.push({ id: newId, landlordName: property.landlord.name, landlordAvatar: property.landlord.name.charAt(0), landlordPhone: property.landlord.phone, messages: [] }); conv = conversations[newId]; updateChatBadge(); }
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
      const labelDiv = document.createElement('div'); labelDiv.className = 'thumb-label'; labelDiv.innerText = '📸'; labelDiv.style.cssText = 'text-align:center;font-size:9px;color:#facc15;margin-bottom:4px;';
      thumbStrip.appendChild(labelDiv);
      let activeThumb = null;
      function setMainMedia(item, thumbEl) {
        heroDiv.innerHTML = '';
        if (item.type === 'image') { const img = document.createElement('img'); img.src = item.url; img.style.cssText = 'width:100%;height:100%;object-fit:cover'; heroDiv.appendChild(img); }
        else { const vid = document.createElement('video'); vid.src = item.url; vid.autoplay = true; vid.muted = true; vid.loop = true; vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; heroDiv.appendChild(vid); vid.play().catch(e=>{}); let toggle = slideElement.querySelector('.sound-toggle'); if (!toggle) { const btn = document.createElement('button'); btn.className = 'sound-toggle'; btn.innerHTML = '🔇'; slideElement.appendChild(btn); toggle = btn; } const updateIcon = () => { toggle.innerHTML = vid.muted ? '🔇' : '🔊'; }; const handler = (e) => { e.stopPropagation(); vid.muted = !vid.muted; updateIcon(); }; toggle.replaceWith(toggle.cloneNode(true)); const fresh = slideElement.querySelector('.sound-toggle'); if(fresh) fresh.addEventListener('click', handler); vid.muted = true; updateIcon(); }
        if(activeThumb) activeThumb.classList.remove('active-thumb');
        if(thumbEl) thumbEl.classList.add('active-thumb');
        activeThumb = thumbEl;
      }
      gallery.forEach((item, idx) => {
        const thumb = document.createElement('div'); thumb.className = 'thumbnail';
        if(idx===0) thumb.classList.add('active-thumb');
        if(item.type === 'image') { const img = document.createElement('img'); img.src = item.url; thumb.appendChild(img); }
        else { const vid = document.createElement('video'); vid.src = item.url; vid.muted = true; vid.loop = true; vid.style.cssText = 'width:100%;height:100%;object-fit:cover'; thumb.appendChild(vid); thumb.addEventListener('mouseenter', () => vid.play().catch(e=>{})); thumb.addEventListener('mouseleave', () => { vid.pause(); vid.currentTime=0; }); }
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
        showDemoToast(`✨ ${newCount} new properties loaded`);
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
    
    // ========== PROFILE MODAL FUNCTIONS ==========
    const profileModal = document.getElementById('profileModal');
    function openProfile() {
      document.getElementById('profileName').innerText = currentUserName;
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
    
    // ========== ROLE-BASED ADD BUTTON ==========
    const addBtn = document.getElementById('addListingBtn');
    function updateAddButton() {
      if(currentUserRole === 'landlord') addBtn.classList.add('visible');
      else addBtn.classList.remove('visible');
    }
    addBtn.addEventListener('click', () => { showDemoToast('➕ Add new listing form (coming soon)'); });
    
    // ========== NAVIGATION ==========
    document.querySelector('.nav-item[data-nav="explore"]')?.addEventListener('click', () => {
      container.scrollTo({ top: 0, behavior: 'smooth' });
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      document.querySelector('.nav-item[data-nav="explore"]').classList.add('active');
      showDemoToast('🔍 Exploring properties');
    });
    document.querySelector('.nav-item[data-nav="profile"]')?.addEventListener('click', () => {
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      document.querySelector('.nav-item[data-nav="profile"]').classList.add('active');
      openProfile();
    });
    document.querySelector('.nav-item[data-nav="explore"]')?.classList.add('active');
    
    // ========== DOUBLE TAP TO TOGGLE UI ==========
    const searchHeader = document.getElementById('searchHeader');
    const bottomNav = document.getElementById('bottomNav');
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
    
    // Auto-fade hint
    setTimeout(() => {
      const hint = document.getElementById('doubleTapHint');
      if (hint) hint.style.opacity = '0.5';
    }, 5000);
    
    initConversations();
    renderSlides();
    updateAddButton();
    setTimeout(() => { const fv = document.querySelector('.hero-media video'); if(fv) fv.play().catch(e=>{}); }, 500);
  })();
</script>
</body>
</html>