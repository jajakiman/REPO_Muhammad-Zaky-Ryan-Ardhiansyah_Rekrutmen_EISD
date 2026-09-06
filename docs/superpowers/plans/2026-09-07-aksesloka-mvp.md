# AksesLoka MVP Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the complete AksesLoka MVP described in `PRD-AksesLoka.md` as a responsive Laravel 12 application.

**Architecture:** Use server-rendered Laravel Blade pages with controllers,
Form Requests, policies, and Eloquent models. Keep workflow mutations in a single
`ReportWorkflowService`; keep Supabase uploads behind Laravel's filesystem
contract so tests can use `Storage::fake()`. Leaflet receives serialized active
location data and performs optional Haversine sorting only in the browser.

**Tech Stack:** PHP 8.4, Laravel 12, Blade, Vite, native CSS, vanilla JavaScript,
Leaflet 1.9, PostgreSQL Supabase, Supabase Storage S3 compatibility, Docker,
Nginx, PHPUnit.

**Spec:** `PRD-AksesLoka.md`

## Global Constraints

- Public registration always creates role `reporter`.
- Roles are `reporter`, `officer`, and `admin`; authorization is enforced server-side.
- One officer has exactly one active campus area assignment.
- Reports follow only `submitted -> verified -> in_progress -> resolved`, `submitted -> rejected`, or `submitted -> cancelled`.
- Claiming uses a database transaction and row lock; resolving updates the report and facility condition atomically.
- Report and resolution photos accept JPEG, PNG, or WebP up to 2 MB and store only object paths.
- GPS coordinates remain in the browser and are never sent to or stored by the server.
- Public maps retain a textual location list and OpenStreetMap attribution.
- Master records in use are deactivated instead of permanently deleted.
- UI follows `DESIGN.md`, WCAG AA contrast, keyboard operation, visible focus, 44px targets, and non-color status labels.
- No automatic CRUD/admin package, chart library, indoor map, routing, tracking, chat, notification, export, AI, or mobile-native feature.

---

### Task 1: Laravel Foundation And Design System

**Files:**
- Create: Laravel 12 application scaffold at repository root
- Create: `resources/css/app.css`
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/components/flash.blade.php`
- Modify: `.env.example`, `README.md`
- Test: `tests/Feature/FoundationTest.php`

**Interfaces:**
- Produces named route `home`, shared Blade layout, flash component, and CSS tokens from `DESIGN.md`.

- [ ] Write `FoundationTest` asserting `/` responds 200, includes the AksesLoka name, and exposes a skip link.
- [ ] Run `php artisan test --filter=FoundationTest`; confirm it fails because the route and view do not exist.
- [ ] Add the route, controller/view, shared layout, responsive navigation, flash output, and design tokens.
- [ ] Run `php artisan test --filter=FoundationTest` and `npm run build`; confirm both pass.

### Task 2: Relational Data Model And Seed Data

**Files:**
- Create: migrations for `users`, `campuses`, `campus_areas`, `campus_locations`, `accessibility_features`, `location_accessibility_features`, `issue_categories`, `accessibility_reports`
- Create: corresponding models and factories in `app/Models` and `database/factories`
- Create: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/DataModelTest.php`

**Interfaces:**
- Produces the eight model classes and their named Eloquent relations from PRD section 11.

- [ ] Write tests for hierarchy relations, unique location-feature pairing, report ownership, officer assignment, and seeded master values.
- [ ] Run `php artisan test --filter=DataModelTest`; confirm missing tables or classes cause failure.
- [ ] Add PostgreSQL-compatible migrations with foreign keys, indexes, checks where portable, model casts/relations, factories, and idempotent seeders.
- [ ] Run `php artisan migrate:fresh --seed` and `php artisan test --filter=DataModelTest`; confirm both pass.

### Task 3: Authentication, Roles, And Reporter Profile

**Files:**
- Create: auth and profile controllers, Form Requests, `EnsureUserRole` middleware, auth/profile views
- Modify: `bootstrap/app.php`, `routes/web.php`, `app/Models/User.php`
- Test: `tests/Feature/AuthenticationTest.php`, `tests/Feature/RoleAuthorizationTest.php`, `tests/Feature/ReporterProfileTest.php`

**Interfaces:**
- Produces named routes `register`, `login`, `logout`, `reporter.profile.edit`, and `reporter.profile.update`; middleware alias `role`.

- [ ] Write tests for registration role assignment, affiliation-campus conditional validation, inactive login rejection, session regeneration/logout, role redirects, 403 isolation, and profile updates.
- [ ] Run the three test files; confirm failures are caused by absent routes and behavior.
- [ ] Implement session authentication with Laravel's `Auth` facade, Form Requests, role middleware, and profile forms.
- [ ] Run the three test files; confirm all pass.

### Task 4: Admin Master Data And Officer Accounts

**Files:**
- Create: admin controllers and Form Requests for campuses, areas, locations, features, issue categories, location features, and officers
- Create: `resources/views/admin/**`
- Modify: `routes/web.php`
- Test: `tests/Feature/AdminMasterDataTest.php`, `tests/Feature/AdminOfficerTest.php`

**Interfaces:**
- Produces CRUD named routes under `admin.*`; destructive actions set `is_active=false` when records have history.

- [ ] Write tests for admin-only access, coordinate ranges, scoped uniqueness, inactive-option rejection, location-feature uniqueness, officer role assignment, area requirement, reassignment, and deactivation.
- [ ] Run both tests; confirm absent endpoints fail.
- [ ] Implement resource controllers, validated forms, compact responsive tables, coordinate inputs, and activation controls.
- [ ] Run both tests; confirm all pass.

### Task 5: Public Map, Search, Filters, And Location Detail

**Files:**
- Create: `PublicMapController`, public map/detail views, `resources/js/map.js`
- Modify: `routes/web.php`, `resources/js/app.js`, `package.json`
- Test: `tests/Feature/PublicMapTest.php`

**Interfaces:**
- Produces named routes `map.index` and `locations.show`; JavaScript function `sortLocationsByDistance(locations, latitude, longitude)` remains browser-only.

- [ ] Write feature tests for guest access, active-only data, search/filter combinations, empty state, detail fields, and inactive 404 behavior.
- [ ] Run `PublicMapTest`; confirm missing routes fail.
- [ ] Implement server-side filtering, Leaflet marker rendering, OSM attribution, keyboard-labelled marker links, textual results, mini-map, and GPS-triggered Haversine sorting without network submission.
- [ ] Run `PublicMapTest` and `npm run build`; confirm both pass.

### Task 6: Reporter Submission, Storage, History, And Cancellation

**Files:**
- Create: reporter report controller, Form Requests, report views
- Modify: `config/filesystems.php`, `routes/web.php`
- Test: `tests/Feature/ReporterReportTest.php`

**Interfaces:**
- Produces named routes `reporter.reports.create/store/index/show/cancel`; writes photos through disk `report-photos`; report codes match `RPT-YYYYMMDD-XXXX`.

- [ ] Write tests for ownership, active dependencies, required description/category, code uniqueness, optional file validation/storage, storage failure rollback, history isolation, details, and cancellation constraints.
- [ ] Run `ReporterReportTest`; confirm absent report flow fails.
- [ ] Implement the minimal controller/Form Request flow and S3-compatible Supabase disk configuration; remove uploaded objects if database creation fails.
- [ ] Run `ReporterReportTest`; confirm all pass.

### Task 7: Officer Queue, Claim, Rejection, And Concurrency

**Files:**
- Create: `ReportWorkflowService`, officer report controller and views
- Create: policies for report area access
- Modify: `routes/web.php`, `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/OfficerVerificationTest.php`

**Interfaces:**
- Produces service methods `verify(AccessibilityReport $report, User $officer, string $priority): void` and `reject(AccessibilityReport $report, User $officer, string $reason): void`.

- [ ] Write tests for area-scoped queue/history, direct URL 403, required priority/reason, submitted-only actions, claim ownership, and rejection terminal state.
- [ ] Run `OfficerVerificationTest`; confirm missing workflow fails.
- [ ] Implement policy checks and service transactions using `lockForUpdate()` with state and ownership re-checks inside the lock.
- [ ] Run `OfficerVerificationTest`; confirm all pass.

### Task 8: Officer Handling And Atomic Resolution

**Files:**
- Modify: `ReportWorkflowService`, officer report controller and views
- Test: `tests/Feature/OfficerHandlingTest.php`

**Interfaces:**
- Adds service methods `start(AccessibilityReport $report, User $officer): void` and `resolve(AccessibilityReport $report, User $officer, array $data): void`.

- [ ] Write tests for responsible-officer enforcement, verified-only start, started timestamp, in-progress-only resolve, required notes/condition, optional resolution photo, terminal states, and atomic facility condition update.
- [ ] Run `OfficerHandlingTest`; confirm missing methods/actions fail.
- [ ] Implement start and resolution transactions, including storage cleanup on database failure.
- [ ] Run `OfficerHandlingTest`; confirm all pass.

### Task 9: Role Dashboards, Monitoring, And Landing Page

**Files:**
- Create: landing and role dashboard controllers/views; admin report monitoring controller/view
- Modify: `routes/web.php`
- Test: `tests/Feature/DashboardTest.php`, `tests/Feature/LandingPageTest.php`

**Interfaces:**
- Produces named dashboard routes for all roles and read-only admin report monitoring with documented filters.

- [ ] Write tests proving every displayed metric comes from database records, role/area/ownership scoping, five-item reporter recency cap, admin filters, admin read-only behavior, and public CTA destinations.
- [ ] Run both tests; confirm missing views/routes fail.
- [ ] Implement decision-oriented dashboards and landing sections from PBI-LND-01 without fake statistics, dead links, testimonials, or charts.
- [ ] Run both tests and `npm run build`; confirm all pass.

### Task 10: Production Container And Environment Documentation

**Files:**
- Create: `Dockerfile`, `.dockerignore`, `docker/nginx.conf`, `docker/entrypoint.sh`
- Modify: `.env.example`, `README.md`, `routes/web.php`
- Test: `tests/Feature/HealthCheckTest.php`

**Interfaces:**
- Produces GET `/up` returning HTTP 200; container listens on Render's `PORT`; logs go to stderr.

- [ ] Write the health-check test and run it to confirm the absent endpoint fails.
- [ ] Add the endpoint, production image, Nginx/PHP-FPM startup, non-file session/cache settings, and complete environment variable documentation without secrets.
- [ ] Run `HealthCheckTest`, `docker build -t aksesloka:test .`, and inspect `.dockerignore`; confirm success.

### Task 11: End-To-End Verification And Delivery Audit

**Files:**
- Create: `tests/Feature/ReportLifecycleTest.php`
- Modify: only files required by failures found during this task

**Interfaces:**
- Consumes every prior route and workflow; produces a runnable regression check for the main reporter-to-officer lifecycle.

- [ ] Write one end-to-end test that registers a reporter, submits a report, verifies/claims it as the area officer, starts handling, resolves it, and observes the updated facility condition as reporter and admin.
- [ ] Run the test and confirm it fails if any cross-module connection is missing.
- [ ] Make only the minimal integration corrections needed for the end-to-end test.
- [ ] Run `php artisan test`, `npm run build`, and the Anti-Slop contrast checker for every core text/button pairing.
- [ ] Verify routes with `php artisan route:list`, scan tracked files for secrets, and record manual desktop/mobile/keyboard click-through evidence in `README.md`.
