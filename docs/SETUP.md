# Local Setup (run on your laptop)

Needs PHP 8.2+ (8.3 recommended), Composer, MySQL 8 (or MariaDB 10.6+), Node 20+, Git.
Windows: XAMPP or Laragon provides PHP and MySQL. Enable extensions: mbstring, openssl, pdo_mysql, zip, sodium.

## 1. Create the Laravel app

```bash
composer create-project laravel/laravel fam-secure-app
cd fam-secure-app
git init
```

## 2. Copy in the Week 1 files

From this `fam-secure/` folder, copy into the app (merge folders):

- `database/migrations/*` → `database/migrations/`
- `database/seeders/*` → `database/seeders/`
- `docs/` → `docs/`
- `.github/` → `.github/`
- `README.md`, `SECURITY.md` → project root

## 3. Database

Create a database and a dedicated user (not root):

```sql
CREATE DATABASE fam_secure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fam_app'@'localhost' IDENTIFIED BY 'choose-a-long-random-password';
GRANT SELECT, INSERT, UPDATE, DELETE ON fam_secure.* TO 'fam_app'@'localhost';
```

For migrations, use an account that can CREATE, ALTER, DROP and TRIGGER (a separate `fam_migrate` user is best). The runtime user above stays limited.

In `.env` set `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `APP_DEBUG=false` for anything beyond local work.

## 4. Register the seeders and run

In `database/seeders/DatabaseSeeder.php`'s `run()`:

```php
$this->call([RolePermissionSeeder::class, DemoUserSeeder::class]);
```

```bash
php artisan migrate --seed
```

The seeder prints the demo passwords once. Save them somewhere private.

## 5. First commit

```bash
git add . && git commit -m "Initial Laravel app with Week 1 schema and docs"
```

Check that `.env` is NOT staged (Laravel's default `.gitignore` excludes it).

## Known caveats

The migrations were written without being run (no Composer or MySQL in the authoring environment). Expect small fixes on first `migrate`; the CHECK constraint needs MySQL 8.0.16+ or MariaDB 10.2+.
