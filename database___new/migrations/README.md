# Clean Production Migrations

**Generated from production database**: `nqfkhvtysa@46.101.2.204`  
**Target**: Fresh installation with exact schema clone

## Overview

This directory contains **clean, dependency-ordered migrations** generated directly from the production database schema. No redundancies, no duplicates – just the exact structure needed to recreate 220+ tables with perfect relationships.

## Structure

- **Individual table migrations** – One file per table, numbered by dependency order
- **Circular foreign key migration** – Handles tables with mutual references
- **View migrations** – Database views created last

## Quick Start

### Prerequisites

1. **Fresh database** (recommended name: `ngn_clean`)
   ```sql
   CREATE DATABASE ngn_clean CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Update `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=ngn_clean
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

### Run Migrations

```bash
# Run all migrations in order
php artisan migrate

# Check migration status
php artisan migrate:status
```

**Expected result**: All 220+ tables created with no errors ✓

### Import Production Data

After migrations complete successfully:

```bash
php artisan db:seed --class=ProductionDataSeeder
```

This imports:
- All users (exact IDs preserved)
- All customers (exact IDs preserved)  
- All motorbikes & motorcycles
- All finance applications
- All rentals & bookings
- All judopay records
- Everything else with exact relationships intact

## For Production Deployment

### Scenario 1: Fresh Production Database

```bash
# 1. Backup old DB (if exists)
mysqldump -u root -p old_database > backup_$(date +%Y%m%d).sql

# 2. Create fresh database
mysql -u root -p -e "CREATE DATABASE production_db"

# 3. Update production .env
DB_DATABASE=production_db

# 4. Run migrations
php artisan migrate --force

# 5. Import data
php artisan db:seed --class=ProductionDataSeeder --force
```

### Scenario 2: Existing Production with Data

**⚠️ DO NOT run migrations on existing production DB with data!**

These migrations are for **fresh installations only**. To update an existing database, you need migration patches, not baseline creation.

## Regenerating from Production

If you need to refresh these migrations from the current production state:

### Option 1: SSH Tunnel (if remote)

```bash
# Terminal 1: Create SSH tunnel
ssh -L 3307:localhost:3306 your_user@46.101.2.204

# Terminal 2: Generate migrations
USE_SSH_TUNNEL=1 php scripts/generate_production_migrations.php

# Terminal 3: Export data
USE_SSH_TUNNEL=1 php scripts/export_production_data.php
```

### Option 2: Direct Connection (if whitelisted)

```bash
# Generate migrations
php scripts/generate_production_migrations.php

# Export data  
php scripts/export_production_data.php
```

### Option 3: Run on Production Server

```bash
# SSH into production
ssh your_user@46.101.2.204

# Upload scripts
scp scripts/*.php your_user@46.101.2.204:/tmp/

# Run on server
cd /path/to/production/app
php /tmp/generate_production_migrations.php
php /tmp/export_production_data.php

# Download generated files
scp -r your_user@46.101.2.204:/path/to/app/database/migrations/ ./database/
scp -r your_user@46.101.2.204:/path/to/app/database/seeders/ ./database/
```

## Verification

After running migrations, verify schema matches production:

```bash
php scripts/verify_schema.php
```

This compares:
- Table structure
- Column types and attributes
- Indexes and keys
- Foreign key relationships
- Views

Any differences will be reported.

## Troubleshooting

### Migration Fails

**Check migration status:**
```bash
php artisan migrate:status
```

**Roll back last batch:**
```bash
php artisan migrate:rollback
```

**Fresh start:**
```bash
php artisan migrate:fresh
```

### Foreign Key Errors

Ensure tables are created before their foreign keys:
- Check migration numbering (001, 002, 003...)
- Circular FKs are added in separate migration
- Run with `--force` in production

### Connection Timeouts

Production DB connection timing out? Use SSH tunnel (see above).

## Migration Files

All files follow Laravel naming convention:
- `2024_01_01_000000_001_create_tablename_table.php`
- Numbered in dependency order
- Each creates one table/view
- All use `DB::unprepared()` for exact DDL match

## Data Export

See `database/seeders/ProductionData/README.md` for:
- Individual table seeders
- Row counts per table
- Usage instructions

---

**Last generated**: Check file timestamps  
**Source**: `nqfkhvtysa` production database  
**Tables**: 220+  
**Total data rows**: See seeder README
