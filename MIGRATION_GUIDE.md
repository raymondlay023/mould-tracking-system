# SQLite to MySQL/PostgreSQL Migration Guide

## Overview

This guide provides step-by-step instructions for migrating your Mould Tracking System database from SQLite (development) to MySQL or PostgreSQL (production).

## Prerequisites

- Backup of current SQLite database
- MySQL 8.0+ or PostgreSQL 13+ server
- Database credentials (host, port, username, password, database name)
- SSH access to production server (if applicable)

## Method 1: Fresh Installation (Recommended for New Deployments)

### Step 1: Create Production Database

**MySQL:**
```sql
CREATE DATABASE mould_tracking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mould_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON mould_tracking.* TO 'mould_user'@'localhost';
FLUSH PRIVILEGES;
```

**PostgreSQL:**
```sql
CREATE DATABASE mould_tracking;
CREATE USER mould_user WITH ENCRYPTED PASSWORD 'strong_password';
GRANT ALL PRIVILEGES ON DATABASE mould_tracking TO mould_user;
```

### Step 2: Update `.env` Configuration

**MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mould_tracking
DB_USERNAME=mould_user
DB_PASSWORD=strong_password
```

**PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mould_tracking
DB_USERNAME=mould_user
DB_PASSWORD=strong_password
```

### Step 3: Run Migrations

```bash
php artisan migrate --force
```

### Step 4: Seed Data (if needed)

```bash
# Seed roles and admin user
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder

# Or seed demo data
php artisan db:seed
```

## Method 2: Data Migration (For Existing Data)

### Step 1: Export SQLite Data

```bash
# Install sqlite3 command line tool
# Export to SQL dump
sqlite3 database/database.sqlite .dump > sqlite_dump.sql
```

### Step 2: Convert SQL Dump

The SQLite dump needs manual conversion:

1. **Remove SQLite-specific syntax:**
   - Remove `BEGIN TRANSACTION;` and `COMMIT;`
   - Remove `PRAGMA` statements
   - Change `AUTOINCREMENT` to `AUTO_INCREMENT` (MySQL) or `SERIAL` (PostgreSQL)

2. **Fix UUID columns:**
   - SQLite stores UUIDs as text
   - MySQL/PostgreSQL may need `CHAR(36)` or UUID type

3. **Fix timestamps:**
   - Ensure datetime formats are compatible

### Step 3: Create Production Database

Same as Method 1, Step 1.

### Step 4: Import Data

**MySQL:**
```bash
mysql -u mould_user -p mould_tracking < converted_dump.sql
```

**PostgreSQL:**
```bash
psql -U mould_user -d mould_tracking -f converted_dump.sql
```

### Step 5: Verify Migration

```bash
# Update .env to production database
php artisan tinker

# Verify data
>>> \App\Models\Mould::count()
>>> \App\Models\ProductionRun::count()
>>> \App\Models\MaintenanceEvent::count()
```

## Method 3: Laravel Eloquent Export/Import (Safest)

### Step 1: Export Data Using Command

Create a custom Artisan command:

```php
<?php
// app/Console/Commands/ExportData.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Mould, ProductionRun, MaintenanceEvent, Plant, Zone, Machine};

class ExportData extends Command
{
    protected $signature = 'data:export {file}';
    protected $description = 'Export all data to JSON file';

    public function handle()
    {
        $data = [
            'plants' => Plant::all()->toArray(),
            'zones' => Zone::all()->toArray(),
            'machines' => Machine::all()->toArray(),
            'moulds' => Mould::all()->toArray(),
            'production_runs' => ProductionRun::all()->toArray(),
            'maintenance_events' => MaintenanceEvent::all()->toArray(),
        ];

        file_put_contents($this->argument('file'), json_encode($data, JSON_PRETTY_PRINT));
        $this->info('Data exported successfully!');
    }
}
```

### Step 2: Export from SQLite

```bash
php artisan data:export export.json
```

### Step 3: Switch to Production Database

Update `.env` as shown in Method 1.

### Step 4: Import Data

Create import command:

```php
<?php
// app/Console/Commands/ImportData.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Mould, ProductionRun, MaintenanceEvent, Plant, Zone, Machine};

class ImportData extends Command
{
    protected $signature = 'data:import {file}';
    protected $description = 'Import data from JSON file';

    public function handle()
    {
        $data = json_decode(file_get_contents($this->argument('file')), true);

        foreach ($data['plants'] as $item) {
            Plant::create($item);
        }

        foreach ($data['zones'] as $item) {
            Zone::create($item);
        }

        // ... repeat for all models

        $this->info('Data imported successfully!');
    }
}
```

```bash
php artisan migrate --force
php artisan data:import export.json
```

## Post-Migration Checklist

- [ ] Verify all tables exist: `php artisan migrate:status`
- [ ] Check record counts match SQLite database
- [ ] Test login with existing users
- [ ] Verify foreign key relationships
- [ ] Test creating new production run
- [ ] Test maintenance event creation
- [ ] Run application tests: `php artisan test`
- [ ] Check dashboard loads correctly
- [ ] Verify reports display data
- [ ] Test Excel export functionality

## Performance Optimization

After migration, optimize the database:

**MySQL:**
```sql
OPTIMIZE TABLE moulds, production_runs, maintenance_events;
ANALYZE TABLE moulds, production_runs, maintenance_events;
```

**PostgreSQL:**
```sql
VACUUM ANALYZE;
```

## Rollback Strategy

Always keep the SQLite database backup:

```bash
# Backup before migration
cp database/database.sqlite database/database.sqlite.backup

# Rollback if needed
cp database/database.sqlite.backup database/database.sqlite
# Revert .env to SQLite configuration
```

## Troubleshooting

**Error: "Access denied for user"**
- Verify database credentials
- Check user permissions: `SHOW GRANTS FOR 'mould_user'@'localhost';`

**Error: "Unknown database"**
- Ensure database was created
- Check database name in `.env`

**UUID errors**
- Ensure UUID columns are `CHAR(36)` in MySQL
- Use `UUID` type in PostgreSQL

**Timestamp timezone issues**
- Set timezone in database configuration
- Use `timestampTz` in migrations for timezone awareness

## Additional Resources

- [Laravel Database Documentation](https://laravel.com/docs/database)
- [MySQL Migration Best Practices](https://dev.mysql.com/doc/refman/8.0/en/migration.html)
- [PostgreSQL Migration Guide](https://www.postgresql.org/docs/current/migration.html)

---

**Last Updated**: January 2026
