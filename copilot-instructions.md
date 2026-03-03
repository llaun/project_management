<!-- .github/copilot-instructions.md -->
# Copilot / AI agent instructions — Flashy Ticket System

Purpose
- Short guidance to help AI coding agents make safe, useful edits in this small PHP project.

Big picture
- Single-page PHP app (two near-duplicate entry files: `index.php` and `index1.php`) that implements a minimal ticket system.
- State is stored in PHP sessions (`$_SESSION['tickets']`) — there is no database. Uploaded images are saved under `uploads/` and referenced by path in session data.
- Authentication is a tiny, hardcoded check that sets `$_SESSION['loggedin']` (see top of `index.php` / `index1.php`).

Key files and responsibilities
- `index.php` — main ticket UI + logic; handles login, ticket creation, file uploads, and rendering.
- `index1.php` — variant of `index.php` (contains an extra PHP stub at top). Treat these as the same application — keep edits consistent across both or consolidate into one canonical file.
- `uploads/` — directory where uploaded images are saved (created at runtime if missing).

Runtime / developer workflow (how to run & test changes)
- Runs under a PHP-enabled webserver. In this workspace use XAMPP: place the project in `htdocs` (already here) and browse to `http://localhost/tiket/` or `http://localhost/tiket/index1.php`.
- Ensure `uploads/` is writable by the webserver. The app will create it if missing.
- Debug hints: `index1.php` sets `error_reporting(E_ALL)` and `ini_set('display_errors','1')`; enable display errors or check `php_error_log` when diagnosing issues.

Data flows / important snippets
- Ticket creation: posted form fields -> `$_POST` -> build `$ticket` array -> push into `$_SESSION['tickets']`.
  - ID pattern: `'TKT-'.str_pad(count($_SESSION['tickets'])+1,5,'0',STR_PAD_LEFT)`
  - Date: `date('Y-m-d H:i:s')`
- File uploads: loop over `$_FILES['images']['tmp_name']`, use `uniqid('img_')` + original filename, and `move_uploaded_file()` into `uploads/` — images saved as relative paths and served directly.

Project-specific conventions and warnings
- Language: UI text uses Mongolian; preserve or update translations consistently when changing labels/messages.
- Minimal sanitization: user text is wrapped with `htmlspecialchars()` when creating the ticket, but there is no CSRF protection, few upload checks, and no MIME/type validation — treat security changes as explicit tasks (do not silently harden without tests and a clear plan).
- Two duplicate entrypoints: `index.php` and `index1.php`. Preferred strategy: either
  - Make a small refactor to extract shared logic into `app.php` and have both require it, or
  - Choose one file as canonical (update references) and remove the duplicate after review.

Suggested safe actions for AI agents
- Make small, single-purpose commits with clear titles. Example commit titles:
  - `fix(upload): validate uploaded image MIME types and restrict extensions`
  - `refactor: extract ticket logic to app.php and require from index.php`
  - `chore: consolidate index1.php into index.php (manual review required)`
- When editing behavior that affects persisted session data (ticket IDs, array keys), include a migration note in the PR description and keep backward-compatible access where possible.

Security & correctness checklist (explicit, per-change)
- If changing upload handling, ensure you:
  - validate MIME type and extension server-side
  - enforce a max file size and handle upload errors
  - avoid trusting the original filename (the app already prefixes with `uniqid()`; keep that)
- If modifying authentication, do NOT change hardcoded credentials silently — suggest moving to env/config and mention migration steps.

Examples (copy/paste-able) — how tickets are created and files saved
```php
$ticket = [
  'id' => 'TKT-'.str_pad(count($_SESSION['tickets'])+1,5,'0',STR_PAD_LEFT),
  'title' => htmlspecialchars($_POST['title']),
  // ...
  'images' => []
];
// handle uploads
foreach($_FILES['images']['tmp_name'] as $k => $tmp){
  if($_FILES['images']['error'][$k] === 0){
    $file_name = uniqid('img_').'_'.basename($_FILES['images']['name'][$k]);
    move_uploaded_file($tmp, 'uploads/'.$file_name);
    $ticket['images'][] = 'uploads/'.$file_name;
  }
}
```

What NOT to do without human review
- Replace session-based storage with a DB in a single PR — this is a breaking change and needs migration planning.
- Change UI language strings without confirming translations.
- Merge large security refactors without small unit/integration tests and a manual run.

Where to look next / helpful places in the code
- `index.php` and `index1.php` — primary logic and UI.
- `uploads/` — file storage; check permissions and existing files before changing upload logic.

If you edit files
- Run manual verification in browser: login (default admin/admin) -> create ticket with image -> verify it shows in ticket list and image opens.
- On Windows PowerShell you can open the app quickly:
```powershell
Start-Process 'http://localhost/tiket/index1.php'
```

If anything here is unclear or you want me to expand a section (security hardening, refactor plan, or add tests), tell me which area to focus on and I'll iterate.

---
Generated from repository inspection on 2025-12-10. Keep this file short — update it when project structure or runtime instructions change.
