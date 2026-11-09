# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A WordPress theme (`wp-content/themes/portfolio/`) for a personal portfolio site. It includes a homepage with an animated IDE-style hero, a filterable recipe archive with OCR-powered auto-population, and portfolio project cards driven by ACF fields.

WordPress core is not tracked in this repo. Run it locally with Docker (`docker-compose.yml` at the repo root):

```bash
docker compose up -d                                   # http://localhost:8080 (PHP 8.5)
docker compose run --rm cli plugin list                # WP-CLI
docker compose exec wordpress tail -f /var/www/html/wp-content/debug.log
```

- Put a `.sql` dump in `db/` before the first `up` to auto-import it (`*.sql` is gitignored).
- `FREE_OCR_API_KEY` is read from a gitignored `.env` file.
- To test another PHP version, change the `php8.5` image tags.

The minimum supported PHP is **8.2.35** (security floor). Composer enforces this via `vendor/composer/platform_check.php`.

## Theme Directory

All theme work happens in `wp-content/themes/portfolio/`.

## SCSS Compilation

There is **no Node.js build step**. SCSS is compiled server-side by `scssphp/scssphp` 2.x, installed via Composer in the theme. `vendor/` is committed, so deploys need no Composer:

- Edit any `.scss` file in `css/`
- The browser requests `css/style.php?p=style.scss`, which compiles on the fly
- Compiled output is cached as `css/scss_cache/style-<mtime>.css`
- To update the compiler, run `composer update` in the theme directory. `config.platform.php` is pinned to 8.2.35 so the lock stays installable on the minimum PHP version.
- Cache-busting version is set automatically via `filemtime()` of the newest `.scss` file

To force recompilation, touch any `.scss` file or clear the `css/scss_cache/` directory.

## JavaScript

JS files in `js/` are loaded via `ftheme::add_script()` — to add a new script, call that method in `functions.php`. Scripts are versioned via `filemtime()` automatically.

- `script.js` — global nav (smooth scroll, mobile menu toggle)
- `home.js` — IDE typing animation on the hero; has `bypassAnimation()` for hash-based skipping
- `recipes.js` — AJAX taxonomy filtering, URL param management, pagination

## Architecture

### PHP Class Structure

- `ftheme` (functions.php) — asset enqueueing, ACF options page, partials loader, `build_project()` helper
- `Recipes` (functions/recipes.php) — CPT + taxonomy registration, OCR hook, archive query filtering
- `fld_images` (functions/images.php) — image size registration, `get_image()` / `get_alt_text()` helpers

### Custom Post Type: Recipes

Registered in `functions/recipes.php`. Taxonomies: `source` and `cuisine`. The `meals` taxonomy is intentionally commented out.

**OCR auto-population**: when a Recipe post is saved via the REST API (`rest_after_insert_recipes`), the `recipe_image_OCR()` method fires, sends the `recipe_image` ACF field to FreeOCR AI (via RapidAPI), and populates `ingredients`, `cooking_instructions`, `servings`, `time`, and `page` ACF fields automatically. The `editing` ACF field acts as a flag to skip OCR on subsequent saves.

### ACF Dependency

The theme requires the Advanced Custom Fields plugin. Homepage content (hero, about me, projects) is entirely managed through ACF fields on an Options page. Recipe fields are also ACF-based.

### Partials System

`ftheme::load_partials()` scans and auto-includes all `.php` files from a `partials/` directory. Load order is filesystem order (alphabetical).

## Key SCSS Variables (`css/vars-mixins.scss`)

- Fonts: `$o` = Open Sans, `$d` = Dosis
- Breakpoints: `$tablet` = 1040px, `$mobile` = 800px
- Colors follow an IDE/dark theme palette

## External Dependencies (CDN)

- jQuery 3.3.1 (Cloudflare)
- Bootstrap 4.0.0-beta.2
- Font Awesome 4.7.0 + v6 Kit
- Google Fonts (Open Sans, Dosis)

## API Keys

The OCR feature in `functions/recipes.php` uses a RapidAPI key for the FreeOCR AI service. Store it in `wp-config.php` or a local config — never commit it directly.