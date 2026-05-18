# Final database guide (ngn_clean + production)

## What each thing is

| Piece | Where it lives | Purpose |
|--------|----------------|---------|
| **`database/migrations/unified-sync/`** | Repo | One-time / bootstrap `CREATE TABLE` set (~235 tables). Builds structure when target DB is empty or mismatched. |
| **`database/migrations/`** (normal) | Repo | Day-to-day app changes (`ALTER`, new tables). Run on `ngn_clean` while developing. |
| **`database/migrations/prod-align-ngnclean/`** | Repo | Generated gap-fix: what **production** is missing vs `ngn_clean`. Run **on production** only. |
| **`database/schema/ngn-clean/`** | Repo (committed) | Portable canonical schema (`manifest.json` + `tables/*.sql`). No `ngn_clean` DB needed on server. |
| **`db:export-ngnclean-schema`** | Local Mac | Export live `ngn_clean` → snapshot folder. Commit after schema changes. |
| **`db:review-prod-ngnclean-schema`** | Local Mac | Full table/column diff prod vs `ngn_clean` (no data). Regenerates `prod-align-ngnclean` if needed. |
| **`db:sync-prod-to-connected`** | Local / staging | **Gets row data from production** (`SYNC_PROD_*`). **Applies structure from snapshot** (default). **Writes to `DB_*` target** — not production. |

**Rule:** Production (`SYNC_PROD_*`) = **data source**. `ngn_clean` / snapshot = **schema source**. Never copy production structure over `ngn_clean` columns.

---

## Scenario A — You have **nothing** locally (empty MySQL)

Goal: local `ngn_clean` with correct tables + production data.

### `.env`

```env
DB_DATABASE=ngn_clean
SYNC_PROD_DB_HOST=...
SYNC_PROD_DB_DATABASE=...
SYNC_PROD_DB_USERNAME=...
SYNC_PROD_DB_PASSWORD=...
```

Create empty database `ngn_clean`.

### Path 1 — Recommended (snapshot already in git)

```bash
php artisan db:sync-prod-to-connected --deep-clone --force
```

What happens:

1. Reads **`database/schema/ngn-clean/`** for each table: `DROP` → `CREATE` (canonical).
2. Copies **rows from production** for columns that exist on both sides.
3. New `ngn_clean` columns stay `NULL`/default until prod is aligned.

If target schema does not match snapshot, command may run **`unified-sync`** first (`--prepare-schema` inside `--deep-clone`), then sync.

### Path 2 — No snapshot in repo yet

```bash
php artisan migrate --path=database/migrations/unified-sync --force
php artisan migrate --force
php artisan db:export-ngnclean-schema
git add database/schema/ngn-clean
php artisan db:sync-prod-to-connected --deep-clone --force
```

**Yes — `SyncProdToNgnlocalCommand` with `--deep-clone` is what you run when local DB is empty** (after snapshot exists or after export).

---

## Scenario B — Normal development (you already have `ngn_clean`)

1. Change code + add/run **normal** migrations on local `ngn_clean`.
2. `php artisan db:export-ngnclean-schema`
3. Commit `database/schema/ngn-clean/`
4. Optional: refresh prod data locally  
   `php artisan db:sync-prod-to-connected --deep-clone --force`

You do **not** re-run `unified-sync` every day — only for fresh DB or if bootstrap is broken.

---

## Scenario C — Production live site (structure behind app)

**Do not** run `db:sync-prod-to-connected --deep-clone` with `DB_*` pointing at live production (drops tables).

On the **production server** (app DB = `DB_*`):

```bash
php artisan migrate --force
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
```

Regenerate alignment migrations locally when needed:

```bash
php artisan db:review-prod-ngnclean-schema --generate-migrations
# commit prod-align-ngnclean, deploy, migrate on prod
```

Verify:

```bash
php artisan db:sync-prod-to-connected --compare-vs-ngnclean
```

(Data on prod is already there — you only fixed **columns/tables**.)

---

## Your question: order of unified-sync → prod-align → export → sync?

| Step | Run where | When |
|------|-----------|------|
| `migrate --path=unified-sync` | Local | Fresh empty DB **or** no snapshot; optional if `--deep-clone` already runs it |
| `migrate` (app) | Local | Always while developing |
| `migrate --path=prod-align-ngnclean` | **Production** | Prod missing tables/columns vs `ngn_clean` |
| `db:export-ngnclean-schema` | **Local** | After `ngn_clean` schema is correct — **commit snapshot** |
| `db:sync-prod-to-connected --deep-clone` | **Local/staging** | Pull **production data** into local target — **not** for fixing prod schema |

**Export is not “getting data from production”.** Export only dumps **structure** from local `ngn_clean`.  
**Sync is “getting data from production”.** It uses `SYNC_PROD_*` as read-only source.

---

## Important commands (copy-paste)

```bash
# Local: empty DB → prod data + snapshot schema
php artisan db:sync-prod-to-connected --deep-clone --force

# Local: export canonical schema (commit result)
php artisan db:export-ngnclean-schema

# Local: diff prod vs ngn_clean (no data)
php artisan db:review-prod-ngnclean-schema

# Local: prod vs committed snapshot
php artisan db:sync-prod-to-connected --compare-vs-ngnclean

# Production: align schema only
php artisan migrate --path=database/migrations/prod-align-ngnclean --force
```

---

## Never

- `php artisan migrate:refresh` on production or `ngn_clean`
- `db:sync-prod-to-connected --deep-clone --force` with target = live production DB
- `--use-production-schema` (drops `ngn_clean`-only columns)

---

## One-line mental model

**Migrations build `ngn_clean` → export freezes structure → sync copies production rows into that structure locally → prod-align closes gaps on the live server.**
