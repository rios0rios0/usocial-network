# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This file is not edited by hand. Every change writes its own fragment under
`.changes/unreleased/` with [chlog](https://github.com/luizjhonata/chlog), and a release compiles
the pending fragments into a version section here — so two branches each adding an entry no
longer touch the same lines, and a rebase that used to conflict on this file now conflicts on
nothing.

When a new release is proposed:

1. Create a new branch `bump/x.x.x` (this isn't a long-lived branch!!!);
2. The fragments pending under `.changes/unreleased/` are compiled into a version section by `chlog batch auto && chlog merge` (AutoBump does this for you — it reads the fragments directly);
3. Open a Pull Request with the bump version changes targeting the `main` branch;
4. When the Pull Request is merged, a new Git tag must be created using [GitHub environment](https://github.com/rios0rios0/usocial-network/tags).

Releases to productive environments should run from a tagged version.
Exceptions are acceptable depending on the circumstances (critical bug fixes that can be cherry-picked, etc.).

## [Unreleased]

## [0.4.0] - 2026-08-28

### Added

- added the Claude automated code review and `@claude` mention responder workflows, `claude-review.yaml` and `claude-mention.yaml`, matching the `reusable-claude-review.yaml` / `reusable-claude-mention.yaml` definitions they call in `rios0rios0/pipelines`, authenticating with the `CLAUDE_CODE_OAUTH_TOKEN` secret

### Fixed

- restored the `.changes/unreleased/` directory with a `.gitkeep`, so the release tooling keeps recognising this project as [chlog](https://github.com/luizjhonata/chlog)-based after a release consumes the last fragment. Git tracks files rather than directories, so the bump commit that removed the final fragment removed the directory too, and the next run read the empty `[Unreleased]` section as "nothing to release"
- restored the `id-token: write` permission on both Claude workflow callers. Without it the caller grants less than the reusable workflow declares, which GitHub rejects before the job starts -- runs ended in `startup_failure`. The action needs the scope because `setupGitHubToken()` exchanges a GitHub OIDC token for the GitHub App token it posts with, unless a `github_token` is passed explicitly.

### Removed

- removed the unused `id-token: write` permission from the Claude workflow callers, and changed `claude-review.yaml`'s display name to `Claude Review` so it matches its file name and its `Claude Mention` sibling. `anthropics/claude-code-action` needs `id-token: write` only for workload identity federation or the Bedrock / Vertex / Foundry OIDC paths; these authenticate with `claude_code_oauth_token`, so the scope allowed minting OIDC tokens for any audience without ever being used.

## [0.3.0] - 2026-08-26

### Added

- added a tailored `code-review` skill under `.github/skills/` so GitHub Copilot reviews changes against the [rios0rios0/guide](https://github.com/rios0rios0/guide/wiki) standards and this repository's own load-bearing invariants

### Changed

- changed the changelog to [chlog](https://github.com/luizjhonata/chlog) fragments: a change now writes its own YAML file under `.changes/unreleased/` through `chlog new --kind <Kind> --body "..."`, and `CHANGELOG.md` is GENERATED from them at release time by `chlog batch auto && chlog merge`. That is the one thing a single shared file cannot do — two branches each adding an entry no longer touch the same lines, so a rebase that used to conflict on `CHANGELOG.md` now conflicts on nothing. The `[Unreleased]` section was empty, so nothing had to be carried across. AutoBump already reads the fragments directly, so the release flow is unchanged.

### Fixed

- fixed the `main` pipeline, which every repository's `sast:gitleaks` job had been failing since the code-review skill landed: the skill's own security bullet listed credential prefixes verbatim to warn against writing them, and the scanner's second pass matches those prefixes on their own, so the warning tripped the rule it was describing. The bullet now names the vendors instead, and the commit that carried the original wording is allowlisted by fingerprint in `.gitleaksignore`, because the scan walks the whole history reachable from `HEAD` and no edit at the tip can clear a past commit. No credential was ever committed.

## [0.2.4] - 2026-08-04

### Changed

- refreshed `CLAUDE.md` and `.github/copilot-instructions.md` to document that the CI pipeline runs security scanning beyond `composer lint` — an SCA `composer-audit` step (which is why the empty `composer.lock` is committed) and semgrep SAST (`.semgrepignore`)

## [0.2.3] - 2026-06-09

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

