# GOALS: Nestly Real Estate App

## Primary Goal
Migrate from MySQL to PostgreSQL and rebuild the client UI/UX for mobile-first real estate browsing with fully functional buttons, forms, and navigation.

## Sub-Goals
1. **Database**: PostgreSQL migration with fresh schema
2. **Mobile UI**: Adapt desktop layouts to phone; add bottom nav hamburger menu
3. **Functional Buttons/Wire-up**: Contact form, property search, property detail, admin CRUD
4. **Admin Console**: Dashboard CRUD for properties/categories/types
5. **Quality**: Tests, lint, typecheck, mobile viewport validation

## Success Criteria
- `php artisan migrate:fresh` runs on pgsql without errors
- All header/footer nav links resolve to working pages
- Contact form submits without JS errors
- Properties page lists with functional filters
- Mobile viewport renders cleanly with touch-friendly targets