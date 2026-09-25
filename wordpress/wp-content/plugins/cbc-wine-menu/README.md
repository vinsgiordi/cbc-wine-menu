# CBC Wine Menu Plugin

Custom WordPress plugin for the CBC Cannavale digital wine menu.

This directory contains the portable plugin that will later be installed on the production WordPress website.

## Current status

Milestone 2 — plugin bootstrap.

## Structure

```text
cbc-wine-menu/
├── cbc-wine-menu.php
├── includes/
│   └── Autoloader.php
├── src/
│   ├── Activator.php
│   ├── Deactivator.php
│   ├── Plugin.php
│   ├── PostTypes/
│   ├── Taxonomies/
│   ├── Sessions/
│   ├── Tables/
│   └── Admin/
├── admin/
├── public/
├── assets/
│   ├── css/
│   └── js/
├── templates/
└── languages/
```

Business logic belongs in this plugin, not in a theme.
