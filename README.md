# CBC Wine Menu

This repository contains a custom WordPress plugin for [CBC Cannavale](https://cbcannavale.it/), a restaurant that needs a digital wine list accessible from QR codes placed on tables.

The solution is developed as a portable WordPress plugin so it can later be installed on the existing production website without depending on the production theme or hosting details during early development.

## Overview

**CBC Wine Menu** provides a digital wine menu for restaurant guests. Each table has a permanent QR code. Scanning the code opens a table-specific URL, creates a temporary secure session (30 minutes), and grants access to the wine list on a mobile-first interface.

Restaurant staff manage wines, categories, availability, and prices from the WordPress administration area, without changing source code.

The first release focuses only on the wine menu. Future optional features may include food, cocktails, beers, allergens, anonymous statistics, and Wi-Fi based access validation.

## Features

- Digital wine menu managed from WordPress Admin
- Custom Post Type for wines and taxonomy for categories
- Permanent QR entry points per table (initially 20 tables)
- Temporary guest sessions lasting 30 minutes
- Secure server-side session validation
- Italian and English ready interface strings
- Mobile-first responsive frontend
- Plugin-based architecture, independent from the production theme

## Technologies Used

### Application

- **WordPress** as the CMS and administration platform
- **Custom WordPress plugin** (`cbc-wine-menu`) for all business logic
- **PHP 8.3**
- **MariaDB 10.11**
- **Apache** as the local web server
- Native WordPress APIs (Custom Post Types, Taxonomies, Rewrite API, Settings API, i18n)

### Local development

- **Lando** for the local WordPress environment
- **WP-CLI** for WordPress installation and management
- **Git** for version control
- **WSL2 / Ubuntu** on Windows 11

## Getting Started

Before starting, ensure you have installed:

1. **Git**
2. **Docker Desktop** (required by Lando)
3. **Lando**
4. **WSL2** with Ubuntu (recommended on Windows)

## Installation Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/vinsgiordi/cbc-wine-menu.git
cd cbc-wine-menu
git checkout develop
```

The primary development branch is `develop`.

### 2. Start Lando

```bash
lando start
lando info
```

Expected local site:

- https://cbc-wine-menu.lndo.site

### 3. Install WordPress

WordPress core is **not** committed to this repository. Install it locally inside `wordpress/`:

```bash
lando wp core download
lando wp config create \
  --dbname=wordpress \
  --dbuser=wordpress \
  --dbpass=wordpress \
  --dbhost=database
lando wp core install \
  --url="https://cbc-wine-menu.lndo.site" \
  --title="CBC Wine Menu" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@example.com
```

Then confirm the installation:

```bash
lando wp core version
```

Open:

- Frontend: https://cbc-wine-menu.lndo.site
- Admin: https://cbc-wine-menu.lndo.site/wp-admin

Local admin credentials above are for development only. Do not reuse them in production.

### 4. Activate the Plugin

When the plugin is available in the repository:

1. Go to **Plugins** in WordPress Admin
2. Activate **CBC Wine Menu**

Or via WP-CLI:

```bash
lando wp plugin activate cbc-wine-menu
```

## Running and Development

### Start / stop the environment

```bash
lando start
lando stop
lando info
```

### Useful WP-CLI commands

```bash
lando wp core version
lando wp plugin list
lando wp plugin activate cbc-wine-menu
```

### Local URLs

Table entry points will follow this pattern:

```text
https://cbc-wine-menu.lndo.site/vini/tavolo/01
https://cbc-wine-menu.lndo.site/vini/tavolo/02
...
https://cbc-wine-menu.lndo.site/vini/tavolo/20
```

Production URLs will follow the same path structure on https://cbcannavale.it/ once deployment is ready.

## Project Structure

```text
cbc-wine-menu/
├── .gitignore
├── .lando.yml
├── README.md
└── wordpress/                          # local WordPress (ignored by Git)
    └── wp-content/
        └── plugins/
            └── cbc-wine-menu/          # custom plugin (tracked by Git)
```

Only the custom plugin and project configuration are versioned. WordPress core, `wp-config.php`, uploads, and local secrets stay out of Git.

## Database Management

Lando provides a MariaDB service. Default local credentials:

- **Database:** `wordpress`
- **User:** `wordpress`
- **Password:** `wordpress`
- **Internal host:** `database`
- **Internal port:** `3306`

For the current external host port, run:

```bash
lando info
```

Do not commit database dumps or production data to this public repository.

## Security Notes

This repository is public. Never commit:

- `wp-config.php`
- production credentials
- API keys or tokens
- database dumps
- customer personal data
- `.env` files containing secrets

Temporary sessions protect access lifetime. They do **not** prove that a guest is physically inside the restaurant if a QR code photo is shared.

## Contributing

Contributions are welcome. If you have suggestions, bug fixes, or improvements:

1. Create a feature branch from `develop`
2. Keep commits focused and written in English
3. Open a pull request against `develop`

## License

License details for this repository have not been finalized yet. A `LICENSE` file will be added when the project licensing is confirmed.

## Contact

For inquiries or assistance, feel free to contact [vincenzogiordano99@libero.it](mailto:vincenzogiordano99@libero.it).
