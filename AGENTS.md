# AGENTS.md

This file provides guidance to AI coding agents (Claude Code, Codex, Cursor, GitHub Copilot) when working with code in this repository.

## Build & Run

No build step. `composer.json` declares only the PHP `>=7.2` constraint plus `lint`/`test` scripts — there are no runtime PHP dependencies to install and no npm. Frontend libraries are bundled in `resources/plugins/`.

```bash
# Dev server: run from the PARENT of a checkout named exactly `USocial-Network`, on port 80,
# then open http://localhost/USocial-Network/ (port 80 may need elevated privileges)
php -S localhost:80

# Lint (CI runs `composer lint`)
composer lint   # php -l on every non-vendor .php file (script in composer.json)

# Or directly, without Composer:
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

`RoutesManagement::$app` hardcodes the base path `/USocial-Network`: `ViewsManagement` resolves views from `DOCUMENT_ROOT . '/USocial-Network'`, and `base_url()` (used for every asset and link) omits the port. `php -S localhost:8000` from the repo root redirects to a 404.

`composer test` is a placeholder (`exit 0`) — there is no test suite.

## Database

MySQL via PDO. Each `db/*.sql` file is a full `mysqldump` snapshot (MySQL 5.7), not an incremental migration: it creates `usocial` if missing, then drops and recreates every table with seed rows, so loading one replaces all existing data. Load the newest, without a database argument:

```bash
mysql -u root -p < db/2019051802_usocial.sql
```

Credentials come from the server process's env vars (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`), falling back to defaults in `core/db/DatabaseConnection.php`. Nothing loads a `.env` file; `.env.example` only lists the names, so export them before starting the server.

## Architecture

MVC with service layer. Entry point: `index.php` → `RoutesManagement`. Routing is filesystem-based: a URL maps directly to a controller file path (e.g. `app/controllers/user/list.php`, linked via `RoutesManagement::base_url()`) — there is no central route table.

- **Controllers** (`app/controllers/`): procedural request-handler scripts (no base class), organized by feature subdirectory; each is its own entry point. Every write (sign-up, post insert/like/comment/delete, friendship changes, photo update) and the login query call PDO directly in the controller.
- **Services** (`app/services/`): `UserService`, `PostService` — the read queries (user listings, timeline, posts, comments). Put new logic here rather than in controllers.
- **Views** (`app/views/`): PHP templates rendered by `ViewsManagement`.
- **Core** (`core/`): framework classes — `DatabaseConnection` and `SessionManagement` are singletons (`getInstance()`); `RoutesManagement` is static-only (`redirect()`, `base_url()`); `ViewsManagement` is instantiated per render.

## Conventions

- PHP 7.2+ features: object type hints, return type declarations, abstract method overrides.
- All DB queries use PDO prepared statements — never interpolate user input into SQL.
- Frontend assets in `resources/`; vendor libraries committed in `resources/plugins/`.
- Commit messages follow [these conventions](https://github.com/rios0rios0/guide/wiki/Git-Flow): `type(SCOPE): message` in simple past tense (e.g. `chore(bump): bumped version to 0.4.1`).
- `CHANGELOG.md` follows [Keep a Changelog](https://keepachangelog.com/).

## CI

`.github/workflows/default.yaml` delegates to `rios0rios0/pipelines/.github/workflows/composer-library.yaml@main`. Beyond `composer lint` (PHP syntax checking) it runs security scanning: an SCA `composer-audit` step — `composer.lock` is committed (with an empty package set) solely to enable that audit — and semgrep SAST (`.semgrepignore` excludes `resources/plugins/`). Tagged commits produce a GitHub Release.

<!-- chlog:start -->
## Changelog (chlog) — MANDATORY

If the repository you are working in uses chlog (a `.chlog.yaml` or `.chlog.yml`
config file, or a `.changes/` directory, exists at the project root), the
following is binding and ALWAYS applies: whenever you make ANY change, you MUST
create a changelog fragment as part of the same change — automatically, without
being asked, before committing.

- Do NOT edit CHANGELOG.md directly; it is generated from fragments.
- Create the fragment with:
  `chlog new --kind <Kind> --body '<past-tense description>'`
- Write an apostrophe inside the single-quoted body as `'\''`.
- Valid kinds: Added, Changed, Deprecated, Removed, Fixed, Security
- Choose the kind that best matches the change (e.g., new feature → Added,
  bug fix → Fixed, behavior change → Changed, removal → Removed, security fix → Security).
- If the change is backward-INCOMPATIBLE with the public API (a breaking
  change), you MUST add the `--breaking` flag:
  `chlog new --kind <Kind> --breaking --body '<past-tense description>'`.
  This is the ONLY thing that triggers a major version bump — the kind alone
  never does (per SemVer, major = incompatible change). When unsure whether a
  change breaks compatibility, ask the user instead of guessing.
- Fragments are YAML files in `.changes/unreleased/`; stage them with your commit.
- `chlog check` fails the build when a fragment is missing — never skip it.
<!-- chlog:end -->
