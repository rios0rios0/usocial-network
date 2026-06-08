# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build & Run

No build step. `composer.json` declares only the PHP `>=7.2` constraint plus `lint`/`test` scripts — there are no runtime PHP dependencies to install and no npm. Frontend libraries are bundled in `resources/plugins/`.

```bash
# Dev server
php -S localhost:8000

# Lint (CI runs `composer lint`)
composer lint   # find . -name '*.php' -not -path './vendor/*' | xargs -n1 php -l

# Or directly, without Composer:
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

`composer test` is a placeholder (`exit 0`) — there is no test suite.

## Database

MySQL via PDO. Apply migrations in order:

```bash
mysql -u root -p usocial < db/2019051801_usocial.sql
mysql -u root -p usocial < db/2019051802_usocial.sql
```

Credentials via env vars (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`) with defaults in `core/db/DatabaseConnection.php`; see `.env.example`.

## Architecture

MVC with service layer. Entry point: `index.php` → `RoutesManagement`. Routing is filesystem-based: a URL maps directly to a controller file path (e.g. `app/controllers/user/list.php`, linked via `RoutesManagement::base_url()`) — there is no central route table.

- **Controllers** (`app/controllers/`): thin, procedural request-handler scripts (no base class), organized by feature subdirectory.
- **Services** (`app/services/`): `UserService`, `PostService` — most business logic and DB interaction.
- **Views** (`app/views/`): PHP templates rendered by `ViewsManagement`.
- **Core** (`core/`): framework classes — `DatabaseConnection` and `SessionManagement` are singletons (`getInstance()`); `RoutesManagement` is static-only (`redirect()`, `base_url()`); `ViewsManagement` is instantiated per render.

## Conventions

- PHP 7.2+ features: object type hints, return type declarations, abstract method overrides.
- All DB queries use PDO prepared statements — never interpolate user input into SQL.
- Frontend assets in `resources/`; vendor libraries committed in `resources/plugins/`.
- Commit messages follow [these conventions](https://github.com/rios0rios0/guide/wiki/Life-Cycle/Git-Flow) (e.g. `feat:`, `fix:`, `chore:`).
- `CHANGELOG.md` follows [Keep a Changelog](https://keepachangelog.com/).

## CI

`.github/workflows/default.yaml` delegates to `rios0rios0/pipelines/.github/workflows/composer-library.yaml@main`, which runs the `composer.json` scripts (`composer lint` for PHP syntax checking). Tagged commits produce a GitHub Release.
