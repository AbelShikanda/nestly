# DECISIONS: Nestly PostgreSQL + Mobile-First

## DB Decision
- **Choice**: PostgreSQL over MySQL
- **Rationale**: User requested PostgreSQL migration; Laravel 8 natively supports pgsql
- **`.env` settings**: `DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=laravel`, `DB_USERNAME=root`, `DB_PASSWORD=` (user to create creds)

## Mobile-First Decision
- **Choice**: Adapt existing desktop layouts to small screens + add bottom nav
- **Rationale**: Project is "primarily a phone layout"; keeping responsive breakpoints for larger devices
- **Implementation**: Hamburger header menu + bottom tab bar (Home/Explore/Add/Search/Profile)

## Functional Priority
- **Choice**: Fix all `underConstruction` placeholder links first
- **Rationale**: 14 instances were blocking actual navigation; user wants "buttons and icons to be functional"
- **Order**: Header → Footer → Partials → Contact → Property detail → Filters → Mobile nav → Admin CRUD

## Open Questions (for later sprint)
- Admin CRUD scope: properties only vs full (colors/sizes etc.)
- Newsletter storage: internal DB vs external service (Mailchimp/SendGrid)
- PostgreSQL host/port: default 5432 local or remote production host?