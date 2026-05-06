# Metode Pengembangan Sistem

Metode pengembangan sistem yang digunakan pada Project Web Prodi TI ini adalah **Agile dengan pendekatan Iterative Incremental**. Metode ini dipilih karena kebutuhan sistem dapat berkembang secara bertahap, banyak modul yang saling terhubung, dan pihak pengguna dapat memberikan umpan balik pada setiap iterasi sehingga hasil pengembangan lebih sesuai dengan kebutuhan nyata.

Pendekatan **Iterative Incremental** membagi proses pengembangan menjadi beberapa siklus kecil yang berulang. Pada setiap siklus, sistem dibangun sedikit demi sedikit dalam bentuk fitur atau modul yang dapat diuji, dievaluasi, lalu disempurnakan pada siklus berikutnya. Dengan cara ini, pengembangan tidak menunggu seluruh sistem selesai untuk memperoleh hasil, tetapi menghasilkan versi sistem yang terus bertambah kualitas dan fungsinya.

## Alasan Pemilihan Metode

Metode Agile Iterative Incremental cocok untuk proyek Web Prodi TI karena:

1. Kebutuhan sistem bersifat dinamis dan berpotensi berubah mengikuti kebutuhan prodi.
2. Sistem terdiri dari banyak fitur seperti halaman publik, admin panel, data akademik, pengumuman, galeri, kurikulum, tracer alumni, dan lain-lain.
3. Pengembangan bertahap memudahkan validasi dari pengguna atau pihak pengelola website.
4. Risiko kesalahan dapat ditekan karena setiap tahap selalu disertai evaluasi dan pengujian.
5. Sistem dapat segera digunakan secara parsial walaupun seluruh fitur belum selesai dibangun.

## Tahapan Pengembangan Sistem

Dalam implementasinya, pendekatan Agile Iterative Incremental pada proyek ini dibagi menjadi enam tahap utama, yaitu **Requirements, Design, Development, Testing, Deployment,** dan **Review**. Setiap tahap memiliki tujuan, aktivitas, dan hasil keluaran yang berbeda.

### 1. Requirements

Tahap Requirements adalah tahap awal untuk mengumpulkan dan menganalisis kebutuhan sistem. Pada tahap ini, dilakukan identifikasi fitur apa saja yang dibutuhkan oleh pengguna, admin, dan pengelola konten.

Aktivitas pada tahap ini meliputi:

- Mengidentifikasi kebutuhan fungsional sistem.
- Mengidentifikasi kebutuhan non-fungsional seperti keamanan, performa, kemudahan akses, dan kompatibilitas perangkat.
- Menganalisis alur kerja website prodi.
- Mengumpulkan kebutuhan dari pihak terkait, misalnya admin prodi, dosen, staf, dan pengguna publik.
- Menentukan prioritas fitur yang akan dikerjakan lebih dulu.

Contoh kebutuhan pada proyek ini antara lain:

- Menampilkan informasi profil program studi.
- Mengelola pengumuman dan kegiatan.
- Menampilkan data dosen dan staff.
- Menampilkan kurikulum dan project mahasiswa.
- Menyediakan halaman tracer alumni.
- Menyediakan panel admin untuk pengelolaan konten.

Output dari tahap ini adalah daftar kebutuhan sistem yang jelas dan terstruktur sebagai dasar pengembangan pada tahap berikutnya.

### 2. Design

Tahap Design adalah tahap perancangan sistem berdasarkan kebutuhan yang telah dikumpulkan. Pada tahap ini, sistem mulai dirancang dari sisi struktur, tampilan, alur proses, dan penyimpanan data.

Aktivitas pada tahap ini meliputi:

- Merancang arsitektur sistem menggunakan pola MVC pada Laravel.
- Menentukan struktur database dan relasi antar tabel.
- Merancang alur navigasi halaman publik dan halaman admin.
- Membuat rancangan tampilan antarmuka pengguna.
- Menentukan route, controller, model, dan view yang dibutuhkan.
- Menyusun desain iterasi fitur yang akan dikembangkan per tahap.

Dalam proyek ini, perancangan dilakukan agar setiap modul dapat berdiri dengan jelas namun tetap saling terhubung. Misalnya, data pengumuman, kegiatan, galeri, dan kurikulum dirancang agar dapat dikelola dari admin panel dan ditampilkan di halaman publik.

Output dari tahap ini adalah rancangan sistem, rancangan basis data, rancangan antarmuka, dan rancangan alur kerja yang siap diimplementasikan.

### 3. Development

Tahap Development adalah proses implementasi rancangan menjadi kode program. Pada tahap ini, pengembangan dilakukan secara bertahap sesuai prioritas fitur yang telah ditentukan.

Aktivitas pada tahap ini meliputi:

- Membuat model, controller, migration, dan view.
- Membangun fitur halaman publik.
- Membangun fitur admin untuk pengelolaan konten.
- Mengintegrasikan database dengan tampilan dan logika aplikasi.
- Mengimplementasikan validasi form dan aturan bisnis.
- Menambahkan fitur pendukung seperti pencarian, filter, pagination, dan upload file.

Pada proyek Web Prodi TI, pengembangan dilakukan per modul agar setiap bagian bisa selesai, diuji, lalu langsung dimanfaatkan. Contohnya, modul pengumuman dapat diselesaikan terlebih dahulu sebelum modul lain seperti tracer alumni atau galeri.

Output dari tahap ini adalah fitur sistem yang telah dibangun dan siap diuji.

### 4. Testing

Tahap Testing bertujuan memastikan setiap fitur yang dibuat berjalan sesuai kebutuhan dan tidak menimbulkan kesalahan fungsi.

Aktivitas pada tahap ini meliputi:

- Pengujian fungsi halaman publik.
- Pengujian fitur admin panel.
- Pengujian validasi input form.
- Pengujian penyimpanan data ke database.
- Pengujian tampilan di berbagai ukuran layar.
- Pengujian hak akses dan keamanan sesi admin.

Pengujian dilakukan secara bertahap pada setiap iterasi. Jika ditemukan bug atau hasil yang belum sesuai, perbaikan dilakukan segera sebelum masuk ke tahap berikutnya.

Output dari tahap ini adalah sistem yang lebih stabil, lebih sesuai kebutuhan, dan layak untuk digunakan.

### 5. Deployment

Tahap Deployment adalah tahap penerapan sistem yang sudah selesai diuji ke lingkungan yang dapat digunakan oleh pengguna sebenarnya.

Aktivitas pada tahap ini meliputi:

- Menyiapkan server atau hosting.
- Melakukan konfigurasi environment aplikasi.
- Mengunggah source code dan database.
- Mengatur domain dan akses publik.
- Memastikan fitur utama dapat berjalan dengan baik pada server produksi.

Pada proyek ini, deployment penting karena website Prodi TI ditujukan sebagai sistem informasi yang dapat diakses oleh pengguna publik secara online. Setelah di-deploy, sistem dapat langsung digunakan untuk menampilkan informasi resmi program studi.

Output dari tahap ini adalah sistem yang telah aktif dan dapat diakses pengguna.

### 6. Review

Tahap Review adalah tahap evaluasi setelah sistem dijalankan atau setelah satu iterasi selesai. Pada tahap ini dilakukan penilaian terhadap hasil pengembangan untuk menentukan apakah ada fitur yang perlu diperbaiki, ditambah, atau disempurnakan.

Aktivitas pada tahap ini meliputi:

- Mengevaluasi hasil implementasi fitur.
- Mengumpulkan masukan dari pengguna atau pengelola website.
- Menilai apakah fitur sudah sesuai dengan kebutuhan awal.
- Mengidentifikasi kekurangan dan perbaikan yang dibutuhkan.
- Menentukan prioritas untuk iterasi berikutnya.

Tahap review sangat penting dalam pendekatan Agile karena memberikan ruang untuk penyempurnaan berkelanjutan. Dengan begitu, sistem tidak hanya selesai secara teknis, tetapi juga lebih relevan dengan kebutuhan pengguna.

Output dari tahap ini adalah catatan evaluasi dan daftar perbaikan untuk iterasi selanjutnya.

## Alur Iterative Incremental

Dalam pendekatan Iterative Incremental, keenam tahap di atas tidak hanya dilakukan satu kali, tetapi diulang dalam beberapa siklus sampai sistem dianggap cukup lengkap. Polanya dapat digambarkan sebagai berikut:

1. Menentukan kebutuhan fitur prioritas.
2. Mendesain fitur tersebut.
3. Mengembangkan fitur.
4. Menguji fitur.
5. Menerapkan fitur.
6. Mengevaluasi hasil dan melanjutkan ke fitur berikutnya.

Setiap iterasi menghasilkan tambahan fungsionalitas baru pada sistem. Dengan demikian, sistem berkembang secara bertahap dan terus meningkat kualitasnya.

## Kelebihan Metode Ini untuk Project Web Prodi TI

Penggunaan Agile Iterative Incremental pada project ini memiliki beberapa kelebihan:

- Lebih mudah menyesuaikan perubahan kebutuhan.
- Pengembangan fitur lebih terarah dan terprioritas.
- Risiko kesalahan lebih kecil karena diuji per tahap.
- Memudahkan komunikasi antara pengembang dan pengguna.
- Sistem dapat dirilis secara bertahap tanpa menunggu semua fitur selesai.

## Kesimpulan

Berdasarkan karakteristik project Web Prodi TI, metode **Agile dengan pendekatan Iterative Incremental** merupakan metode yang tepat digunakan. Metode ini memungkinkan pengembangan sistem dilakukan secara bertahap melalui enam tahap utama, yaitu Requirements, Design, Development, Testing, Deployment, dan Review. Dengan pendekatan ini, sistem dapat dibangun lebih fleksibel, terukur, dan sesuai dengan kebutuhan pengguna yang dapat berkembang dari waktu ke waktu.

## P

Untuk project Prodi TI ini, yang paling cocok adalah metode Agile dengan pendekatan Iterative-Incremental. Alasannya, sistemnya terdiri dari banyak modul yang saling terkait seperti publik, admin, konten dinamis, kalender akademik, galeri, pengumuman, kurikulum, tracer alumni, dan fitur seperti sinkronisasi data. Model seperti ini biasanya berkembang lewat feedback bertahap, jadi lebih aman kalau dibangun per iterasi daripada sekali jadi.
