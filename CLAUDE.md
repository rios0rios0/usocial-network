# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

USocial Network — a social networking app built with PHP 7.2+ and a custom MVC framework. No package manager (no Composer, no npm). All frontend libraries are bundled in `resources/plugins/`.

## Commands

Start dev server:
```bash
php -S localhost:8000
```

Lint all PHP files (same check CI runs):
```bash
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

Apply database migrations:
```bash
mysql -u root -p usocial < db/2019051801_usocial.sql
mysql -u root -p usocial < db/2019051802_usocial.sql
```

There is no build step, no test suite, and no formatter.

## Architecture

Custom MVC framework with no external dependencies:

- **Controllers** (`app/controllers/`): thin request handlers organized by feature (home, login, post, user).
- **Services** (`app/services/`): `UserService` and `PostService` hold business logic and database queries.
- **Views** (`app/views/`): PHP templates rendered by `ViewsManagement` in `core/views/`.
- **Router**: `RoutesManagement` in `core/routes/` dispatches URLs to controllers.
- **Database**: `DatabaseConnection` singleton in `core/db/` wraps PDO/MySQL. Configured via env vars `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` (defaults: `localhost`, `root`, empty, `usocial`).
- **Session**: `SessionManagement` singleton in `core/session/`.

## Conventions

- Commit messages follow [these conventions](https://github.com/rios0rios0/guide/wiki/Life-Cycle/Git-Flow) (`feat:`, `fix:`, `chore:`).
- All database queries use PDO prepared statements — never interpolate user input into SQL.
- `CHANGELOG.md` follows [Keep a Changelog](https://keepachangelog.com/) with semantic versioning.

## CI

`.github/workflows/default.yaml` delegates to `rios0rios0/pipelines/.github/workflows/composer-library.yaml@main`. Runs PHP lint on push/PR to `main`. Tagged commits produce a GitHub Release.
