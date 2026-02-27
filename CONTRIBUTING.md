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

```bash
git clone https://github.com/rios0rios0/usocial-network.git
cd usocial-network
```

### 2. Start the Development Server

The project has no external dependencies to install. Use PHP's built-in web server to run the
application locally:

```bash
php -S localhost:8000
```

### 3. Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

You will be redirected to the sign-up/login page. From there you can register an account and explore
the social network features (home feed, user profiles, user listing).

### 4. Run the Linter (CI)

The project uses a shared CI pipeline for linting. To run the same checks locally, use:

```bash
php -l index.php
find app core -name "*.php" -exec php -l {} \;
```

This performs a syntax check on all PHP files, which is the baseline validation also executed in CI.

## Development Workflow

1. Fork and clone the repository
2. Create a branch: `git checkout -b feat/my-change`
3. Make your changes
4. Verify the application runs correctly (`php -S localhost:8000`)
5. Commit following the [commit conventions](https://github.com/rios0rios0/guide/wiki/Life-Cycle/Git-Flow)
6. Open a pull request against `main`
