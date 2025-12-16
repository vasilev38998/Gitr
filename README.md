# Social Network (PHP)

Base PHP project structure for a social network, intended for **Ubuntu 22.04 + Apache + MySQL**.

## Structure

- `public/` — Apache document root (entrypoint `index.php`)
- `src/` — PHP application code
- `config/` — configuration
- `database/` — SQL scripts / migrations
- `templates/` — PHP/HTML templates
- `assets/` — CSS/JS/images

## Quick start

1. Install dependencies:

```bash
composer install
```

2. Create the database:

```bash
mysql -u root -p < database/init.sql
```

3. Configure DB connection via env vars:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

4. Point Apache DocumentRoot to `public/` and ensure `mod_rewrite` is enabled.
