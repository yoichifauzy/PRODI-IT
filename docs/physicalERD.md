# Physical ERD (Database Schema)

This document describes the physical database schema derived from the current Laravel migrations. It focuses on actual tables, columns, data types, keys, indexes, and relationships. The schema below reflects all applied migration changes (added and dropped columns).

## 3.2.3 Implementasi Sistem

Pada tahap implementasi, seluruh hasil perancangan UML, ERD, dan wireframe diterjemahkan ke dalam baris kode program. Project Web Prodi IT dikembangkan dengan PHP menggunakan framework Laravel 13 berarsitektur Model-View-Controller (MVC), sehingga logika data dikelola pada Model melalui Eloquent ORM, alur pemrosesan berada di Controller, dan antarmuka dibangun pada View berbasis Blade. Pembangunan aset front-end menggunakan Vite sebagai bundler, Tailwind CSS untuk styling, serta JavaScript untuk interaksi UI. Penyimpanan data fisik memakai MySQL yang dikonfigurasi sesuai rancangan ERD agar struktur tabel, relasi, dan integritas data tetap konsisten dengan desain.

## Conventions

- Types follow typical MySQL equivalents for Laravel schema definitions.
- Timestamps are nullable unless otherwise stated.
- Foreign keys specify ON DELETE behavior when defined.

## Tables

### users

**Columns**

- id: bigint unsigned, PK, auto increment
- name: varchar(255), not null
- email: varchar(255), not null, unique
- role: varchar(20), not null, default 'admin'
- is_active: tinyint(1), not null, default 1
- email_verified_at: timestamp, null
- password: varchar(255), not null
- remember_token: varchar(100), null
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- PRIMARY KEY (id)
- UNIQUE (email)

**Referenced by**

- curricula.created_by
- announcements.created_by, announcements.updated_by
- aspirations.read_by
- academic_events.created_by
- projects.created_by
- researches.created_by
- community_services.created_by
- galleries.created_by
- documents.created_by
- tracer_alumnis.created_by
- hero_slides.created_by, hero_slides.updated_by
- lecturer_staff.created_by, lecturer_staff.updated_by
- vision_missions.created_by, vision_missions.updated_by
- activities.created_by

### password_reset_tokens

**Columns**

- email: varchar(255), PK
- token: varchar(255), not null
- created_at: timestamp, null

### sessions

**Columns**

- id: varchar(255), PK
- user_id: bigint unsigned, null (indexed, no FK)
- ip_address: varchar(45), null
- user_agent: text, null
- payload: longtext, not null
- last_activity: int, not null

**Indexes**

- PRIMARY KEY (id)
- INDEX (user_id)
- INDEX (last_activity)

### cache

**Columns**

- key: varchar(255), PK
- value: mediumtext, not null
- expiration: bigint, not null

**Indexes**

- PRIMARY KEY (key)
- INDEX (expiration)

### cache_locks

**Columns**

- key: varchar(255), PK
- owner: varchar(255), not null
- expiration: bigint, not null

**Indexes**

- PRIMARY KEY (key)
- INDEX (expiration)

### jobs

**Columns**

- id: bigint unsigned, PK, auto increment
- queue: varchar(255), not null
- payload: longtext, not null
- attempts: tinyint unsigned, not null
- reserved_at: int unsigned, null
- available_at: int unsigned, not null
- created_at: int unsigned, not null

**Indexes**

- PRIMARY KEY (id)
- INDEX (queue)

### job_batches

**Columns**

- id: varchar(255), PK
- name: varchar(255), not null
- total_jobs: int, not null
- pending_jobs: int, not null
- failed_jobs: int, not null
- failed_job_ids: longtext, not null
- options: mediumtext, null
- cancelled_at: int, null
- created_at: int, not null
- finished_at: int, null

### failed_jobs

**Columns**

- id: bigint unsigned, PK, auto increment
- uuid: varchar(255), not null, unique
- connection: text, not null
- queue: text, not null
- payload: longtext, not null
- exception: longtext, not null
- failed_at: timestamp, not null, default CURRENT_TIMESTAMP

**Indexes**

- PRIMARY KEY (id)
- UNIQUE (uuid)

### curricula

**Columns**

- id: bigint unsigned, PK, auto increment
- name: varchar(255), not null
- major_selection: varchar(20), null
- description: text, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null
- deleted_at: timestamp, null (soft delete)

**Notes**

- Columns academic_year and is_active were dropped by later migrations.

### curriculum_courses

**Columns**

- id: bigint unsigned, PK, auto increment
- curriculum_id: bigint unsigned, not null, FK -> curricula.id (ON DELETE CASCADE)
- code: varchar(20), not null
- name: varchar(255), not null
- credits_theory: tinyint unsigned, not null
- credits_practice: tinyint unsigned, not null
- sort_order: smallint unsigned, not null, default 0
- created_at: timestamp, null
- updated_at: timestamp, null

**Notes**

- Column semester and its related unique/index were dropped.

### cpl_categories

**Columns**

- id: bigint unsigned, PK, auto increment
- name: varchar(255), not null
- slug: varchar(255), not null, unique
- description: text, null
- sort_order: tinyint unsigned, not null, default 0
- created_at: timestamp, null
- updated_at: timestamp, null

### cpl_items

**Columns**

- id: bigint unsigned, PK, auto increment
- cpl_category_id: bigint unsigned, not null, FK -> cpl_categories.id (ON DELETE CASCADE)
- code: varchar(30), null
- description: text, not null
- sort_order: smallint unsigned, not null, default 0
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (cpl_category_id, sort_order)

### announcements

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- slug: varchar(255), not null, unique
- excerpt: text, null
- content: longtext, not null
- cover_image: varchar(255), null
- status: enum('draft','published','archived'), not null, default 'draft'
- published_at: timestamp, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- updated_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null
- deleted_at: timestamp, null (soft delete)

**Indexes**

- UNIQUE (slug)
- INDEX (status, published_at)

### aspirations

**Columns**

- id: bigint unsigned, PK, auto increment
- email: varchar(255), not null
- nim: varchar(30), null
- subject: varchar(255), not null
- message: text, not null
- status: enum('unread','read','archived'), not null, default 'unread'
- read_at: timestamp, null
- read_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- ip_address: varchar(45), null
- user_agent: text, null
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (nim)
- INDEX (status, created_at)

**Notes**

- Column full_name was dropped by later migration.

### academic_events

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- slug: varchar(255), not null, unique
- description: text, null
- event_type: enum('krs','uts','uas','holiday','seminar','other'), not null, default 'other'
- start_date: date, not null
- end_date: date, null
- location: varchar(255), null
- google_event_url: varchar(255), null
- is_published: tinyint(1), not null, default 1
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- UNIQUE (slug)
- INDEX (start_date, end_date)
- INDEX (event_type, is_published)

### projects

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- slug: varchar(255), not null, unique
- student_name: varchar(255), not null
- student_nim: varchar(30), null
- year: smallint unsigned, null
- summary: text, null
- thumbnail: varchar(255), null
- status: enum('draft','published'), not null, default 'draft'
- is_featured: tinyint(1), not null, default 0
- published_at: timestamp, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null
- deleted_at: timestamp, null (soft delete)

**Indexes**

- UNIQUE (slug)
- INDEX (status, published_at)
- INDEX (year)

**Notes**

- Columns demo_url and repository_url were dropped by later migration.

### researches

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- researcher_name: varchar(255), not null
- researcher_role: enum('dosen','mahasiswa','kolaborasi'), not null, default 'dosen'
- year: smallint unsigned, not null
- publication: varchar(255), null
- link: varchar(255), null
- abstract: text, null
- status: enum('draft','published'), not null, default 'draft'
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (year, status)

### community_services

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- activity_date: date, not null
- location: varchar(255), null
- organizer: varchar(255), null
- summary: text, null
- documentation_cover: varchar(255), null
- status: enum('draft','published'), not null, default 'draft'
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (activity_date)
- INDEX (status)

### galleries

**Columns**

- id: bigint unsigned, PK, auto increment
- name: varchar(255), not null
- slug: varchar(255), not null, unique
- description: text, null
- status: enum('draft','published'), not null, default 'draft'
- published_at: timestamp, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- UNIQUE (slug)
- INDEX (status, published_at)

### gallery_items

**Columns**

- id: bigint unsigned, PK, auto increment
- gallery_id: bigint unsigned, not null, FK -> galleries.id (ON DELETE CASCADE)
- title: varchar(255), null
- caption: text, null
- image_path: varchar(255), not null
- taken_at: date, null
- published_at: timestamp, null
- sort_order: smallint unsigned, not null, default 0
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (gallery_id, sort_order)
- INDEX (published_at)

### documents

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null
- slug: varchar(255), not null, unique
- category: varchar(255), null
- description: text, null
- file_path: varchar(255), not null
- file_type: varchar(20), null
- file_size: bigint unsigned, null
- is_published: tinyint(1), not null, default 1
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- UNIQUE (slug)
- INDEX (category, is_published)

### settings

**Columns**

- id: bigint unsigned, PK, auto increment
- key: varchar(255), not null, unique
- value: longtext, null
- type: varchar(30), not null, default 'string'
- group: varchar(50), not null, default 'general'
- created_at: timestamp, null
- updated_at: timestamp, null

### tracer_study_links

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), not null, default 'Tracer Study'
- description: text, null
- form_url: varchar(255), not null
- is_active: tinyint(1), not null, default 1
- published_at: timestamp, null
- created_at: timestamp, null
- updated_at: timestamp, null

### vision_missions

**Columns**

- id: bigint unsigned, PK, auto increment
- vision_title: varchar(255), not null, default 'Visi'
- vision_text: text, not null
- mission_title: varchar(255), not null, default 'Misi'
- mission_text: longtext, not null
- is_active: tinyint(1), not null, default 1
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- updated_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (is_active)

### activities

**Columns**

- id: bigint unsigned, PK, auto increment
- category: varchar(120), not null
- title: varchar(255), not null
- description: text, null
- location: varchar(255), null
- event_date: date, not null
- published_at: timestamp, null
- image_path: varchar(255), null
- sort_order: smallint unsigned, not null, default 0
- is_published: tinyint(1), not null, default 1
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- INDEX (event_date, is_published)
- INDEX (published_at)

### hero_slides

**Columns**

- id: bigint unsigned, PK, auto increment
- title: varchar(255), null
- subtitle: varchar(255), null
- image_path: varchar(255), not null
- sort_order: smallint unsigned, not null, default 0
- is_active: tinyint(1), not null, default 1
- start_at: timestamp, null
- end_at: timestamp, null
- created_at: timestamp, null
- updated_at: timestamp, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- updated_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)

**Indexes**

- INDEX (is_active, sort_order)

### lecturer_staff

**Columns**

- id: bigint unsigned, PK, auto increment
- name: varchar(255), not null
- position: varchar(255), not null
- type: enum('lecturer','staff'), not null, default 'lecturer'
- email: varchar(255), null
- bio: text, null
- photo_path: varchar(255), null
- sort_order: smallint unsigned, not null, default 0
- is_active: tinyint(1), not null, default 1
- created_at: timestamp, null
- updated_at: timestamp, null
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- updated_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)

**Indexes**

- INDEX (type, is_active, sort_order)

### tracer_alumnis

**Columns**

- id: bigint unsigned, PK, auto increment
- nim: varchar(30), not null, unique
- graduation_year: smallint unsigned, null
- company_name: varchar(255), not null
- company_level: varchar(255), null
- department: varchar(255), null
- relevance: varchar(255), null
- notes: text, null
- is_active: tinyint(1), not null, default 1
- created_by: bigint unsigned, null, FK -> users.id (ON DELETE SET NULL)
- created_at: timestamp, null
- updated_at: timestamp, null

**Indexes**

- UNIQUE (nim)
- INDEX (is_active)
- INDEX (graduation_year)

## Relationship Summary

The following section expands each foreign-key relationship with its
cardinality (one-to-many, one-to-one, many-to-many), direction (which side
is the parent/owner and which side is the child), and DB-level behavior
(ON DELETE rules) when defined. This makes the ERD easier to read and the
intent of each constraint explicit.

Notation used below:

- **Type:** the relational pattern (One-to-Many = 1:N, One-to-One = 1:1,
  Many-to-Many = M:N)
- **Cardinality:** shows the parent (1) and child (N) where applicable
- **Direction:** `child.column -> parent.table.column` (FK stored on child)
- **Notes:** nullability, cascade behavior, and practical implications

### Relationships (detailed)

- **curricula.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One (many `curricula` rows may reference a single `users` row)
    - Cardinality: `users (1)` <-- `curricula (N)`
    - Direction: `curricula.created_by` (child FK) -> `users.id` (parent PK)
    - Notes: `created_by` is nullable; when the referenced user is deleted,
      the value is set to NULL rather than deleting the curriculum.

- **curriculum_courses.curriculum_id -> curricula.id** (ON DELETE CASCADE)
    - Type: One-to-Many (one curriculum owns many courses)
    - Cardinality: `curricula (1)` <-- `curriculum_courses (N)`
    - Direction: `curriculum_courses.curriculum_id` -> `curricula.id`
    - Notes: cascade-on-delete ensures courses are removed automatically
      when a curriculum is deleted (strong ownership relationship).

- **cpl_items.cpl_category_id -> cpl_categories.id** (ON DELETE CASCADE)
    - Type: One-to-Many (category has many CPL items)
    - Cardinality: `cpl_categories (1)` <-- `cpl_items (N)`
    - Direction: `cpl_items.cpl_category_id` -> `cpl_categories.id`
    - Notes: cascade-on-delete; deleting a category removes its items.

- **announcements.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One (many announcements may be created by one user)
    - Cardinality: `users (1)` <-- `announcements (N)`
    - Notes: nullable FK, set to NULL when the creator user is deleted.

- **announcements.updated_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One (many announcements may be updated by one user)
    - Cardinality: `users (1)` <-- `announcements (N)`
    - Notes: stores last updater; nullable and set to NULL on user deletion.

- **aspirations.read_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One (many aspirations records may be marked read by one user)
    - Cardinality: `users (1)` <-- `aspirations (N)`
    - Notes: indicates which admin marked the aspiration as read; nullable.

- **academic_events.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `academic_events (N)`
    - Notes: event ownership/creator tracking; nullable.

- **projects.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `projects (N)`
    - Notes: creator tracking for student projects; nullable.

- **researches.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `researches (N)`
    - Notes: author/owner tracking; nullable.

- **community_services.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `community_services (N)`
    - Notes: owner/creator tracking; nullable.

- **galleries.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `galleries (N)`
    - Notes: gallery ownership; nullable.

- **gallery_items.gallery_id -> galleries.id** (ON DELETE CASCADE)
    - Type: One-to-Many
    - Cardinality: `galleries (1)` <-- `gallery_items (N)`
    - Notes: gallery owns items; cascade-on-delete removes child items when
      the parent gallery is deleted.

- **documents.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `documents (N)`
    - Notes: document owner/creator tracking; nullable.

- **tracer_alumnis.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `tracer_alumnis (N)`
    - Notes: admin who created the tracer alumni record; nullable.

- **vision_missions.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `vision_missions (N)`
    - Notes: creator tracking; nullable.

- **vision_missions.updated_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `vision_missions (N)`
    - Notes: last updater tracking; nullable.

- **activities.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `activities (N)`
    - Notes: creator tracking for program activities; nullable.

- **hero_slides.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `hero_slides (N)`
    - Notes: admin who created the hero slide; nullable.

- **hero_slides.updated_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `hero_slides (N)`
    - Notes: last editor of the hero slide; nullable.

- **lecturer_staff.created_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `lecturer_staff (N)`
    - Notes: admin who created the lecturer/staff record; nullable.

- **lecturer_staff.updated_by -> users.id** (ON DELETE SET NULL)
    - Type: Many-to-One
    - Cardinality: `users (1)` <-- `lecturer_staff (N)`
    - Notes: last editor of the lecturer/staff record; nullable.

### Summary notes and clarifications

- **One-to-One relations:** there are no explicit 1:1 constraints declared via
  unique foreign keys in the current migrations. A 1:1 relation would
  require a UNIQUE constraint on the child FK (or a shared PK), which is not
  present in the schema.

- **Many-to-Many relations:** the current migrations do not define any pivot
  (join) tables for M:N relationships. If you need M:N (example: `courses`
  and `tags`), create an explicit pivot table (e.g. `course_tag`) with two
  FKs and an index on both columns.

- **Standalone tables (no outward FK constraints):** `settings`, `hero_slides`,
  `lecturer_staff`, `password_reset_tokens`, `sessions`,
  `cache`, `jobs`, `failed_jobs` are intentionally independent and therefore
  not shown with outward relational edges in the ERD. Note that some of
  these tables may contain columns titled like `user_id` but do not declare
  DB-level FK constraints (e.g. `sessions.user_id` is indexed but not
  constrained).

If you'd like, I can:

- Render a compact tabular matrix of parent/child cardinalities for quick
  reference, or
- Produce a Mermaid ER diagram that visually encodes these cardinalities
  using crow's foot or 1..\* notation.

## Notes on Dropped Columns

- curricula: academic_year, is_active
- curriculum_courses: semester
- aspirations: full_name
- projects: demo_url, repository_url
