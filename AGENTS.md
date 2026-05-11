# AGENTS.md

## Cursor Cloud specific instructions

### Project Overview

A.U.R.A (Asset Update & Report Application) — Laravel 12.x incident reporting and asset management system for Gandaria City. Server-side rendered with Blade templates, Tailwind CSS 4, and Vite for frontend assets.

### Running the Application

Standard commands documented in `composer.json` scripts:
- **Full dev environment**: `composer dev` (starts Laravel server, queue, pail, and Vite concurrently)
- **Laravel server only**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Vite dev server only**: `npm run dev`
- **Lint**: `php vendor/bin/pint --test` (or without `--test` to auto-fix)
- **Tests**: `php artisan test`

### Critical Gotchas for Local Development (SQLite)

1. **PostgreSQL-specific migrations**: Several migrations (`2026_04_04_090200_fix_asset_findings_status_enum`, `2026_04_04_100000_enable_supabase_rls`, `2026_04_05_030000_add_dev_role_to_users_table`) use raw PostgreSQL SQL (CHECK constraints, RLS) that is incompatible with SQLite. When setting up locally with SQLite:
   - These migrations must be manually marked as run in the migrations table
   - The `users` table CHECK constraint on `role` must be removed to allow the 'DEV' role
   - The `asset_findings` table CHECK constraint on `status` must be removed to allow 'Pending Approval'
   - The `activity_logs` migration creates the table successfully but the RLS statements fail — mark as run after table creation

2. **PHPUnit tests fail with SQLite**: The feature tests use `RefreshDatabase` which runs all migrations including the PostgreSQL-specific ones. This is a known pre-existing issue. The Unit tests pass fine.

3. **Storage link required**: Run `php artisan storage:link` after setup for file uploads to work.

4. **FILESYSTEM_DISK**: The `.env.example` has `FILESYSTEM_DISK=s3` at the bottom (for Supabase S3 in production). For local dev, set `FILESYSTEM_DISK=local`.

### Login Credentials (Seeded Users)

- **CPM**: `cpm@gandariacity.com` / `password123`
- **SPV1**: `spv1@gandariacity.com` / `password123`
- **SPV2**: `spv2@gandariacity.com` / `password123`
- **IT**: `it@gandariacity.com` / `password123`
- **DEV**: `dev@gandariacity.com` / `devmonitor2026!`

### Database Setup for Local Dev

```bash
touch database/database.sqlite
php artisan migrate --force  # Will fail on PG-specific migrations
# Manually mark PG migrations as run, fix CHECK constraints (see gotchas above)
php artisan db:seed --force
```
