---
name: code-review
description: "Review pull requests and diffs in usocial-network — the PHP 7.2 MVC social network — against the rios0rios0/guide standards, with extra weight on SQL injection, XSS in the view templates, session handling, and credential configuration. Use when reviewing a PR, a branch, or staged changes here."
---

# Code review — `usocial-network`

`usocial-network` is a small hand-rolled PHP MVC application: controllers in `app/controllers/`, business logic in `app/services/`, PHP templates in `app/views/`, and a `core/` framework with a PDO connection singleton, filesystem-based routing, session management, and a template renderer. There is no framework and no test suite, so security review carries the weight.

## When to use this skill

Use it whenever you are asked to review a pull request, a diff, a branch, or staged changes
in this repository — and before opening a pull request of your own, as a self-check. It is a
**review** skill: it produces findings, not commits.

## Source of truth

The canonical engineering standards live in the
**[rios0rios0/guide wiki](https://github.com/rios0rios0/guide/wiki)**. This file is a
repo-tailored index into that guide plus the rules that only apply here. Precedence, highest
first:

1. This repository's `.github/copilot-instructions.md`, `CLAUDE.md`, and `CONTRIBUTING.md` —
   they describe *this* codebase and its load-bearing invariants.
2. The **rios0rios0/guide** wiki — the shared standard.
3. General language idiom.

When the guide and a general convention disagree, the guide wins. When this file and the
guide disagree, the guide wins and this file should be corrected in the same pull request.

### Guide pages that apply here

| Topic | Page |
|-------|------|
| YAML Conventions — `.yaml`, single quotes, unquoted scalars | [YAML](https://github.com/rios0rios0/guide/wiki/YAML) |
| Architecture — Clean Architecture and SOLID | [Architecture](https://github.com/rios0rios0/guide/wiki/Architecture) |
| Backend Design — layers, actors, dependency direction | [Backend-Design](https://github.com/rios0rios0/guide/wiki/Backend-Design) |
| Mapper Design Pattern — replacing `switch`/`case` | [Mapper-Design-Pattern](https://github.com/rios0rios0/guide/wiki/Mapper-Design-Pattern) |
| Git Flow — branches, commits, SemVer, breaking changes | [Git-Flow](https://github.com/rios0rios0/guide/wiki/Git-Flow) |
| Documentation & Change Control — changelog and docs discipline | [Documentation-&-Change-Control](https://github.com/rios0rios0/guide/wiki/Documentation-&-Change-Control) |
| CHANGELOG Formatting — capitalisation and backticks | [CHANGELOG-Formatting](https://github.com/rios0rios0/guide/wiki/CHANGELOG-Formatting) |
| Security — OWASP checklist, secret hygiene, SAST | [Security](https://github.com/rios0rios0/guide/wiki/Security) |
| CI & CD — pipeline stages and the local quality gates | [CI-&-CD](https://github.com/rios0rios0/guide/wiki/CI-&-CD) |
| Code Style — baseline naming and the operations vocabulary | [Code-Style](https://github.com/rios0rios0/guide/wiki/Code-Style) |

## How to run the review

1. **Establish the range.** Resolve the default branch with
   `git symbolic-ref refs/remotes/origin/HEAD` (strip `refs/remotes/origin/`; fall back to `main`),
   then read the diff with `git diff <default>...HEAD` and the file list with
   `git diff <default>...HEAD --name-only`.
2. **Read whole files, not just hunks.** A hunk cannot show a layering violation, a missing
   test, or a duplicated helper. Open every changed file in full, plus the files it imports
   from the layer below.
3. **Check the change set as a unit** — not only the code. A change that alters behaviour,
   configuration, or architecture is incomplete without its changelog entry and its
   documentation update, and that omission is a finding in its own right.
4. **Map every finding to a rule.** Each finding must name the rule it breaks and link the
   guide page (or the repository file) that states it. A comment that cannot be traced to a
   rule is a suggestion, not a defect — label it as such.
5. **Report, do not rewrite.** Produce the review in the output format below. Only edit files
   when the request explicitly asks for fixes.

## What matters most in `usocial-network`

These are the checks that catch real defects in this repository. Work through
them before the generic ones.

- **Every query is parameterised.** `core/db/DatabaseConnection.php` gives a PDO handle; a value concatenated into SQL anywhere in `app/services/` is a **Critical** SQL-injection finding, no exceptions.
- **Every value echoed into a template is escaped.** Views under `app/views/` render user-supplied content — `htmlspecialchars` (or an equivalent helper) on output, always. An unescaped echo of a post body or a user name is a **Critical** XSS finding.
- **Passwords use `password_hash`/`password_verify`.** Anything reversible, or any comparison with `==`, is a Critical finding.
- **Session handling goes through `SessionManagement`.** Check that login regenerates the session id, that logout destroys it, and that no controller trusts a client-set value for identity.
- **Routing is filesystem-based**, so a path segment reaching a `require`/`include` is a local-file-inclusion risk. Whitelist the resolvable controllers rather than trusting the URL.
- **Database credentials come from environment variables** (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`) with the fallbacks in `DatabaseConnection.php` — a real credential committed as a fallback is a Critical finding. `.env` is never committed.
- **File uploads under `resources/`** need a type and size check and must not be executable from the web root.
- **Migrations are ordered, dated SQL files in `db/`** — a schema change without one leaves every other environment broken.
- **`composer.lock` stays committed** even with an empty package set; the CI `composer-audit` step depends on it.
- Authorisation is checked per action, not only in the view: hiding a link is not access control.

### Commands a reviewer should be able to quote

```bash
php -S localhost:8000
composer lint          # php -l over every non-vendor .php file
mysql -u root -p usocial < db/2019051801_usocial.sql
```

## Architecture and layering

See [Architecture](https://github.com/rios0rios0/guide/wiki/Architecture) and [Backend Design](https://github.com/rios0rios0/guide/wiki/Backend-Design).

Dependencies point **inward**, always. Infrastructure depends on the domain; the domain
never imports infrastructure, a framework, a driver, or an SDK. The layer boundary is the
first thing to check on any new file:

- An import of a concrete client, ORM, HTTP library, or cloud SDK from inside the domain
  layer is a **Critical** finding, no matter how convenient.
- Entities hold business rules and stay framework-agnostic — no serialisation tags, no ORM
  annotations, no transport concerns. Tags belong on DTOs in the infrastructure layer.
- New external dependencies enter through a port (an interface owned by the consumer) and an
  adapter that implements it. Code that reaches past the port to the concrete type breaks
  the pattern even when it compiles.
- Mappers isolate the layers: a repository mapper converts between models and entities, a
  controller mapper between requests/responses and entities. Leaking a transport DTO into
  the domain is a finding.
- Operation names come from the standard vocabulary: `List`, `Get`, `Insert`, `Update`,
  `Delete`, `BatchInsert`, `BatchUpdate`, `BatchDelete`, `DeleteAll`.

### Dispatch tables over `switch`

See [Mapper Design Pattern](https://github.com/rios0rios0/guide/wiki/Mapper-Design-Pattern). Two or three stable cases may stay a
`switch`. Four or more, or a set that grows with features, becomes a map from key to handler
so that adding a case is a new entry rather than an edit to the dispatcher. Flag new
`switch`/`if-else` chains that dispatch on a string or enum key.

### YAML

See [YAML Conventions](https://github.com/rios0rios0/guide/wiki/YAML). The extension is `.yaml`, never `.yml`. String values are
single-quoted; double quotes appear only where interpolation or an escape needs them;
booleans and numbers are never quoted. This applies to workflows, compose files, manifests,
and YAML blocks inside Markdown.

## Tests

`composer test` is a placeholder (`exit 0`) — there is no suite. Verification is `composer lint` plus manual exercise of the affected flow. A pull request that adds real logic should extract it into a testable service and start a suite; until then, security review substitutes for tests, so read every query and every echo.

## Documentation and change control

See [Documentation & Change Control](https://github.com/rios0rios0/guide/wiki/Documentation-&-Change-Control) and
[CHANGELOG Formatting](https://github.com/rios0rios0/guide/wiki/CHANGELOG-Formatting).

This repository uses **chlog fragments**. `CHANGELOG.md` is generated and is never edited by
hand.

- Every change ships a fragment created with `chlog new --kind <Kind> --body "…"`, staged in
  the **same commit** as the code. Kinds: `Added`, `Changed`, `Deprecated`, `Removed`,
  `Fixed`, `Security`.
- A backward-incompatible change to the public interface additionally carries `--breaking`.
  The kind alone never triggers a major bump.
- A hand-edited `CHANGELOG.md`, or a code change with no fragment under
  `.changes/unreleased/`, is a **Critical** finding — `chlog check` fails the build for it.
- Fragment bodies start with a lowercase verb in simple past tense, capitalise proper nouns
  (GitHub, Go, Docker), and wrap code identifiers and versions in backticks.
- `README.md` is updated whenever usage, setup, configuration, or architecture changes;
  `.github/copilot-instructions.md` and `CLAUDE.md` whenever the workflow, commands, or
  structure changes. Documentation and code ship in one commit.

## Git Flow and pull-request hygiene

See [Git Flow](https://github.com/rios0rios0/guide/wiki/Git-Flow) and [Merge Guide](https://github.com/rios0rios0/guide/wiki/Merge-Guide).

- Branch names are `feat/`, `fix/`, `refactor/`, `chore/`, `test/`, or `docs/` followed by a
  ticket ID or a short slug — `feat/TICKET-000`, `fix/input-mask`.
- Commit subjects are `type(SCOPE): message`: simple past tense (`added`, `fixed`, `changed`,
  `removed`), lowercase first word, no trailing period, code identifiers in backticks.
- Branches are synchronised with `git rebase`, never `git merge`. A merge commit from the
  default branch inside a feature branch is a finding.
- Breaking changes are flagged in **three** places: the commit footer
  (`**BREAKING CHANGE:** …`), the changelog, and the pull-request description. One or two of
  the three is not enough.
- Versions follow [SemVer](https://semver.org/): MAJOR for incompatible changes, MINOR for
  features, PATCH for fixes.

## Security

See [Security](https://github.com/rios0rios0/guide/wiki/Security).

- **No hard-coded secrets.** API keys, tokens, passwords, and private keys belong in
  environment variables or a secret manager — never in source, tests, fixtures, or the
  changelog. A secret that reaches a commit must be rotated, not merely deleted.
- **Never write a PEM header sentinel or a realistic key shape into a fixture**
  (`ghp_…`, `sk-…`, `AKIA…`, `xoxb-…`, JWT-shaped strings, or the dashed `BEGIN …` banners).
  Gitleaks matches the shape, not the value, so a placeholder that merely *looks* like a
  credential fails the pipeline. Use inert placeholders such as `fixture-token-placeholder`.
- **Suppressions must be justified.** Entries in `.gitleaksignore`, `.trivyignore`,
  `.semgrepignore`, or `.codeql-false-positives` need a fingerprint, a dated comment, and a
  reason. A suppression added to silence a real finding is a Critical.
- Validate and sanitise every external input; use parameterised queries; apply least
  privilege; keep secrets out of logs.
- Dependency manifest changes are reviewed for new transitive vulnerabilities. When a fix
  exists, bump the version rather than suppressing the finding.

## What not to flag

A review that raises noise gets ignored. Do not report these:

- The absence of a framework — the hand-rolled `core/` is the project.
- `resources/plugins/` holding vendored Bootstrap, jQuery, Vue, and Axios copies.
- Anything the guide does not require and this file does not list, unless it is a genuine correctness or security defect — say so plainly and label it a Suggestion.

## Review output format

```
## Code review: <branch or PR>

### Critical (must fix before merge)
- `path/to/file.ext:LINE` — <what is wrong> — violates <rule> (<guide page or repo file>)

### Warning (should fix)
- `path/to/file.ext:LINE` — <what is wrong> — violates <rule>

### Suggestion (optional)
- `path/to/file.ext:LINE` — <improvement>

### Change-control checklist
- [ ] Changelog entry present for every behavioural change
- [ ] `README.md` updated if usage, setup, or architecture changed
- [ ] `.github/copilot-instructions.md` and `CLAUDE.md` updated if the workflow, commands, or structure changed
- [ ] Commit messages follow `type(SCOPE): message` in simple past tense
- [ ] Breaking changes flagged in the commit footer, the changelog, and the PR description

### Verdict: APPROVE / REQUEST CHANGES
<one paragraph: the blocking findings, or why the change is ready>
```

## Severity

| Severity       | Use for                                                                                                                            |
|----------------|------------------------------------------------------------------------------------------------------------------------------------|
| **Critical**   | Broken dependency direction, a leaked secret, an injection or authentication flaw, a missing changelog entry, a banned mock library, a load-bearing invariant broken, a test deleted rather than fixed. |
| **Warning**    | Naming that departs from the guide, a missing test for a new branch of logic, an unexplained magic value, a stale README or instructions file, a `switch` that should be a map. |
| **Suggestion** | Readability, consistency with neighbouring modules, and performance ideas that no rule mandates.                                     |

Rank findings most severe first, and state plainly when nothing blocks the merge — an empty
Critical section is a valid, useful review.
