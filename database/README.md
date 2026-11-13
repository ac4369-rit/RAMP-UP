# Database setup

## 1. Create the database

From the project folder, run both files **in this order** (the first one deletes and recreates the `rampup` database):

```bash
mysql -u root -p < database/squema.sql
mysql -u root -p < database/seed.sql
```

Using phpMyAdmin (XAMPP) instead: open the **Import** tab, import `squema.sql` first, then `seed.sql`.

## 2. Connect the app

Copy `.env.example` to `.env` and put your own MySQL user and password in it:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=rampup
DB_USER=root
DB_PASS=
```

`.env` is in `.gitignore`, so your password is never committed.

## 3. Run the app

```bash
php -S localhost:8000 -t public
```

Open http://localhost:8000. You get sent to the login page.

## Demo accounts

Every account uses the password **`rampup123`**.

| Email | Who | Good for testing |
|---|---|---|
| `alex@rampup.test` | Alex, designer, newest member of Atlas | All screens of every part (has skills, tool access, buddy, questions) |
| `new@rampup.test` | Nora, has an account but no project yet | The full invite flow: open http://localhost:8000/join/ATLAS-X2M8LP |
| `sam@rampup.test` | Sam, frontend developer on Atlas | A team member who has been there for a while |
| `leo@rampup.test` | Leo, senior developer, Alex's buddy | Reviewing tasks: accept Alex's task or request changes (Part 3) |
| `ana@rampup.test` | Ana, project lead, Croatian UI | Second project (Library Booking System) |

Part 3 (Getting started with work), logged in as Alex:

- *My tasks* shows a starter task whose deadline is at risk (it is always due 2 days after the seed was loaded) and a task with review feedback.
- *Starter tasks* has free tasks Alex can take.
- Submit a task for review, then log in as `leo@rampup.test` to accept it or request changes. Log in as Alex again to see the result.

Invite codes in the seed:

| Code | State |
|---|---|
| `ATLAS-X2M8LP` | open, for `new@rampup.test` |
| `LIBRARY-4H6TZ` | open, for `alex@rampup.test` (Alex joins a second project) |
| `ATLAS-7F3K9Q` | already accepted (shows the "already accepted" message) |

## Notes

- All dates and times are stored in **UTC**. The app converts them to `APP_TIMEZONE` only when showing them.
- Both SQL files start with `SET NAMES utf8mb4;` so names like *Ana Kovač* and *Đuro Šimić* are stored correctly. Without it, the MySQL command line on Windows can save them as garbled text.
- The tables and their relations are drawn in [ERD.md](ERD.md).
