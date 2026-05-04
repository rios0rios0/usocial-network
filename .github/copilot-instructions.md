# Copilot Instructions

## Project Overview

USocial Network is a social networking application built with PHP 7.2+. It demonstrates modern PHP features such as object type hints and abstract function overrides. Users can register, log in, view a home feed, browse user profiles, and interact with posts. The UI template is derived from [bootstrap-social-network-template](https://github.com/fresh5447/bootstrap-social-network-template).

## Repository Structure

```
usocial-network/
├── .github/
│   └── workflows/
│       └── default.yaml        # CI/CD pipeline (delegates to shared composer-library.yaml workflow)
├── app/                        # Application layer (MVC)
│   ├── controllers/            # Request handlers in subdirectories: home/, login/, post/, user/
│   ├── services/               # Business logic: UserService, PostService
│   └── views/                  # PHP templates: login, home, user views, layouts, fragments
├── core/                       # Core framework components
│   ├── db/                     # DatabaseConnection singleton (PDO + MySQL)
│   ├── routes/                 # RoutesManagement: URL dispatch and base URL helpers
│   ├── session/                # SessionManagement singleton
│   └── views/                  # ViewsManagement: template rendering engine
├── db/                         # SQL migration scripts (run in order)
│   ├── 2019051801_usocial.sql
│   └── 2019051802_usocial.sql
├── resources/                  # Frontend static assets
│   ├── images/
│   ├── plugins/                # Bundled libraries: Bootstrap, Font Awesome, Vue.js, jQuery, Axios
│   ├── scripts/
│   └── styles/
├── index.php                   # Entry point — bootstraps RoutesManagement
├── README.md
├── CONTRIBUTING.md
└── CHANGELOG.md
```

## Technology Stack

| Layer | Technology |
|---|---|
| Language | PHP 7.2+ |
| Database | MySQL (via PDO) |
| Frontend | HTML5 |
| CSS Framework | Bootstrap 3.3.7, Font Awesome 4.7.0 |
| JavaScript | Vue.js 2.5.9, jQuery 1.11.2, Axios 0.12.0 |
| Web Server | Apache, Nginx, or PHP built-in server |
| Package Manager | None — no Composer, no npm |

## Build, Test, Lint, and Run Commands

There is no build step. The project runs directly with PHP.

### Start the development server

```bash
php -S localhost:8000
```

Then open `http://localhost:8000` in a browser.

### Lint (syntax check all PHP files)

```bash
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

This is the same check run in CI. Expect near-instant results for each file.

### Database setup

Apply the SQL migrations in order against a local MySQL instance:

```bash
mysql -u root -p usocial < db/2019051801_usocial.sql
mysql -u root -p usocial < db/2019051802_usocial.sql
```

Database credentials are configured via environment variables with fallback defaults in `core/db/DatabaseConnection.php`: `DB_HOST` (default `localhost`), `DB_USER` (default `root`), `DB_PASSWORD` (default empty), `DB_NAME` (default `usocial`). Set these env vars or edit the file for local development.

## Architecture and Design Patterns

- **MVC**: Controllers in `app/controllers/` handle HTTP requests; services in `app/services/` contain business logic; templates in `app/views/` render HTML.
- **Service Layer**: `UserService` and `PostService` encapsulate database interactions and domain logic, keeping controllers thin.
- **Singleton**: `DatabaseConnection::getInstance()` and `SessionManagement::getInstance()` ensure a single shared instance per request.
- **Central Router**: `RoutesManagement` in `core/routes/` dispatches URLs to the correct controller.
- **Template Rendering**: `ViewsManagement` in `core/views/` loads PHP view files and passes data to them.
- **PDO Prepared Statements**: Database queries use PDO with prepared statements to parameterise user input.

## Dependencies

All dependencies are bundled in the repository — there is no package manager to run.

**PHP**: Built-in PDO with `pdo_mysql` driver (must be enabled in `php.ini`).

**Bundled frontend libraries** (in `resources/plugins/`):
- Bootstrap 3.3.7
- Font Awesome 4.7.0
- Vue.js 2.5.9
- jQuery 1.11.2
- Axios 0.12.0

## CI/CD Pipeline

The pipeline is defined in `.github/workflows/default.yaml` and delegates to the shared reusable workflow at `rios0rios0/pipelines/.github/workflows/composer-library.yaml@main`.

Triggers:
- Push to `main`
- Any tag push (used for releases)
- Pull requests targeting `main`
- Manual `workflow_dispatch`

The pipeline runs PHP linting on all source files. Tagged commits produce a GitHub Release.

## Development Workflow

1. Fork the repository and clone your fork.
2. Create a feature branch: `git checkout -b feat/my-feature`
3. Make your changes.
4. Verify locally: run `php -S localhost:8000` and confirm the app works in a browser.
5. Run the linter: `find app core -name "*.php" -exec php -l {} \;`
6. Commit following the [commit conventions](https://github.com/rios0rios0/guide/wiki/Life-Cycle/Git-Flow) (e.g. `feat:`, `fix:`, `chore:`).
7. Open a pull request targeting `main`.

## Coding Conventions

- Follow the standards defined in the [Development Guide](https://github.com/rios0rios0/guide/wiki).
- Use **PHP 7.2+ features**: object type hints, return type declarations, abstract method overrides.
- Keep controllers thin — delegate logic to service classes.
- Use PDO prepared statements for all database queries; never interpolate user input directly into SQL.
- Frontend scripts and styles live in `resources/`; vendor/plugin files are placed in `resources/plugins/` and committed to the repository.
- `CHANGELOG.md` follows [Keep a Changelog](https://keepachangelog.com/) with semantic versioning.

## Common Tasks

### Add a new page/route

1. Create a controller in `app/controllers/` extending the base controller pattern.
2. Register the route in `core/routes/RoutesManagement.php`.
3. Create the corresponding view template in `app/views/`.
4. Add any business logic to a service in `app/services/`.

### Add a database migration

1. Create a new `.sql` file in `db/` with the next sequential timestamp prefix (e.g. `2019051803_usocial.sql`).
2. Write idempotent SQL (use `CREATE TABLE IF NOT EXISTS`, `ALTER TABLE ... ADD COLUMN IF NOT EXISTS`, etc.).
3. Apply it locally: `mysql -u root -p usocial < db/<filename>.sql`.

### Troubleshooting

- **Blank page / 500 error**: Enable PHP error display (`error_reporting(E_ALL); ini_set('display_errors', 1);` at the top of `index.php`) to see the actual error.
- **Database connection failure**: Check credentials in `core/db/DatabaseConnection.php` and confirm MySQL is running with the `usocial` database created.
- **`pdo_mysql` not found**: Enable the extension in `php.ini` (`extension=pdo_mysql`) and restart the server.
- **Assets not loading**: Verify the base URL generated by `RoutesManagement` matches the address you are accessing (important when running behind a proxy or on a non-root path).
