# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build & Run

No build step, no package manager (no Composer, no npm). All dependencies are bundled.

```bash
# Dev server
php -S localhost:8000

# Lint (same as CI)
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

## Database

MySQL via PDO. Apply migrations in order:

```bash
mysql -u root -p usocial < db/2019051801_usocial.sql
mysql -u root -p usocial < db/2019051802_usocial.sql
```

Credentials via env vars (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`) with defaults in `core/db/DatabaseConnection.php`.

## Architecture

MVC with service layer. Entry point: `index.php` → `RoutesManagement`.

- **Controllers** (`app/controllers/`): thin request handlers, organized by feature subdirectory.
- **Services** (`app/services/`): `UserService`, `PostService` — all business logic and DB interaction.
- **Views** (`app/views/`): PHP templates rendered by `ViewsManagement`.
- **Core** (`core/`): framework singletons — `DatabaseConnection`, `SessionManagement`, `RoutesManagement`, `ViewsManagement`.

## Conventions

- PHP 7.2+ features: object type hints, return type declarations, abstract method overrides.
- All DB queries use PDO prepared statements — never interpolate user input into SQL.
- Frontend assets in `resources/`; vendor libraries committed in `resources/plugins/`.
- Commit messages follow [these conventions](https://github.com/rios0rios0/guide/wiki/Life-Cycle/Git-Flow) (e.g. `feat:`, `fix:`, `chore:`).
- `CHANGELOG.md` follows [Keep a Changelog](https://keepachangelog.com/).

## CI

`.github/workflows/default.yaml` delegates to `rios0rios0/pipelines/.github/workflows/composer-library.yaml@main`. Runs PHP linting; tagged commits produce a GitHub Release.
