# 📄 Resume Builder — Laravel Backend

A full-stack **resume building platform** built with **Laravel 12**, **Livewire 4**, and **Laravel Sanctum**. It exposes a RESTful JSON API consumed by a separate React frontend, while also serving a fully server-rendered **admin panel** powered by Livewire. Users can create, edit, and manage multiple resumes. Admins can monitor platform health, manage users, view analytics, and export data — all from a dedicated dashboard.

---

## 🧭 Table of Contents

- [Overview](#-overview)
- [Architecture](#-architecture)
- [Technology Stack](#-technology-stack)
- [Core Features](#-core-features)
    - [User-Facing API](#user-facing-api)
    - [Resume Management](#resume-management)
    - [Admin Panel](#admin-panel)
    - [Audit Logging](#audit-logging)
    - [Security & Middleware](#security--middleware)
- [Data Models](#-data-models)
- [API Reference](#-api-reference)
- [Admin Routes](#-admin-routes)
- [Project Structure](#-project-structure)
- [Database](#-database)
- [Environment Configuration](#-environment-configuration)
- [Local Development Setup](#-local-development-setup)
- [Docker Deployment](#-docker-deployment)
- [Authentication Flow](#-authentication-flow)
- [Admin Bridge Login](#-admin-bridge-login)

---

## 🔍 Overview

Resume Builder is a multi-role SaaS application where:

- **Regular users** register, log in, and build structured resumes via the React frontend (a separate project). Each resume can contain personal details, education, work experience, projects, certifications, and skills. Resumes can be saved as **drafts** or **published**, and can be themed with an **accent color** and a chosen **template/type**.
- **Admins** access a server-rendered Livewire dashboard at `/admin/*` where they can view platform-wide statistics, manage users (block/unblock, promote to admin, delete), manage all resumes, view and export audit logs, and study deep analytics (DAU, WAU, MAU, retention cohorts).

The backend is the single source of truth — it stores all data, enforces all business rules, and produces JSON for the API consumers as well as HTML for the admin panel.

---

## 🏗 Architecture

```
┌──────────────────────────────────┐
│        React SPA Frontend        │  ← Separate project (not in this repo)
│  (Auth, Resume Editor, Dashboard)│
└────────────────┬─────────────────┘
                 │  JSON API (Laravel Sanctum tokens)
                 ▼
┌──────────────────────────────────┐
│        Laravel 12 Backend        │
│                                  │
│  ┌──────────────┐  ┌──────────┐  │
│  │  REST API    │  │  Admin   │  │
│  │  /api/*      │  │  Panel   │  │
│  │  Sanctum Auth│  │ /admin/* │  │
│  └──────┬───────┘  └────┬─────┘  │
│         │               │        │
│  ┌──────▼───────────────▼──────┐ │
│  │   Controllers / Livewire    │ │
│  │   Models / Services         │ │
│  └──────────────┬──────────────┘ │
│                 │                │
│  ┌──────────────▼──────────────┐ │
│  │   PostgreSQL / SQLite DB    │ │
│  └─────────────────────────────┘ │
└──────────────────────────────────┘
```

The application follows a strict **MVC pattern**:

- **Controllers** (`app/Http/Controllers/`) handle the JSON API surface.
- **Livewire Components** (`app/Livewire/Admin/`) power the reactive admin panel without writing JavaScript.
- **Models** (`app/Models/`) define Eloquent relationships and UUID primary keys.
- **Services** (`app/Services/`) contain reusable business logic (e.g., `AuditLogger`).
- **Blade Views** (`resources/views/`) render the admin panel HTML.

---

## 🛠 Technology Stack

| Layer                | Technology                                         |
| -------------------- | -------------------------------------------------- |
| Framework            | Laravel 12 (PHP 8.2+)                              |
| Reactive UI          | Livewire 4                                         |
| Authentication       | Laravel Sanctum (token-based, dual access/refresh) |
| PDF Generation       | barryvdh/laravel-dompdf                            |
| Icons                | Blade Lucide Icons + Blade Icons                   |
| User Agent Detection | jenssegers/agent                                   |
| Frontend Build       | Vite + Node 20                                     |
| Database (dev)       | SQLite                                             |
| Database (prod)      | PostgreSQL 16                                      |
| Containerisation     | Docker (multi-stage), Docker Compose               |
| Testing              | PHPUnit 11                                         |
| Code Style           | Laravel Pint                                       |

---

## ✨ Core Features

### User-Facing API

All endpoints under `/api/*` are consumed by the React SPA.

#### Authentication

- **Register** — Creates a new account with full name, email, and password. Roles supported: `USER` and `ADMIN`. Email verification flow is scaffolded (commented out for simpler onboarding).
- **Login** — Validates credentials, checks account verification and block status, then issues a **Sanctum access token** and a **refresh token** with separate abilities (`access` / `refresh`). Records a `UserSession` entry with IP address and user agent.
- **Refresh** — Exchanges a valid refresh token for a fresh access token, revoking the old one.
- **Logout** — Revokes all tokens, clears the user session record, and marks the user as logged out.
- **Me** — Returns the authenticated user's id, email, and full name.
- **Update Profile** — Updates name, email, and optionally password. All changes are recorded in the audit log.
- **Forgot/Reset Password** — Uses Laravel's built-in password broker to send a signed reset link and process resets. (Scaffolded, can be enabled in routes.)

#### Resume Management

- **List Resumes** — Paginated, filterable by `published` or `drafts`. Each item includes a computed **completion score** (0–100%) derived from how many resume sections have been filled in.
- **Get Single Resume** — Returns the full resume with all nested relations (personal details, education, experience, projects, certifications, skills) as a JSON Resource.
- **Create Resume** — Creates a new resume shell with a title, type (template), accent color, and draft flag.
- **Update Resume** — Full transactional update of all sections. Uses `replaceRelation()` for one-to-many sections (education, experience, projects, certifications), and `updateOrCreate()` for singleton sections (personal details, skills).
- **Delete Resume** — Soft-deletes the record and logs the action.
- **Stats** — Dashboard stats for the authenticated user: total resumes, completed resumes, average completion, and last-edited resume summary.

---

### Resume Data Model

A resume is composed of the following sections, each stored in its own table:

| Section                 | Model                                  | Relationship |
| ----------------------- | -------------------------------------- | ------------ |
| Resume metadata         | `Resume`                               | —            |
| Personal details        | `PersonalDetail`                       | `hasOne`     |
| Education               | `Education`                            | `hasMany`    |
| Professional Experience | `Experience` (category=`professional`) | `hasMany`    |
| Other Experience        | `Experience` (category=`other`)        | `hasMany`    |
| Projects                | `Project`                              | `hasMany`    |
| Certifications          | `Certification`                        | `hasMany`    |
| Skills                  | `Skill`                                | `hasOne`     |

All primary keys use **UUIDs** (`HasUuids` trait).

#### Completion Score Logic

The `calculateCompletion()` method in `ResumeController` scores a resume out of 100:

| Section                                      | Points |
| -------------------------------------------- | ------ |
| Personal details (name, email, phone, about) | 20     |
| Education entries                            | 15     |
| Skills                                       | 15     |
| Professional Experience                      | 25     |
| Projects                                     | 15     |
| Certifications                               | 10     |

---

### Admin Panel

Accessible at `/admin/*`. Requires session-based authentication (`auth` guard) and the `admin` middleware (role must be `ADMIN`). Entirely server-rendered with **Livewire 4**, meaning the UI is reactive (search, filters, pagination, modals) with zero custom JavaScript.

#### Admin Dashboard (`/admin/dashboard`)

**Livewire Component:** `AdminDashboard`

Displays platform-wide KPIs:

- Total users and total resumes
- Number of blocked users
- Number of currently logged-in users
- 5 most recently joined users
- **Chart: User sign-ups per day** (last 7 days)
- **Chart: Resumes created per day** (last 7 days, timezone-aware PostgreSQL query)
- **Chart: Template usage distribution** (doughnut/pie — which resume type is most popular)

#### Admin Analytics (`/admin/analytics`)

**Livewire Component:** `AdminAnalytics`

Deep engagement metrics:

- **DAU (Daily Active Users):** today, yesterday, and 7-day average. Chart over last 7 days using `UserSession.last_seen_at`.
- **WAU (Weekly Active Users):** distinct users active in the last 7 days.
- **MAU (Monthly Active Users):** distinct users active in the last 30 days.
- **Resumes created** chart (last 7 days).
- **Template usage distribution** chart.
- **7-day Retention Summary:** percentage of users who signed up exactly 7 days ago and were active today.
- **Weekly Cohort Retention Table:** for each of the last 4 weekly cohorts, shows how many users joined vs. how many came back the following week, with a percentage.
- **Retention Trend Chart:** visualizes the retention percentage across the last 4 cohorts.

All time-series queries are **timezone-aware** — stored UTC timestamps are converted to the app timezone (`Asia/Kolkata` by default) before grouping by day.

#### User Management (`/admin/users`)

**Livewire Component:** `UserManagement`

- Paginated, searchable user list (search by name, email, or ID).
- **Filters:** role (`USER`/`ADMIN`), status (`active`/`blocked`), join date range.
- **Sorting:** any column, ascending or descending.
- **Create User** — Modal form to create new users (admin can set role).
- **Toggle Admin** — Promotes or demotes a user between `USER` and `ADMIN` roles. Cannot change own role.
- **Toggle Block** — Blocks or unblocks a user. Blocking immediately revokes all Sanctum tokens, clears all user sessions, and forces logout. Cannot block self or other admins.
- **Delete User** — Two-step confirmation modal before permanent deletion. Cannot delete self or admins.
- All actions are fully audit-logged.

#### Resume Management (`/admin/resumes`)

**Livewire Component:** `ResumeManagement`

- Paginated, searchable list of all resumes across all users.
- Search by resume title, or by owner name/email.
- **Filters:** resume type/template, creation date range, sort order.
- **View Resume** — Navigates to a detail page showing the full resume data.
- **Preview Resume** — Renders the resume in a styled preview.
- **Delete Resume** — Permanently deletes the resume with audit logging.

#### Audit Logs (`/admin/audit-logs`)

**Livewire Component:** `AuditLogs`

- Paginated, searchable log of every significant action in the system.
- Search by action name, target type, target ID, or actor name/email.
- **Filters:** action type dropdown (all distinct actions), date range, sort order.
- **View Log Entry** — Modal showing full log details: actor, action, target, before/after snapshots, metadata, IP, and user agent.
- **Export to CSV** — Streams all audit log records as a downloadable CSV.
- Filter state is persisted in the URL query string for shareable links.

#### Templates (`/admin/templates`)

Displays the list of available resume templates with usage counts.

---

### Audit Logging

The `AuditLogger` service (`app/Services/AuditLogger.php`) provides a static `log()` method that creates an `AuditLog` record for every significant event. Each record captures:

| Field         | Description                                                        |
| ------------- | ------------------------------------------------------------------ |
| `actor_id`    | The user who performed the action (from auth or passed explicitly) |
| `action`      | A snake_case string constant (e.g., `USER_LOGIN_SUCCESS`)          |
| `target_type` | The Eloquent model class of the affected entity                    |
| `target_id`   | The primary key of the affected entity                             |
| `before`      | JSON snapshot of the entity state before the change                |
| `after`       | JSON snapshot of the entity state after the change                 |
| `meta`        | Arbitrary JSON metadata (page, reason, device info, etc.)          |
| `ip`          | Requester's IP address                                             |
| `user_agent`  | Requester's browser/client string                                  |

**Tracked actions include:**

| Action                                                  | Trigger                                |
| ------------------------------------------------------- | -------------------------------------- |
| `USER_REGISTERED`                                       | New user sign-up or admin-created user |
| `USER_LOGIN_SUCCESS`                                    | Successful login                       |
| `USER_LOGIN_FAILED`                                     | Wrong password or blocked account      |
| `USER_LOGOUT`                                           | Explicit logout                        |
| `USER_PROFILE_UPDATED`                                  | Name or email changed                  |
| `USER_PASSWORD_CHANGED`                                 | Password updated                       |
| `RESUME_CREATED`                                        | New resume created                     |
| `RESUME_UPDATED`                                        | Resume content updated                 |
| `RESUME_DELETED`                                        | Resume deleted                         |
| `ADMIN_LOGIN_SUCCESS`                                   | Admin bridged into the admin panel     |
| `ADMIN_BRIDGE_TOKEN_MISSING/INVALID/FORBIDDEN`          | Failed admin bridge attempts           |
| `ADMIN_VIEWED_USER`                                     | Admin viewed a user's detail page      |
| `ADMIN_USER_BLOCKED` / `ADMIN_USER_UNBLOCKED`           | User block status toggled              |
| `ADMIN_USER_GRANTED_ADMIN` / `ADMIN_USER_REVOKED_ADMIN` | Role change                            |
| `ADMIN_USER_DELETED`                                    | User deleted by admin                  |
| `ADMIN_RESUME_VIEWED`                                   | Admin viewed a resume                  |
| `ADMIN_RESUME_DELETED`                                  | Admin deleted a resume                 |
| `ADMIN_ACTION_DENIED`                                   | Unauthorized admin action attempt      |

---

### Security & Middleware

Three custom middleware protect API and admin routes:

| Middleware             | Alias            | Purpose                                                                                                  |
| ---------------------- | ---------------- | -------------------------------------------------------------------------------------------------------- |
| `EnsureUserNotBlocked` | `not_blocked`    | Rejects blocked users — returns 403 JSON for API requests, redirects to login for web requests           |
| `UpdateLastSeen`       | `updateLastSeen` | Updates `UserSession.last_seen_at` on every authenticated API request to power the DAU/WAU/MAU analytics |
| `AdminOnly`            | `admin`          | Ensures the session-authenticated user has `role === 'ADMIN'`; 403 otherwise                             |

All protected API routes run under `auth:sanctum` + `updateLastSeen` + `not_blocked`.

All admin panel routes run under `auth` (session) + `admin`.

---

## 🗄 Data Models

```
users
├── id (UUID)
├── fullName
├── email
├── password
├── role (USER | ADMIN)
├── isVerified
├── isLoggedIn
└── is_blocked

resumes
├── id (UUID)
├── userId → users.id
├── resumeTitle
├── resumeType  (template identifier)
├── accentColor
└── isDraft

personal_details
├── resumeId → resumes.id
├── fullName, email, phone, about
└── address, linkedin, github, website, etc.

education
├── resumeId → resumes.id
├── name, degree, location
└── startDate, endDate, grades

experiences
├── resumeId → resumes.id
├── category (professional | other)
├── company, role, location
└── startDate, endDate, description

projects
├── resumeId → resumes.id
└── name, description, link, stack, etc.

certifications
├── resumeId → resumes.id
└── name, issuer, date, link

skills
├── resumeId → resumes.id
└── skills (JSON/text blob of skills data)

user_sessions
├── user_id → users.id
├── access_token_id
├── refresh_token_id
├── ip_address, user_agent
└── last_seen_at

audit_logs
├── actor_id → users.id
├── action
├── target_type, target_id
├── before (JSON), after (JSON), meta (JSON)
└── ip, user_agent
```

---

## 📡 API Reference

**Base URL:** `http://localhost:8000/api`

All protected routes require the header:

```
Authorization: Bearer <access_token>
```

### Auth Endpoints

| Method | Endpoint                   | Auth                | Description                  |
| ------ | -------------------------- | ------------------- | ---------------------------- |
| `POST` | `/api/auth/register`       | No                  | Register a new user          |
| `POST` | `/api/auth/login`          | No                  | Login and receive tokens     |
| `POST` | `/api/auth/logout`         | Yes                 | Logout and revoke tokens     |
| `GET`  | `/api/auth/me`             | Yes                 | Get current user info        |
| `POST` | `/api/auth/refresh`        | Yes (refresh token) | Get new access token         |
| `PUT`  | `/api/auth/update-profile` | Yes                 | Update name, email, password |

### Resume Endpoints

| Method   | Endpoint                                 | Auth | Description                      |
| -------- | ---------------------------------------- | ---- | -------------------------------- |
| `GET`    | `/api/resume/stats`                      | Yes  | Dashboard stats for current user |
| `GET`    | `/api/resume/all?type=published\|drafts` | Yes  | Paginated list of user's resumes |
| `POST`   | `/api/resume/create`                     | Yes  | Create a new resume              |
| `GET`    | `/api/resume/{id}`                       | Yes  | Get full resume by ID            |
| `PUT`    | `/api/resume/{id}`                       | Yes  | Update resume (all sections)     |
| `DELETE` | `/api/resume/{id}`                       | Yes  | Delete a resume                  |

### Register Request Body

```json
{
    "fullName": "Jane Doe",
    "email": "jane@example.com",
    "password": "secret123",
    "confirmPassword": "secret123",
    "role": "USER"
}
```

### Login Response

```json
{
    "success": true,
    "message": "Welcome back Jane Doe",
    "data": { "id": "...", "email": "...", "fullName": "..." },
    "access_token": "1|...",
    "refresh_token": "2|...",
    "is_admin": false
}
```

### Resume Update Request Body (partial example)

```json
{
  "resumeTitle": "My Software Engineer Resume",
  "resumeType": "Modern",
  "accentColor": "#6366f1",
  "isDraft": false,
  "personalDetails": {
    "fullName": "Jane Doe",
    "email": "jane@example.com",
    "phone": "+1-555-0100",
    "about": "Passionate software engineer..."
  },
  "educationDetails": [
    {
      "name": "MIT",
      "degree": "B.Sc. Computer Science",
      "location": "Cambridge, MA",
      "dates": { "startDate": "2018-09", "endDate": "2022-05" },
      "grades": "3.9 GPA"
    }
  ],
  "professionalExperience": [...],
  "projects": [...],
  "certifications": [...],
  "skills": { "technical": ["PHP", "Laravel", "React"], "soft": ["Leadership"] }
}
```

---

## 🔐 Admin Routes

The admin panel is a **server-rendered web application** (not a JSON API). Access it in a browser.

| Route                                 | Livewire Component | Description                      |
| ------------------------------------- | ------------------ | -------------------------------- |
| `GET /admin/dashboard`                | `AdminDashboard`   | Platform KPIs and charts         |
| `GET /admin/analytics`                | `AdminAnalytics`   | DAU/WAU/MAU, retention cohorts   |
| `GET /admin/users`                    | `UserManagement`   | User list, create/block/delete   |
| `GET /admin/users/{user}`             | —                  | User detail page                 |
| `GET /admin/resumes`                  | `ResumeManagement` | All resumes list                 |
| `GET /admin/resumes/{resume}/show`    | —                  | Resume data view                 |
| `GET /admin/resumes/{resume}/preview` | —                  | Resume rendered preview          |
| `GET /admin/templates`                | `Templates`        | Template usage stats             |
| `GET /admin/audit-logs`               | `AuditLogs`        | Searchable, filterable log       |
| `GET /admin/reports/export`           | —                  | Export user/resume report as CSV |
| `GET /admin/audit-logs/export`        | —                  | Export audit logs as CSV         |
| `POST /admin/logout`                  | —                  | Logout from admin panel          |

---

## 📁 Project Structure

```
resume-builder-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── BridgeLoginController.php   # Admin SSO bridge
│   │   │   ├── Admin/                          # Admin-specific controllers
│   │   │   ├── ResumeController.php            # Resume CRUD API
│   │   │   └── UserController.php              # Auth & profile API
│   │   ├── Middleware/
│   │   │   ├── AdminOnly.php
│   │   │   ├── EnsureUserNotBlocked.php
│   │   │   └── UpdateLastSeen.php
│   │   ├── Requests/                           # Form request validators
│   │   └── Resources/
│   │       └── ResumeResource.php              # JSON API transformer
│   ├── Livewire/
│   │   └── Admin/
│   │       ├── AdminDashboard.php
│   │       ├── AdminAnalytics.php
│   │       ├── AuditLogs.php
│   │       ├── ResumeManagement.php
│   │       ├── Templates.php
│   │       ├── UserDetails.php
│   │       └── UserManagement.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Certification.php
│   │   ├── Education.php
│   │   ├── Experience.php
│   │   ├── PersonalDetail.php
│   │   ├── Project.php
│   │   ├── Resume.php
│   │   ├── Skill.php
│   │   ├── User.php
│   │   └── UserSession.php
│   ├── Providers/
│   └── Services/
│       └── AuditLogger.php                     # Centralized audit service
├── database/
│   ├── migrations/                             # 21 migration files
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── admin/                              # Admin Blade templates
│   │   │   ├── dashboard/
│   │   │   ├── analytics/
│   │   │   ├── users/
│   │   │   ├── resumes/
│   │   │   ├── templates/
│   │   │   └── audit-logs/
│   │   ├── livewire/admin/                     # Livewire component views
│   │   ├── components/                         # Reusable Blade components
│   │   └── welcome.blade.php                   # Landing / SPA shell
│   ├── css/
│   └── js/
├── routes/
│   ├── api.php                                 # JSON API routes
│   ├── admin.php                               # Admin panel routes
│   ├── web.php                                 # Public web routes
│   └── console.php
├── Dockerfile                                  # Multi-stage build
├── docker-compose.yml                          # App + PostgreSQL
├── start.sh                                    # Container entrypoint
├── vite.config.js
├── composer.json
└── package.json
```

---

## 🗃 Database

The project ships with **21 migrations**, applied in order:

1. Default Laravel users, cache, jobs tables
2. `resumes` — core resume record with title, type, accent color, draft flag
3. `personal_details` — one-to-one personal info per resume
4. `education` — many education entries per resume
5. `skills` — one-to-one skills JSON blob per resume
6. `experiences` — many work experiences (professional + other category)
7. `projects` — many projects per resume
8. `certifications` — many certifications per resume
9. `personal_access_tokens` — Sanctum tokens (fixed UUID tokenable_id)
10. `sessions` — Laravel session driver (fixed UUID user_id)
11. Users table cleanup (UUID keys, remove legacy columns)
12. `role` column on users (`USER` | `ADMIN`)
13. `accentColor` column on resumes
14. `isDraft` column on resumes
15. `resumeType` field type change
16. `is_blocked` column on users
17. `user_sessions` — tracks IP, user agent, last seen, token IDs
18. `audit_logs` — full audit trail with before/after JSON

**Supported databases:**

- **Development:** SQLite (default, zero-config)
- **Production:** PostgreSQL 16 (recommended; analytics queries use PostgreSQL-specific `to_char` and `AT TIME ZONE` syntax)

---

## ⚙️ Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
APP_NAME=ResumeBuilder
APP_ENV=local
APP_KEY=                        # Generated by artisan key:generate
APP_URL=http://localhost:8000

FRONTEND_URL=http://localhost:5173   # React SPA origin (for CORS and redirects)

# Database (SQLite for local dev)
DB_CONNECTION=sqlite

# Database (PostgreSQL for production)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=resume_builder
# DB_USERNAME=postgres
# DB_PASSWORD=secret

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Mail (for password reset; use 'log' driver during dev)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@resumebuilder.com"
```

---

## 🚀 Local Development Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+
- SQLite (comes with PHP) or PostgreSQL

### One-Command Setup

```bash
composer run setup
```

This runs: `composer install` → copy `.env` → `artisan key:generate` → `artisan migrate` → `npm install` → `npm run build`.

### Start Dev Server

```bash
composer run dev
```

This concurrently runs:

- `php artisan serve` — Laravel dev server on `http://localhost:8000`
- `php artisan queue:listen` — Queue worker for background jobs
- `npm run dev` — Vite HMR server for frontend assets

### Run Tests

```bash
composer run test
# or
php artisan test
```

---

## 🐳 Docker Deployment

The project ships with a **multi-stage Dockerfile**:

1. **Stage 1 — Node build:** Installs npm dependencies and runs `npm run build` to compile Vite assets.
2. **Stage 2 — PHP runtime:** Uses `php:8.2-cli`, installs system deps (`pdo_pgsql`, `unzip`, etc.), installs Composer dependencies (no-dev), copies built Vite assets from Stage 1, and runs `start.sh`.

**`start.sh`** container entrypoint:

1. Clears all bootstrap caches
2. Runs `php artisan migrate --force`
3. Starts the built-in PHP server: `php -S 0.0.0.0:8000 -t public`

### Build & Run with Docker Compose

```bash
docker-compose up --build
```

This starts:

- **`app`** — Laravel app on port `8000`
- **`db`** — PostgreSQL 16 on port `5432` (database: `resume_builder`, user: `postgres`, password: `postgres`)

Make sure your `.env` is configured for PostgreSQL before building:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=resume_builder
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

---

## 🔐 Authentication Flow

### User Authentication (API)

```
Client                          Laravel API
  │                                  │
  │── POST /api/auth/login ─────────>│
  │                                  │ Validate credentials
  │                                  │ Check isVerified + is_blocked
  │                                  │ Delete old tokens
  │                                  │ Create access token (ability: access)
  │                                  │ Create refresh token (ability: refresh)
  │                                  │ Create UserSession record
  │<── { access_token, refresh_token }│
  │                                  │
  │── GET /api/resume/all ──────────>│  Authorization: Bearer <access_token>
  │   (updateLastSeen middleware)    │  Updates UserSession.last_seen_at
  │<── { resumes: [...] } ──────────│
  │                                  │
  │── POST /api/auth/refresh ───────>│  Authorization: Bearer <refresh_token>
  │                                  │ Verify token has 'refresh' ability
  │                                  │ Delete old access tokens
  │                                  │ Issue new access token
  │<── { access_token: "..." } ─────│
```

### Token Security

- A user can only have **one active session** at a time — all previous tokens are deleted on each new login.
- The `not_blocked` middleware checks block status on **every request**, so a block takes effect immediately even for users with active tokens.
- The refresh token cannot be used for API access (it only has the `refresh` ability), preventing misuse if an access token is leaked.

---

## 🌉 Admin Bridge Login

Admins authenticate via the React SPA using the same token-based login, then are redirected to `/auth/bridge?token=<access_token>`. The `BridgeLoginController` validates:

1. Token is present
2. Token exists in the database
3. The token belongs to an actual user
4. The user has `role === 'ADMIN'`

If all checks pass, the user is logged into the **session guard** (Laravel web auth) and redirected to `/admin/dashboard`. This bridge allows the admin panel (which uses session auth) to share the same user account system as the API (which uses token auth). Every bridge attempt — successful or failed — is recorded in the audit log.

---

## 📝 License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
