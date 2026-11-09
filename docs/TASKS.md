# TASKS: Nestly PostgreSQL + Mobile-First + Functional UI

## Current Sprint: Phase 4 - Mobile & Newsletter

### [1] DB Migration to PostgreSQL ✅✅✅
- [x] Set up PostgreSQL database (DB_CONNECTION=pgsql, DB_HOST=127.0.0.1, DB_PORT=5433, DB_DATABASE=pabe_nestly_db, DB_USERNAME=abel_nestly, DB_PASSWORD=abel_nestly)
- [x] Update `.env` with PostgreSQL connection settings
- [x] Run `php artisan migrate:fresh` to refresh schema ✅
- [x] Verify all 11 tables migrate successfully ✅

### [2] Routes & Controllers - Core Functionality ✅✅✅
- [x] Add `POST /contact` route + `storeContact()` method in PagesController ✅
- [x] Add `GET /property/{property:slug}` route + `propertyShow()` method ✅
- [x] Add `GET /properties/filter` route + `filterProperties()` method ✅
- [x] Create `property-show.blade.php` view ✅

### [3] Mobile-First Header & Navigation ✅✅✅
- [x] Fix Login/Register links to real auth routes ✅
- [x] Add authenticated user dropdown with logout ✅
- [x] Implement bottom mobile nav (Home/Explore/Add/Search/Profile) ✅
- [x] Responsive hamburger menu infrastructure ✅

### [4] Contact Form Backend ✅✅✅
- [x] Validation in `storeContact()` works ✅
- [x] Success flash message framework in place ✅
- [x] ContactMessage model + migration + persistent storage ✅
- [x] Contact messages stored in DB via ContactMessage::create() ✅

### [5] Property Detail Page Styling ✅
- [x] Add SCSS styles for `#fh5co-property-single` ✅
- [x] Verify view renders with consistent design ✅

### [6] Admin CRUD for Properties ✅✅
- [x] PropertyController index/show/destroy/verify/feature/deleteImage exist ✅
- [x] Admin resource routes registered for properties ✅
- [x] Enhanced admin property index view with status badges and CRUD actions ✅

### [7] Mobile Viewport Testing ○
- [ ] Test hamburger menu on phone sizes
- [ ] Verify bottom nav touch targets >=44px
- [ ] Check form input readability on device widths

### [8] Newsletter Form Backend ○
- [ ] Add newsletter submit route + storage (or integrate with external service)
- [ ] Update footer subscribe form to point to working endpoint

### [9] Lint / Typecheck ○
- [ ] Run `php artisan route:list` verification ✅
- [ ] Check for any PHP syntax issues ✅
- [ ] Verify asset compilation with `npm run dev` ✅

---
**Next action**: Implement newsletter form backend (item [8]) - add route + storage for footer subscribe form.