# Contributing

Contributions are welcome. By participating, you agree to maintain a respectful and constructive environment.

For coding standards, testing patterns, architecture guidelines, commit conventions, and all
development practices, refer to the **[Development Guide](https://github.com/rios0rios0/guide/wiki)**.

## Prerequisites

- [PHP 7.2+](https://www.php.net/downloads) installed and available in your `PATH`
- A text editor or IDE (e.g. PhpStorm, VS Code)
- Git

## Running the Application

### 1. Clone and Enter the Project

The application hardcodes its base path as `/USocial-Network` (`RoutesManagement::$app`), so
clone it into a directory with exactly that name:

```bash
git clone https://github.com/rios0rios0/usocial-network.git USocial-Network
cd USocial-Network
```

### 2. Set Up the Database

Each `db/*.sql` file is a full MySQL dump: it creates the `usocial` database if missing, then
drops and recreates every table with seed data. Load the newest one:

```bash
mysql -u root -p < db/2019051802_usocial.sql
```

Credentials are read from the `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` environment
variables (fallback defaults are in `core/db/DatabaseConnection.php`). No `.env` file is loaded;
`.env.example` only lists the names, so export them before starting the server.

### 3. Start the Development Server

The project has no external dependencies to install. Use PHP's built-in web server from the
**parent** directory of the checkout. `base_url()` omits the port from every asset and link, so
the server must listen on port 80 (this may need elevated privileges):

```bash
cd ..
php -S localhost:80
```

### 4. Access the Application

Open your browser and navigate to:

```
http://localhost/USocial-Network/
```

You will be redirected to the sign-up/login page. From there you can register an account and explore
the social network features (home feed, user profiles, user listing).

### 5. Run the Linter (CI)

The project uses a shared CI pipeline for linting, which runs `composer lint`. To run the same
checks locally, use:

```bash
composer lint
```

Or, without Composer:

```bash
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

This performs a syntax check on all PHP files, which is the baseline validation also executed in CI.

## Development Workflow

1. Fork and clone the repository
2. Create a branch: `git checkout -b feat/my-change`
3. Make your changes
4. Verify the application runs correctly (see "Start the Development Server" above)
5. Commit following the [commit conventions](https://github.com/rios0rios0/guide/wiki/Git-Flow)
6. Open a pull request against `main`
