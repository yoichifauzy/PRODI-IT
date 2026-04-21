# Dokumentasi Lengkap Project Web Prodi TI

## 1. Gambaran Umum Project

Project ini adalah website Program Studi Teknologi Informasi yang dibagi menjadi dua area utama:

1. Area Publik (untuk pengunjung/user).
2. Area Admin (untuk pengelolaan konten website).

Secara arsitektur, aplikasi memakai pola MVC pada Laravel:

- Model: merepresentasikan data (pengumuman, event, proyek, kurikulum, dll).
- View: halaman antarmuka publik dan admin (Blade).
- Controller: logika bisnis untuk mengambil/menyimpan data.
- Route: pemetaan URL ke controller.
- Middleware: kontrol akses dan keamanan sesi admin, serta pengaturan locale publik.

## 2. Teknologi dan Arsitektur

### 2.1 Backend

- PHP + Laravel 13.
- Eloquent ORM untuk query data.
- Blade Template Engine.
- Middleware custom:
    - `EnsureAdminRole`: membatasi akses admin hanya untuk role `admin`/`super_admin` yang aktif.
    - `EnsureAdminSessionSecurity`: fingerprint user-agent dan idle timeout sesi admin.
    - `SetPublicLocale`: pengaturan bahasa publik (`id`/`en`) via query/session/cookie.

### 2.2 Frontend

- Vite sebagai bundler/build tool.
- Tailwind CSS untuk styling.
- JavaScript untuk interaksi UI (tema, navbar mobile, i18n client-side, dsb).

### 2.3 Struktur Akses

- Publik: semua pengunjung dapat melihat halaman publik tanpa login.
- Admin: harus login melalui `/admin/login` dan lolos middleware `auth`, `admin`, `admin.session`.

## 3. Halaman Publik (User)

Berikut halaman publik yang tersedia dan fungsinya.

| No  | URL                                 | Route Name                    | Controller@Method                         | View                          | Fungsi Utama                                                                                                       |
| --- | ----------------------------------- | ----------------------------- | ----------------------------------------- | ----------------------------- | ------------------------------------------------------------------------------------------------------------------ |
| 1   | `/`                                 | `home`                        | `HomeController@index`                    | `public.home`                 | Beranda utama dengan section dinamis (hero, tentang, visi-misi, kegiatan, galeri, kalender, aspirasi, akreditasi). |
| 2   | `/kalender-akademik`                | `calendar.index`              | `AcademicCalendarController@index`        | `public.calendar`             | Kalender akademik bulanan, navigasi bulan, menampilkan event yang dipublish.                                       |
| 3   | `/kegiatan`                         | `public.activities`           | `PublicPageController@activities`         | `public.activities`           | Daftar kegiatan yang dipublish (dengan pagination).                                                                |
| 4   | `/kegiatan/{activity}`              | `public.activities.show`      | `PublicPageController@activityShow`       | `public.activity-detail`      | Detail kegiatan + rekomendasi kegiatan terkait.                                                                    |
| 5   | `/dosen-dan-staff`                  | `public.lecturer-staff`       | `PublicPageController@lecturerStaff`      | `public.lecturer-staff`       | Daftar dosen/staff aktif, dengan filter tipe dan pencarian.                                                        |
| 6   | `/dosen-dan-staff/{lecturerStaff}`  | `public.lecturer-staff.blogs` | `PublicPageController@lecturerStaffBlogs` | `public.lecturer-staff-blogs` | Daftar blog/aktivitas dari dosen/staff tertentu yang dipublish.                                                    |
| 7   | `/kurikulum`                        | `public.curriculum`           | `PublicPageController@curriculum`         | `public.curriculum`           | Menampilkan kurikulum dan daftar mata kuliah per semester.                                                         |
| 8   | `/project-mahasiswa`                | `public.projects`             | `PublicPageController@projects`           | `public.projects`             | Daftar project mahasiswa (featured + regular).                                                                     |
| 9   | `/project-mahasiswa/{project:slug}` | `public.projects.show`        | `PublicPageController@projectShow`        | `public.project-detail`       | Detail project mahasiswa + project terkait.                                                                        |
| 10  | `/tracer-alumni`                    | `public.tracer-alumni`        | `PublicPageController@tracerAlumni`       | `public.tracer-alumni`        | Data tracer alumni aktif, bisa filter berdasarkan tahun lulus.                                                     |
| 11  | `/pengumuman`                       | `public.announcements`        | `PublicPageController@announcements`      | `public.announcements`        | Daftar pengumuman yang tampil publik sesuai aturan publish/schedule.                                               |
| 12  | `/pengumuman/sync`                  | `public.announcements.sync`   | `PublicPageController@announcementsSync`  | - (JSON)                      | Endpoint sinkronisasi signature/count pengumuman untuk update data real-time ringan.                               |
| 13  | `POST /aspirations`                 | `aspirations.store`           | `PublicSite\AspirationController@store`   | -                             | Submit form aspirasi dari user (disimpan ke inbox admin).                                                          |

### 3.1 Detail Section di Halaman Home

Section pada halaman beranda (`public.home`):

1. Hero (`#hero`): slide hero aktif berdasarkan jadwal (`start_at`, `end_at`).
2. Tentang Kami (`#tentang`): konten dari tabel settings.
3. Visi & Misi (`#visi-misi`): diambil dari data aktif (atau fallback data terbaru).
4. Kegiatan (`#acara`): daftar kegiatan publik terbaru.
5. Galeri (`#galeri`): kategori galeri + item galeri yang sudah eligible tampil publik.
6. Kalender (`#kalender`): ringkasan event akademik.
7. Aspirasi (`#aspirasi`): form kirim aspirasi user.
8. Akreditasi (`#akreditasi`): section informasi tambahan.

### 3.2 Validasi Form Aspirasi Publik

Field form aspirasi:

- `full_name` (wajib)
- `email` (wajib, format RFC+DNS)
- `nim` (opsional)
- `subject` (wajib)
- `message` (wajib, max 3000 karakter)

Sistem juga menyimpan metadata:

- `ip_address`
- `user_agent`

## 4. Halaman Admin dan Kemampuan Admin (Lengkap)

### 4.1 Halaman Akses Admin

| URL                    | Fungsi                         |
| ---------------------- | ------------------------------ |
| `/admin/login` (GET)   | Menampilkan form login admin.  |
| `/admin/login` (POST)  | Proses login (email/password). |
| `/admin/logout` (POST) | Logout admin.                  |
| `/admin`               | Dashboard admin.               |

Ketentuan akses:

- Hanya user dengan role `admin` atau `super_admin` dan `is_active = true`.
- Sesi admin diamankan dengan fingerprint user-agent dan idle timeout.

### 4.2 Menu Admin yang Tersedia

Menu di panel admin:

1. Dashboard
2. Hero Section
3. Tentang Kami
4. Kegiatan Kami
5. Filter Galeri
6. Item Galeri
7. Dosen & Staff
8. Kurikulum
9. Mata Kuliah Kurikulum
10. Project Mahasiswa
11. Tracer Alumni
12. Pengumuman
13. Kalender Akademik
14. Visi & Misi
15. Aspirasi

### 4.3 Rincian Kemampuan Admin per Modul

#### A. Dashboard

Admin bisa:

1. Melihat statistik ringkas:
    - Total pengumuman.
    - Jumlah pengumuman publish.
    - Total aspirasi.
    - Jumlah aspirasi belum dibaca.
    - Total event akademik.
    - Jumlah event yang publish.
2. Melihat 5 aspirasi terbaru dan membuka detailnya.

#### B. Hero Section (`admin.hero-slides.*`)

Admin bisa:

1. Melihat daftar slide hero dengan urutan.
2. Menambah slide baru.
3. Mengubah slide.
4. Menghapus slide.
5. Upload gambar slide.
6. Mengatur:
    - `title`, `subtitle`
    - `sort_order`
    - `is_active`
    - jadwal tayang `start_at` dan `end_at`.

#### C. Tentang Kami (`admin.about-section.*`)

Admin bisa:

1. Edit konten section tentang kami (single settings page).
2. Mengubah teks:
    - judul section
    - subtitle section
    - heading
    - deskripsi utama
    - deskripsi kedua
3. Upload media:
    - gambar 1
    - gambar 2
    - video (mp4/mov/webm)
4. Sistem otomatis mengganti file lama saat file baru diunggah.

#### D. Kegiatan Kami (`admin.activities.*`)

Admin bisa:

1. Lihat daftar kegiatan.
2. Cari kegiatan (`q`) berdasarkan judul/kategori/deskripsi/lokasi.
3. Tambah kegiatan.
4. Edit kegiatan.
5. Hapus kegiatan.
6. Upload/ganti gambar kegiatan.
7. Mengatur data:
    - `category`, `title`, `description`, `location`
    - `event_date`
    - `published_at` (jadwal tampil)
    - `sort_order`
    - `is_published`.

#### E. Filter Galeri (`admin.galleries.*`)

Admin bisa:

1. Lihat daftar kategori/filter galeri.
2. Tambah kategori galeri.
3. Edit kategori galeri.
4. Hapus kategori galeri.
5. Atur:
    - `name`, `slug`, `description`
    - `status` (`draft`/`published`)
    - `published_at`.
6. Melihat jumlah item per kategori.
7. Dari kategori, langsung menuju tambah item galeri.

#### F. Item Galeri (`admin.gallery-items.*`)

Admin bisa:

1. Lihat daftar item galeri.
2. Filter item berdasarkan `gallery_id`.
3. Tambah item galeri.
4. Edit item galeri.
5. Hapus item galeri.
6. Upload/ganti gambar item.
7. Atur:
    - relasi ke kategori (`gallery_id`)
    - `title`, `caption`
    - `taken_at`, `published_at`
    - `sort_order`.

#### G. Dosen & Staff (`admin.lecturer-staff.*`)

Admin bisa:

1. Lihat daftar dosen/staff.
2. Filter berdasarkan tipe (`lecturer`/`staff`).
3. Tambah data dosen/staff.
4. Edit data dosen/staff.
5. Hapus data dosen/staff.
6. Upload/ganti foto profil.
7. Atur data:
    - `name`, `position`, `type`
    - `email`, `bio`
    - `sort_order`
    - `is_active`.
8. Akses manajemen blog per dosen/staff.

#### H. Blog Dosen/Staff (Nested: `admin.lecturer-staff.blogs.*`)

Admin bisa:

1. Melihat daftar blog milik dosen/staff tertentu.
2. Menambah blog.
3. Mengedit blog.
4. Menghapus blog.
5. Upload/ganti cover image blog.
6. Atur data blog:
    - `title`, `slug`, `description`, `location`
    - `activity_date`
    - `sort_order`
    - `is_published`.

#### I. Kurikulum (`admin.curricula.*`)

Admin bisa:

1. Lihat daftar kurikulum + jumlah mata kuliah tiap kurikulum.
2. Tambah kurikulum.
3. Edit kurikulum.
4. Hapus kurikulum.
5. Atur data:
    - `name`
    - `academic_year`
    - `description`
    - `is_active`.
6. Mengatur hanya satu kurikulum aktif dalam satu waktu.

#### J. Mata Kuliah Kurikulum (`admin.curriculum-courses.*`)

Admin bisa:

1. Lihat daftar mata kuliah.
2. Filter berdasarkan `curriculum_id`.
3. Tambah mata kuliah.
4. Edit mata kuliah.
5. Hapus mata kuliah.
6. Atur data:
    - `curriculum_id`
    - `semester`
    - `code`
    - `name`
    - `credits`
    - `short_syllabus`
    - `sort_order`.
7. Validasi unik kode mata kuliah per kombinasi kurikulum + semester.

#### K. Project Mahasiswa (`admin.projects.*`)

Admin bisa:

1. Lihat daftar project mahasiswa.
2. Cari project (`q`) berdasarkan judul, nama mahasiswa, atau NIM.
3. Tambah project.
4. Edit project.
5. Hapus project.
6. Upload/ganti thumbnail project.
7. Atur data:
    - `title`, `slug`
    - `student_name`, `student_nim`
    - `year`, `summary`
    - `demo_url`, `repository_url`
    - `status` (`draft`/`published`)
    - `is_featured`
    - `published_at`.
8. Aturan publish:
    - slug otomatis unik.
    - `published_at` dipakai untuk schedule tampil.

#### L. Tracer Alumni (`admin.tracer-alumni.*`)

Admin bisa:

1. Lihat daftar data tracer alumni.
2. Cari data (`q`) berdasarkan NIM, perusahaan, departemen, relevansi.
3. Tambah data tracer.
4. Edit data tracer.
5. Hapus data tracer.
6. Atur data:
    - `nim` (unik)
    - `graduation_year`
    - `company_name`, `company_level`
    - `department`, `relevance`, `notes`
    - `is_active`.

#### M. Pengumuman (`admin.announcements.*`)

Admin bisa:

1. Lihat daftar pengumuman.
2. Tambah pengumuman.
3. Edit pengumuman.
4. Hapus pengumuman.
5. Upload cover image file atau isi cover image URL.
6. Atur data:
    - `title`, `slug`
    - `content`
    - `status` (`draft`, `published`, `archived`)
    - `published_at`.
7. Sistem otomatis:
    - slug unik.
    - excerpt dari konten.
    - hapus file lama saat cover diganti.

#### N. Kalender Akademik (`admin.academic-events.*`)

Admin bisa:

1. Lihat daftar event akademik.
2. Filter per bulan (`month`) dan pencarian teks (`q`).
3. Tambah event.
4. Edit event.
5. Hapus event.
6. Atur data:
    - `title`, `slug`, `description`
    - `event_type` (`krs`, `uts`, `uas`, `holiday`, `seminar`, `other`)
    - `start_date`, `end_date`
    - `location`, `google_event_url`
    - `is_published`.

#### O. Visi & Misi (`admin.vision-missions.*`)

Admin bisa:

1. Lihat daftar data visi-misi.
2. Tambah data visi-misi.
3. Edit data visi-misi.
4. Hapus data visi-misi.
5. Set data aktif (`is_active`).
6. Sistem memastikan saat satu data diaktifkan, data lain dinonaktifkan.

#### P. Aspirasi (`admin.aspirations.*`)

Admin bisa:

1. Melihat inbox aspirasi.
2. Filter berdasarkan status (`unread`, `read`, `archived`).
3. Cari berdasarkan nama, email, NIM, subjek, isi pesan.
4. Membuka detail aspirasi (otomatis menandai jadi `read` saat dibuka).
5. Mengubah status aspirasi:
    - `unread`
    - `read`
    - `archived`.
6. Menghapus aspirasi.

## 5. Aturan Tampil Data ke Publik (Publish/Visibility Rules)

### 5.1 Announcement

Muncul di publik jika:

1. `status = published` dan `published_at` kosong atau <= sekarang.
2. Atau `status = draft` tetapi `published_at` sudah terlewati (scheduled draft).

### 5.2 Project

Muncul di publik jika:

1. `status = published` dan `published_at` kosong atau <= sekarang.
2. Atau `status = draft` dengan `published_at` sudah terlewati.

### 5.3 Activity

Muncul di publik jika:

1. `is_published = true`.
2. `published_at` kosong atau <= sekarang.

### 5.4 Academic Event

Muncul di publik jika `is_published = true`.

### 5.5 Gallery + Gallery Item

- Kategori galeri ditampilkan jika `status = published` dan `published_at` memenuhi syarat waktu.
- Item galeri ditampilkan jika `published_at` kosong atau <= sekarang.

### 5.6 Lecturer/Staff dan Blog

- Profile hanya tampil jika `is_active = true`.
- Blog hanya tampil jika `is_published = true`.

### 5.7 Tracer Alumni

Hanya data dengan `is_active = true` yang tampil di publik.

## 6. Penyimpanan File/Media

Semua upload memakai disk `public` Laravel, contoh lokasi:

1. `hero-slides/` untuk gambar hero.
2. `about/` dan `about/videos/` untuk section tentang.
3. `activities/` untuk gambar kegiatan.
4. `gallery-items/` untuk item galeri.
5. `lecturer-staff/` untuk foto profil dosen/staff.
6. `lecturer-staff-blogs/` untuk cover blog dosen/staff.
7. `projects/` untuk thumbnail project.
8. `announcements/` untuk cover pengumuman (jika file upload).

## 7. Ringkasan End-to-End Alur Sistem

### 7.1 Alur Publik ke Admin (Aspirasi)

1. User mengisi form aspirasi di halaman home.
2. Data tervalidasi dan disimpan.
3. Admin melihat data aspirasi di dashboard/inbox aspirasi.
4. Admin membaca, update status, atau menghapus aspirasi.

### 7.2 Alur Admin ke Publik (Konten)

1. Admin membuat/mengubah konten (pengumuman, project, event, dll).
2. Admin mengatur status publish dan jadwal tayang.
3. Konten tampil otomatis di halaman publik sesuai aturan visibility.

### 7.3 Alur Home Dinamis

1. Hero, about, visi-misi, kegiatan, galeri, kalender ditarik dari database.
2. Jika data utama tidak tersedia (misal visi-misi aktif), sistem fallback ke data terbaru.

## 8. Daftar Entitas Data Utama yang Aktif Dipakai di Fitur

Entitas yang terhubung langsung dengan halaman publik/admin saat ini:

1. `User`
2. `Announcement`
3. `AcademicEvent`
4. `VisionMission`
5. `HeroSlide`
6. `Activity`
7. `Gallery`
8. `GalleryItem`
9. `LecturerStaff`
10. `LecturerStaffBlog`
11. `Curriculum`
12. `CurriculumCourse`
13. `Project`
14. `TracerAlumni`
15. `Aspiration`
16. `Setting`

## 9. Catatan Implementasi

1. Panel admin tidak menggunakan remember-me untuk mengurangi risiko sesi panjang.
2. Ada validasi role dan status user aktif untuk akses admin.
3. Beberapa modul menerapkan soft delete (`Announcement`, `Project`, `Curriculum`).
4. Pengumuman punya endpoint sinkronisasi (`/pengumuman/sync`) untuk deteksi update data di sisi front-end.

---

Dokumen ini disusun berdasarkan route, controller, model, middleware, dan view yang saat ini aktif di project.
