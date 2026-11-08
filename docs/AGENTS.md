# AGENTS: Nestly Project

## Active Session Guidelines
- This worktree uses Laravel 8 with PostgreSQL target
- Primary focus: mobile-first UI + functional buttons/forms
- Current agent: code-level implementation

## Agent Coordination
- Check `docs/TASKS.md` for current sprint goals
- Report completion via `goal_report` with concrete status
- Create feature branches for non-trivial changes
- Run `php artisan migrate:fresh` after DB config changes

## Known Working Routes
- `/`, `/home`, `/properties`, `/contact`, `/agent`, `/blog`
- `POST /contact`, `GET /property/{slug}`, `GET /properties/filter`

## Pending Route Additions
- Admin CRUD routes (properties/categories/types)
- Property image upload endpoints