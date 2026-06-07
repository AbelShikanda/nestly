<div class="profile-modal" id="profileModal">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar" id="profileAvatar">👤</div>
            <div class="profile-info">
                <h3 id="profileName">{{ auth()->user()->name ?? 'Guest User' }}</h3>
                <p id="profileRole">{{ auth()->user() && auth()->user()->role === 'landlord' ? 'Landlord · Premium ready' : 'Tenant · Looking for home' }}</p>
            </div>
        </div>
        <div class="profile-scroll">
            <div class="section-title">📋 Account Type</div>
            <div style="background:#121218; padding:12px; border-radius:20px; margin-bottom:16px;">
                <span style="color:#aaa">Current plan:</span> 
                <span id="currentPlan" style="color:#facc15; font-weight:bold">{{ auth()->user()->subscription_plan ?? 'Free' }}</span>
            </div>
            
            <div class="section-title">💎 Subscription Plans</div>
            <div class="plan-card" data-plan="standard">
                <div><div class="plan-name">Standard</div><div class="plan-features">✓ 10 active listings · ✓ SMS replies · ✓ Basic analytics</div></div>
                <div class="plan-price">KES 499/mo</div>
            </div>
            <div class="plan-card" data-plan="gold">
                <div><div class="plan-name">Gold</div><div class="plan-features">✓ 25 active listings · ✓ Priority support · ✓ Featured badge</div></div>
                <div class="plan-price">KES 999/mo</div>
            </div>
            <div class="plan-card" data-plan="platinum">
                <div><div class="plan-name">Platinum</div><div class="plan-features">✓ Unlimited listings · ✓ Verified badge · ✓ Top placement</div></div>
                <div class="plan-price">KES 1,999/mo</div>
            </div>
            
            <div class="section-title">🛠️ Support</div>
            <button class="support-ticket-btn" id="supportTicketBtn">📧 Raise a Support Ticket</button>
            <button class="close-profile-btn" id="closeProfileBtn">Close</button>
        </div>
    </div>
</div>