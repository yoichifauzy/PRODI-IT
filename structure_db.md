# Struktur Database

Dokumen ini merangkum struktur database utama yang digunakan pada panel admin dan public interface. Sumbernya dari seluruh migration pada folder `database/migrations`.

## Ruang lingkup

- Fokus pada tabel penting domain aplikasi (admin + konten).
- Tabel sistem Laravel seperti `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, dan `password_reset_tokens` tidak dirinci.
- Kolom otomatis bawaan Laravel seperti `created_at`, `updated_at`, `deleted_at`, `remember_token`, dan `email_verified_at` tidak dicantumkan.

## Catatan perubahan penting

- Kolom `aspirations.full_name` sudah dihapus.
- Kolom `projects.demo_url` dan `projects.repository_url` sudah dihapus.
- Kolom tambahan dari migration lanjutan: `academic_events.google_event_url`, `activities.published_at`, `gallery_items.published_at`, `tracer_alumnis.graduation_year`.

## Relasi utama (ringkas)

- `users` -> banyak tabel konten melalui `created_by`, `updated_by`, `read_by` (nullOnDelete).
- `curricula` 1..n `curriculum_courses` (cascadeOnDelete).
- `cpl_categories` 1..n `cpl_items` (cascadeOnDelete).
- `galleries` 1..n `gallery_items` (cascadeOnDelete).

## Detail tabel penting

### Nama Tabel: `users`

Fungsi: akun admin untuk login dan audit data.
Primary Key: `id`

| No  | Nama Kolom | Tipe            | Panjang | Keterangan         |
| --- | ---------- | --------------- | ------- | ------------------ |
| 1   | id         | bigint unsigned | -       | PK, auto increment |
| 2   | name       | varchar         | 255     | nama admin         |
| 3   | email      | varchar         | 255     | unik               |
| 4   | role       | varchar         | 20      | default: admin     |
| 5   | is_active  | boolean         | -       | default: true      |
| 6   | password   | varchar         | 255     | hash password      |

### Nama Tabel: `settings`

Fungsi: menyimpan konfigurasi global website.
Primary Key: `id`

| No  | Nama Kolom | Tipe            | Panjang | Keterangan         |
| --- | ---------- | --------------- | ------- | ------------------ |
| 1   | id         | bigint unsigned | -       | PK, auto increment |
| 2   | key        | varchar         | 255     | unik               |
| 3   | value      | longtext        | -       | nullable           |
| 4   | type       | varchar         | 30      | default: string    |
| 5   | group      | varchar         | 50      | default: general   |

### Nama Tabel: `hero_slides`

Fungsi: materi hero slider pada halaman beranda.
Primary Key: `id`

| No  | Nama Kolom | Tipe              | Panjang | Keterangan         |
| --- | ---------- | ----------------- | ------- | ------------------ |
| 1   | id         | bigint unsigned   | -       | PK, auto increment |
| 2   | title      | varchar           | 255     | nullable           |
| 3   | subtitle   | varchar           | 255     | nullable           |
| 4   | image_path | varchar           | 255     | path file gambar   |
| 5   | sort_order | smallint unsigned | -       | default: 0         |
| 6   | is_active  | boolean           | -       | default: true      |
| 7   | start_at   | timestamp         | -       | nullable           |
| 8   | end_at     | timestamp         | -       | nullable           |

### Nama Tabel: `vision_missions`

Fungsi: konten visi dan misi program studi.
Primary Key: `id`

| No  | Nama Kolom    | Tipe            | Panjang | Keterangan               |
| --- | ------------- | --------------- | ------- | ------------------------ |
| 1   | id            | bigint unsigned | -       | PK, auto increment       |
| 2   | vision_title  | varchar         | 255     | default: Visi            |
| 3   | vision_text   | text            | -       | isi visi                 |
| 4   | mission_title | varchar         | 255     | default: Misi            |
| 5   | mission_text  | longtext        | -       | isi misi                 |
| 6   | is_active     | boolean         | -       | default: true            |
| 7   | created_by    | bigint unsigned | -       | nullable, FK -> users.id |
| 8   | updated_by    | bigint unsigned | -       | nullable, FK -> users.id |

### Nama Tabel: `curricula`

Fungsi: master kurikulum program studi.
Primary Key: `id`

| No  | Nama Kolom      | Tipe            | Panjang | Keterangan               |
| --- | --------------- | --------------- | ------- | ------------------------ |
| 1   | id              | bigint unsigned | -       | PK, auto increment       |
| 2   | name            | varchar         | 255     | nama kurikulum           |
| 3   | major_selection | varchar         | 20      | nullable                 |
| 4   | description     | text            | -       | nullable                 |
| 5   | is_active       | boolean         | -       | default: false           |
| 6   | created_by      | bigint unsigned | -       | nullable, FK -> users.id |

### Nama Tabel: `curriculum_courses`

Fungsi: mata kuliah per kurikulum dan semester.
Primary Key: `id`

| No  | Nama Kolom       | Tipe              | Panjang | Keterangan                          |
| --- | ---------------- | ----------------- | ------- | ----------------------------------- |
| 1   | id               | bigint unsigned   | -       | PK, auto increment                  |
| 2   | curriculum_id    | bigint unsigned   | -       | FK -> curricula.id, cascadeOnDelete |
| 3   | semester         | tinyint unsigned  | -       | semester ke-                        |
| 4   | code             | varchar           | 20      | kode mata kuliah                    |
| 5   | name             | varchar           | 255     | nama mata kuliah                    |
| 6   | credits_theory   | tinyint unsigned  | -       | sks teori                           |
| 7   | credits_practice | tinyint unsigned  | -       | sks praktik                         |
| 8   | sort_order       | smallint unsigned | -       | default: 0                          |

Catatan indeks: unik (`curriculum_id`, `semester`, `code`), index (`curriculum_id`, `semester`).

### Nama Tabel: `cpl_categories`

Fungsi: kategori capaian pembelajaran lulusan.
Primary Key: `id`

| No  | Nama Kolom  | Tipe             | Panjang | Keterangan         |
| --- | ----------- | ---------------- | ------- | ------------------ |
| 1   | id          | bigint unsigned  | -       | PK, auto increment |
| 2   | name        | varchar          | 255     | nama kategori      |
| 3   | slug        | varchar          | 255     | unik               |
| 4   | description | text             | -       | nullable           |
| 5   | sort_order  | tinyint unsigned | -       | default: 0         |

### Nama Tabel: `cpl_items`

Fungsi: daftar item capaian pembelajaran per kategori.
Primary Key: `id`

| No  | Nama Kolom      | Tipe              | Panjang | Keterangan                               |
| --- | --------------- | ----------------- | ------- | ---------------------------------------- |
| 1   | id              | bigint unsigned   | -       | PK, auto increment                       |
| 2   | cpl_category_id | bigint unsigned   | -       | FK -> cpl_categories.id, cascadeOnDelete |
| 3   | code            | varchar           | 30      | nullable                                 |
| 4   | description     | text              | -       | deskripsi CPL                            |
| 5   | sort_order      | smallint unsigned | -       | default: 0                               |

### Nama Tabel: `announcements`

Fungsi: pengumuman resmi program studi.
Primary Key: `id`

| No  | Nama Kolom   | Tipe            | Panjang                    | Keterangan               |
| --- | ------------ | --------------- | -------------------------- | ------------------------ |
| 1   | id           | bigint unsigned | -                          | PK, auto increment       |
| 2   | title        | varchar         | 255                        | judul pengumuman         |
| 3   | slug         | varchar         | 255                        | unik                     |
| 4   | excerpt      | text            | -                          | nullable                 |
| 5   | content      | longtext        | -                          | isi pengumuman           |
| 6   | cover_image  | varchar         | 255                        | nullable                 |
| 7   | status       | enum            | draft, published, archived | default: draft           |
| 8   | published_at | timestamp       | -                          | nullable                 |
| 9   | created_by   | bigint unsigned | -                          | nullable, FK -> users.id |
| 10  | updated_by   | bigint unsigned | -                          | nullable, FK -> users.id |

Catatan indeks: index (`status`, `published_at`).

### Nama Tabel: `aspirations`

Fungsi: aspirasi dan masukan dari pengunjung.
Primary Key: `id`

| No  | Nama Kolom | Tipe            | Panjang                | Keterangan               |
| --- | ---------- | --------------- | ---------------------- | ------------------------ |
| 1   | id         | bigint unsigned | -                      | PK, auto increment       |
| 2   | email      | varchar         | 255                    | email pengirim           |
| 3   | nim        | varchar         | 30                     | nullable                 |
| 4   | subject    | varchar         | 255                    | subjek aspirasi          |
| 5   | message    | text            | -                      | isi pesan                |
| 6   | status     | enum            | unread, read, archived | default: unread          |
| 7   | read_at    | timestamp       | -                      | nullable                 |
| 8   | read_by    | bigint unsigned | -                      | nullable, FK -> users.id |
| 9   | ip_address | varchar         | 45                     | nullable                 |
| 10  | user_agent | text            | -                      | nullable                 |

Catatan indeks: index (`nim`), index (`status`, `created_at`).

### Nama Tabel: `academic_events`

Fungsi: kalender akademik dan event resmi.
Primary Key: `id`

| No  | Nama Kolom       | Tipe            | Panjang                                | Keterangan               |
| --- | ---------------- | --------------- | -------------------------------------- | ------------------------ |
| 1   | id               | bigint unsigned | -                                      | PK, auto increment       |
| 2   | title            | varchar         | 255                                    | judul event              |
| 3   | slug             | varchar         | 255                                    | unik                     |
| 4   | description      | text            | -                                      | nullable                 |
| 5   | event_type       | enum            | krs, uts, uas, holiday, seminar, other | default: other           |
| 6   | start_date       | date            | -                                      | tanggal mulai            |
| 7   | end_date         | date            | -                                      | nullable                 |
| 8   | location         | varchar         | 255                                    | nullable                 |
| 9   | google_event_url | varchar         | 255                                    | nullable                 |
| 10  | is_published     | boolean         | -                                      | default: true            |
| 11  | created_by       | bigint unsigned | -                                      | nullable, FK -> users.id |

Catatan indeks: index (`start_date`, `end_date`), index (`event_type`, `is_published`).

### Nama Tabel: `activities`

Fungsi: kegiatan prodi dan dokumentasinya.
Primary Key: `id`

| No  | Nama Kolom   | Tipe              | Panjang | Keterangan               |
| --- | ------------ | ----------------- | ------- | ------------------------ |
| 1   | id           | bigint unsigned   | -       | PK, auto increment       |
| 2   | category     | varchar           | 120     | kategori kegiatan        |
| 3   | title        | varchar           | 255     | judul kegiatan           |
| 4   | description  | text              | -       | nullable                 |
| 5   | location     | varchar           | 255     | nullable                 |
| 6   | event_date   | date              | -       | tanggal kegiatan         |
| 7   | published_at | timestamp         | -       | nullable                 |
| 8   | image_path   | varchar           | 255     | nullable                 |
| 9   | sort_order   | smallint unsigned | -       | default: 0               |
| 10  | is_published | boolean           | -       | default: true            |
| 11  | created_by   | bigint unsigned   | -       | nullable, FK -> users.id |

Catatan indeks: index (`event_date`, `is_published`), index (`published_at`).

### Nama Tabel: `galleries`

Fungsi: master galeri kegiatan.
Primary Key: `id`

| No  | Nama Kolom   | Tipe            | Panjang          | Keterangan               |
| --- | ------------ | --------------- | ---------------- | ------------------------ |
| 1   | id           | bigint unsigned | -                | PK, auto increment       |
| 2   | name         | varchar         | 255              | nama galeri              |
| 3   | slug         | varchar         | 255              | unik                     |
| 4   | description  | text            | -                | nullable                 |
| 5   | status       | enum            | draft, published | default: draft           |
| 6   | published_at | timestamp       | -                | nullable                 |
| 7   | created_by   | bigint unsigned | -                | nullable, FK -> users.id |

Catatan indeks: index (`status`, `published_at`).

### Nama Tabel: `gallery_items`

Fungsi: item foto atau video di dalam galeri.
Primary Key: `id`

| No  | Nama Kolom   | Tipe              | Panjang | Keterangan                          |
| --- | ------------ | ----------------- | ------- | ----------------------------------- |
| 1   | id           | bigint unsigned   | -       | PK, auto increment                  |
| 2   | gallery_id   | bigint unsigned   | -       | FK -> galleries.id, cascadeOnDelete |
| 3   | title        | varchar           | 255     | nullable                            |
| 4   | caption      | text              | -       | nullable                            |
| 5   | image_path   | varchar           | 255     | path media                          |
| 6   | taken_at     | date              | -       | nullable                            |
| 7   | published_at | timestamp         | -       | nullable                            |
| 8   | sort_order   | smallint unsigned | -       | default: 0                          |

Catatan indeks: index (`gallery_id`, `sort_order`), index (`published_at`).

### Nama Tabel: `documents`

Fungsi: dokumen pendukung (contoh akreditasi, sertifikat).
Primary Key: `id`

| No  | Nama Kolom   | Tipe            | Panjang | Keterangan               |
| --- | ------------ | --------------- | ------- | ------------------------ |
| 1   | id           | bigint unsigned | -       | PK, auto increment       |
| 2   | title        | varchar         | 255     | judul dokumen            |
| 3   | slug         | varchar         | 255     | unik                     |
| 4   | category     | varchar         | 255     | nullable                 |
| 5   | description  | text            | -       | nullable                 |
| 6   | file_path    | varchar         | 255     | path file                |
| 7   | file_type    | varchar         | 20      | nullable                 |
| 8   | file_size    | bigint unsigned | -       | nullable                 |
| 9   | is_published | boolean         | -       | default: true            |
| 10  | created_by   | bigint unsigned | -       | nullable, FK -> users.id |

Catatan indeks: index (`category`, `is_published`).

### Nama Tabel: `projects`

Fungsi: project mahasiswa.
Primary Key: `id`

| No  | Nama Kolom   | Tipe              | Panjang          | Keterangan               |
| --- | ------------ | ----------------- | ---------------- | ------------------------ |
| 1   | id           | bigint unsigned   | -                | PK, auto increment       |
| 2   | title        | varchar           | 255              | judul project            |
| 3   | slug         | varchar           | 255              | unik                     |
| 4   | student_name | varchar           | 255              | nama mahasiswa           |
| 5   | student_nim  | varchar           | 30               | nullable                 |
| 6   | year         | smallint unsigned | -                | nullable                 |
| 7   | summary      | text              | -                | nullable                 |
| 8   | thumbnail    | varchar           | 255              | nullable                 |
| 9   | status       | enum              | draft, published | default: draft           |
| 10  | is_featured  | boolean           | -                | default: false           |
| 11  | published_at | timestamp         | -                | nullable                 |
| 12  | created_by   | bigint unsigned   | -                | nullable, FK -> users.id |

Catatan indeks: index (`status`, `published_at`), index (`year`).

### Nama Tabel: `researches`

Fungsi: data penelitian dosen atau mahasiswa.
Primary Key: `id`

| No  | Nama Kolom      | Tipe              | Panjang                      | Keterangan               |
| --- | --------------- | ----------------- | ---------------------------- | ------------------------ |
| 1   | id              | bigint unsigned   | -                            | PK, auto increment       |
| 2   | title           | varchar           | 255                          | judul penelitian         |
| 3   | researcher_name | varchar           | 255                          | nama peneliti            |
| 4   | researcher_role | enum              | dosen, mahasiswa, kolaborasi | default: dosen           |
| 5   | year            | smallint unsigned | -                            | tahun                    |
| 6   | publication     | varchar           | 255                          | nullable                 |
| 7   | link            | varchar           | 255                          | nullable                 |
| 8   | abstract        | text              | -                            | nullable                 |
| 9   | status          | enum              | draft, published             | default: draft           |
| 10  | created_by      | bigint unsigned   | -                            | nullable, FK -> users.id |

Catatan indeks: index (`year`, `status`).

### Nama Tabel: `community_services`

Fungsi: data pengabdian masyarakat.
Primary Key: `id`

| No  | Nama Kolom          | Tipe            | Panjang          | Keterangan               |
| --- | ------------------- | --------------- | ---------------- | ------------------------ |
| 1   | id                  | bigint unsigned | -                | PK, auto increment       |
| 2   | title               | varchar         | 255              | judul kegiatan           |
| 3   | activity_date       | date            | -                | tanggal kegiatan         |
| 4   | location            | varchar         | 255              | nullable                 |
| 5   | organizer           | varchar         | 255              | nullable                 |
| 6   | summary             | text            | -                | nullable                 |
| 7   | documentation_cover | varchar         | 255              | nullable                 |
| 8   | status              | enum            | draft, published | default: draft           |
| 9   | created_by          | bigint unsigned | -                | nullable, FK -> users.id |

Catatan indeks: index (`activity_date`), index (`status`).

### Nama Tabel: `lecturer_staff`

Fungsi: data dosen dan staff program studi.
Primary Key: `id`

| No  | Nama Kolom | Tipe              | Panjang         | Keterangan         |
| --- | ---------- | ----------------- | --------------- | ------------------ |
| 1   | id         | bigint unsigned   | -               | PK, auto increment |
| 2   | name       | varchar           | 255             | nama personil      |
| 3   | position   | varchar           | 255             | jabatan            |
| 4   | type       | enum              | lecturer, staff | default: lecturer  |
| 5   | email      | varchar           | 255             | nullable           |
| 6   | bio        | text              | -               | nullable           |
| 7   | photo_path | varchar           | 255             | nullable           |
| 8   | sort_order | smallint unsigned | -               | default: 0         |
| 9   | is_active  | boolean           | -               | default: true      |

Catatan indeks: index (`type`, `is_active`, `sort_order`).

### Nama Tabel: `tracer_alumnis`

Fungsi: data tracer alumni.
Primary Key: `id`

| No  | Nama Kolom      | Tipe              | Panjang | Keterangan         |
| --- | --------------- | ----------------- | ------- | ------------------ |
| 1   | id              | bigint unsigned   | -       | PK, auto increment |
| 2   | nim             | varchar           | 30      | unik               |
| 3   | graduation_year | smallint unsigned | -       | nullable           |
| 4   | company_name    | varchar           | 255     | nama perusahaan    |
| 5   | company_level   | varchar           | 255     | nullable           |
| 6   | department      | varchar           | 255     | nullable           |
| 7   | relevance       | varchar           | 255     | nullable           |
| 8   | notes           | text              | -       | nullable           |
| 9   | is_active       | boolean           | -       | default: true      |

Catatan indeks: unique (`nim`), index (`is_active`), index (`graduation_year`).

### Nama Tabel: `tracer_study_links`

Fungsi: tautan form tracer study.
Primary Key: `id`

| No  | Nama Kolom   | Tipe            | Panjang | Keterangan               |
| --- | ------------ | --------------- | ------- | ------------------------ |
| 1   | id           | bigint unsigned | -       | PK, auto increment       |
| 2   | title        | varchar         | 255     | default: Tracer Study    |
| 3   | description  | text            | -       | nullable                 |
| 4   | form_url     | varchar         | 255     | url form                 |
| 5   | is_active    | boolean         | -       | default: true            |
| 6   | published_at | timestamp       | -       | nullable                 |
| 7   | created_by   | bigint unsigned | -       | nullable, FK -> users.id |
