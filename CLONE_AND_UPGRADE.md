# NGNMOTOR: Get code from NGM-Web (newgo), create NEW repo, then upgrade

**Goal:** Clone to get the code only. Create a new git repo (NGNMOTOR). Do all upgrades there. **Nothing is ever pushed to or updated on the original NGM-Web repo.**

---

## 1. Clone code and create new repo (one script)

Run this from your machine (Chrome/SSH logged in is fine; run in Terminal):

```bash
cd /Users/abdulbasit/NGNWEBTONGN
chmod +x clone-fresh-and-new-repo.sh
./clone-fresh-and-new-repo.sh
```

This script:
- Clones `git@github.com:NeguinhoAdmin/NGM-Web.git` branch **newgo** into **NGNMOTOR**
- Deletes the cloned `.git` (no link to original repo)
- Runs `git init` and an initial commit in **NGNMOTOR**
- Does **not** add any remote to NGM-Web; your new repo has no push/pull to the original

---

## 2. Run the upgrade script (only in NGNMOTOR)

```bash
cd /Users/abdulbasit/NGNWEBTONGN/NGNMOTOR
bash ../upgrade-to-l12-livewire4-flux-backpack.sh
```

---

## 3. After the script: manual steps

- **Flux Pro**: If you have a Flux Pro licence, run `php artisan flux:activate` and enter your Flux email and licence key (from https://fluxui.dev/dashboard).  
  The credentials you have (neguinhomotorsltd51032583 / zkF2DXllcpEM) may be for **Backpack for Laravel** (backpackforlaravel.com), not Flux; use the correct ones for each.

- **Backpack**: The script installs Backpack 7. If the installer created an admin user, use that to log in. Otherwise create an admin user as per Backpack docs. Configure `CheckIfAdmin` and routes as needed.

- **Routes**: Prefer `Route::livewire('/path', ComponentOrViewName::class)` for full-page Livewire components (Livewire 4).

- **Config**: Review `config/livewire.php` (v4 keys: `component_layout`, `component_placeholder`, etc.) and Tailwind/CSS for Flux.

---

## 4. Stack after upgrade

| Item        | Version / note                    |
|------------|------------------------------------|
| Laravel    | 12 (non-LTS)                       |
| Livewire   | 4.x                                |
| Alpine.js  | Added via npm                      |
| Flux       | livewire/flux (+ Pro if activated)|
| Backpack   | 7.x (Laravel 12)                   |
| PHP        | 8.2+                               |
