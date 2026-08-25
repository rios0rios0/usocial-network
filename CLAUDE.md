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
  `chlog new --kind <Kind> --body "<imperative description>"`
- Valid kinds: Added, Changed, Deprecated, Removed, Fixed, Security
- Choose the kind that best matches the change (e.g., new feature → Added,
  bug fix → Fixed, behavior change → Changed, removal → Removed, security fix → Security).
- If the change is backward-INCOMPATIBLE with the public API (a breaking
  change), you MUST add the `--breaking` flag:
  `chlog new --kind <Kind> --breaking --body "<description>"`.
  This is the ONLY thing that triggers a major version bump — the kind alone
  never does (per SemVer, major = incompatible change). When unsure whether a
  change breaks compatibility, ask the user instead of guessing.
- Fragments are YAML files in `.changes/unreleased/`; stage them with your commit.
- `chlog check` fails the build when a fragment is missing — never skip it.
<!-- chlog:end -->
