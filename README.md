<h1 align="center">USocial Network</h1>
<p align="center">
    <a href="https://github.com/rios0rios0/usocial-network/releases/latest">
        <img src="https://img.shields.io/github/release/rios0rios0/usocial-network.svg?style=for-the-badge&logo=github" alt="Latest Release"/></a>
    <a href="https://github.com/rios0rios0/usocial-network/blob/main/LICENSE">
        <img src="https://img.shields.io/github/license/rios0rios0/usocial-network.svg?style=for-the-badge&logo=github" alt="License"/></a>
    <a href="https://github.com/rios0rios0/usocial-network/actions/workflows/default.yaml">
        <img src="https://img.shields.io/github/actions/workflow/status/rios0rios0/usocial-network/default.yaml?branch=main&style=for-the-badge&logo=github" alt="Build Status"/></a>
</p>

An unknown social network application built with PHP **7.2**, leveraging modern language features such as Object Type Hint, Abstract Function Override, and others.

## Features

- Social networking functionality built with PHP 7.2
- Modern PHP features including object type hints and abstract function overrides
- Template forked from: [bootstrap-social-network-template](https://github.com/fresh5447/bootstrap-social-network-template)

## Installation

```bash
git clone https://github.com/rios0rios0/usocial-network.git USocial-Network
mysql -u root -p < USocial-Network/db/2019051802_usocial.sql
```

The application hardcodes its base path as `/USocial-Network` and builds URLs without a port. Clone it into a directory with exactly that name, then configure your web server (Apache/Nginx) to serve the checkout's **parent** directory on port 80. The SQL file is a full dump: it creates the `usocial` database and replaces every table with seed data. Database credentials come from the `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` environment variables (see `.env.example`).

## Usage

Open `http://localhost/USocial-Network/` (or your server's equivalent) to register an account and start using the social network features.

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

This project is available under the license specified in the [LICENSE](LICENSE) file.
