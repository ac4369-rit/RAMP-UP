# Ramp-Up

**Topic:** Ramp-Up — Onboarding into a project already in motion (ISTE.260 Designing the User Experience, Deliverable 2).

Ramp-Up helps a person who joins a team in the middle of a project get up to speed. A newcomer accepts an invitation, sets up their profile, gets access to the team's tools and books a call with a buddy, then catches up on what they missed: recent changes, the decision log, a glossary and the resources for their role. When they know enough, they check their readiness and start work: they pick a starter task, read the handoff notes from the previous owner, report progress or problems, and submit the task for a teammate to review and accept.

## Team

| Name | GitHub | Feature area (Deliverable 1) | Main responsibility in the build |
|---|---|---|---|
| Marin Stancevic | `ms8449` | 1 — Joining the project | Database (schema, seed, ERD), login and invitations, joining screens, navigation |
| Alba | `ac4369-rit` | 2 — Understanding the project | Understanding screens, translations, Translator, README, Composer setup |
| Klesia | `km8359` | 3 — Getting started with work | Page templates, styles, PWA (manifest, service worker), assets |

## Technology

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+ (tested on 8.3), no framework, small router in `src/Router.php` |
| Database | MySQL 8 or MariaDB 10.6+ (tested on MariaDB 10.11), `utf8mb4` |
| i18n | `symfony/translation` 7.4 with ICU message format; PHP `intl` for dates, numbers, weekdays and sorting |
| Configuration | `vlucas/phpdotenv` 5.8 (`.env` file) |
| Frontend | Server-rendered HTML, one CSS file, a small amount of vanilla JavaScript, PWA manifest + service worker |

Required PHP extensions: `pdo_mysql`, `intl`, `mbstring`, `curl`.

## Setup

1. **Get the code**
   ```bash
   git clone https://github.com/ac4369-rit/RAMP-UP.git
   cd RAMP-UP
   ```
   The `vendor/` folder is committed, so `composer install` is not needed. (If you change `composer.json`, run `composer install`.)

2. **Create the database** — run the schema first, then the seed data:
   ```bash
   mysql -u root -p < database/squema.sql
   mysql -u root -p < database/seed.sql
   ```
   With XAMPP you can instead import both files in phpMyAdmin (Import tab), in the same order.

3. **Settings** — copy `.env.example` to `.env` and fill in your MySQL user and password:

   | Variable | Meaning |
   |---|---|
   | `APP_DEBUG` | `true` shows PHP errors and marks missing translations with ⚠. Use `false` for the demo. |
   | `APP_TIMEZONE` | Time zone used to *display* dates (everything is stored in UTC), e.g. `Europe/Zagreb` |
   | `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | MySQL connection |
   | `GITHUB_TOKEN` | Optional, see "External API" |

   `.env` is in `.gitignore` and must never be committed.

4. **Start**
   ```bash
   php -S localhost:8000 -t public
   ```
   Open http://localhost:8000 and sign in.

### Demo accounts

All demo accounts use the password **`rampup123`**. The full list (and the invite codes) is in [database/README.md](database/README.md).

- `alex@rampup.test`: newest member of the Atlas project, sees every screen with data.
- `new@rampup.test`: has no project yet. Open http://localhost:8000/join/ATLAS-X2M8LP to try joining.
- `leo@rampup.test`: reviews tasks. Submit one of Alex's tasks for review, then log in as Leo to accept it or request changes.

## External API

**GitHub REST API** — `GET /repos/{owner}/{repo}/commits`. The "What I missed" screen mixes the project's recent commits with the team's own change log, and guesses which role each commit matters to from its message. Code: `src/Services/GithubService.php`.

- No key is needed for public repositories (60 requests per hour per IP). For private repositories or a higher limit (5000 per hour), put a personal access token in `GITHUB_TOKEN` in `.env`. The token is only used on the server and is never sent to the browser.
- Responses are cached for 15 minutes in the `github_cache` table. Only the repository name is sent to GitHub, no user data.
- If GitHub cannot be reached (offline, timeout after 5 s, rate limit, error), the page still works: it shows the last cached commits with a notice, or a notice that code changes are not shown.

## Languages

| Code | Language | Why |
|---|---|---|
| `en` | English | Source language and fallback |
| `hr` | Hrvatski | Three plural forms (one / few / other) and diacritics |
| `ja` | 日本語 | Third locale: a different script and line breaking without spaces. **Machine-assisted; must be checked by a speaker.** |

- The language is chosen with the selector in the header (`?lang=xx`) and remembered in a cookie. The `<html lang="…">` attribute follows it.
- Texts live in `lang/en.php`, `lang/hr.php` and `lang/ja.php`, with hierarchical keys such as `buddy.cancelTitle`. Plurals use ICU messages, for example `{count, plural, one {# odluka} few {# odluke} other {# odluka}}`.
- Dates, times, weekday names and numbers are formatted with PHP `intl` (`IntlDateFormatter`, `NumberFormatter`), and names are sorted with `Collator`.
- A missing key falls back to English. With `APP_DEBUG=true` it is shown as `⚠ key` so it is easy to spot.

**Adding a language:** copy `lang/en.php` to `lang/xx.php`, translate every value (keep the keys), then add `'xx'` to `SUPPORTED` and `NAMES` in `src/Translator.php`. For a right-to-left language, also add its code to `RTL` in the same file.

## Project structure

| Folder | Contents |
|---|---|
| `public/` | Web root: `index.php` (routes), CSS, JavaScript, PWA manifest and service worker |
| `src/` | `Database.php`, `Auth.php`, `Router.php`, `Translator.php`, `helpers.php`, `Controllers/`, `Services/` |
| `templates/` | `layout.php`, `pages/` (one file per screen), `partials/` (navigation, messages, shared pieces) |
| `lang/` | Translation files |
| `database/` | `squema.sql`, `seed.sql`, `ERD.md`, setup notes |
| `vendor/` | Composer libraries (committed on purpose) |

## Known limitations

- There is no sign-up page: new members must already have an account and join with an invitation link.
- The Japanese translation is machine-assisted and still needs to be checked by a speaker.
- The accessibility check (WCAG 2.2 AA) is reported in the Deliverable 2 document, Section 7. Open issues are listed there and in GitHub Issues.
- The root folders `css/`, `pages/` and the root `manifest.json`, `script.js` and `service-worker.js` are early static prototypes. The app uses the copies in `public/`.

## Credits

- [symfony/translation](https://github.com/symfony/translation) (MIT) and [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) (BSD-3-Clause), installed with Composer.
- AI assistants were used during development. What they were used for is declared in the Deliverable 2 document, Section 12.
