# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

When a new release is proposed:

1. Create a new branch `bump/x.x.x` (this isn't a long-lived branch!!!);
2. The Unreleased section on `CHANGELOG.md` gets a version number and date;
3. Open a Pull Request with the bump version changes targeting the `main` branch;
4. When the Pull Request is merged, a new Git tag must be created using [GitHub environment](https://github.com/rios0rios0/usocial-network/tags).

Releases to productive environments should run from a tagged version.
Exceptions are acceptable depending on the circumstances (critical bug fixes that can be cherry-picked, etc.).

## [Unreleased]

### Changed

- corrected the routing model in `CLAUDE.md` and `.github/copilot-instructions.md`: routing is filesystem-based (a URL maps directly to a controller file path), controllers are procedural scripts with no base class, and `RoutesManagement` only provides `redirect()`/`base_url()` rather than dispatching routes; also fixed `CLAUDE.md` to stop labeling all four `core/` classes as singletons (only `DatabaseConnection` and `SessionManagement` use `getInstance()`)

## [0.2.2] - 2026-06-03

### Changed

- refreshed `CLAUDE.md` and `.github/copilot-instructions.md` to reflect that `composer.json` exists with `lint`/`test` scripts (CI runs `composer lint`), correcting the stale "no Composer" claim, and to reference `.env.example`

### Security

- added `composer.lock` (zero packages, only `php: >=7.2` platform constraint) so the `sca:composer-audit` CI step has a lockfile to inspect; the project has no PHP package dependencies and no security advisories apply

## [0.2.1] - 2026-05-25

### Changed

- refreshed `.github/copilot-instructions.md` to fix remaining stale CI workflow reference in directory tree (`composer.yaml` → `composer-library.yaml`)

## [0.2.0] - 2026-05-19

### Added

- added `CLAUDE.md` with build, architecture, convention, and CI guidance for Claude Code sessions

### Changed

- refreshed `.github/copilot-instructions.md` to fix stale CI workflow reference (`composer.yaml` → `composer-library.yaml`)

## [0.1.2] - 2026-04-28

### Changed

- refreshed `.github/copilot-instructions.md` to fix stale CI workflow reference (`php.yaml` → `composer.yaml`), correct database credential docs to reflect env-var configuration, and add missing bundled libraries (Bootstrap 3.3.7, Font Awesome 4.7.0)

## [0.1.1] - 2026-04-04

### Fixed

- fixed CI workflow reference after pipelines `v4.0.0` renamed `php.yaml` to `composer.yaml`

## [0.1.0] - 2026-03-12

### Added

- added GitHub Actions workflow for CI/CD pipeline

