import "./bootstrap";

const setupLanguageSwitcher = () => {
    const switchButtons = Array.from(
        document.querySelectorAll("[data-lang-switch]"),
    );

    if (switchButtons.length === 0) {
        return;
    }

    const textNodes = Array.from(document.querySelectorAll("[data-i18n]"));
    const ignoredSelector = "[data-i18n-ignore]";
    const dynamicContentSelector =
        "[data-i18n-dynamic], [data-no-auto-translate]";
    const autoTranslationEnabled =
        document.body?.hasAttribute("data-i18n-auto");

    const dictionary = {
        id: {
            "hero.cta.about": "Tentang Kami",
            "hero.cta.aspiration": "Sampaikan Aspirasi",
            "nav.home": "Home",
            "nav.profile_services": "Profil & Layanan",
            "nav.about": "Tentang Kami",
            "nav.lecturer_staff": "Dosen dan Staff",
            "nav.accreditation": "Akreditasi",
            "nav.contact": "Kontak",
            "nav.archive": "Arsip",
            "nav.announcements": "Pengumuman",
            "nav.academic": "Akademik",
            "nav.curriculum": "Kurikulum",
            "nav.learning_outcomes": "Capaian Pembelajaran",
            "nav.academic_calendar": "Kalender Akademi",
            "nav.tridharma": "Tri Dharma PT",
            "nav.research": "Penelitian",
            "nav.community_service": "Pengabdian",
            "nav.spmi": "SPMI",
            "nav.student_alumni": "Kemahasiswaan & Alumni",
            "nav.student_projects": "Project Mahasiswa",
            "nav.events": "Acara",
            "nav.gallery": "Galeri",
            "nav.tracer_alumni": "Tracer Alumni",
            "nav.aspiration": "Aspirasi",
            "nav.login": "Login",
            "nav.dashboard": "Dashboard",
            "section.about.title": "Tentang Kami",
            "section.about.subtitle":
                "Profil singkat Program Studi Teknologi Informasi",
            "about.heading": "Program Studi Teknologi Informasi",
            "about.description_primary":
                "Prodi Teknologi Informasi Politeknik Gajah Tunggal hadir sebagai ruang pengembangan talenta digital yang adaptif, kreatif, dan relevan dengan kebutuhan industri.",
            "about.description_secondary":
                "Kami berfokus pada pembelajaran terapan, proyek inovatif, kolaborasi riset, serta penguatan soft skill agar mahasiswa siap berkontribusi di era transformasi digital.",
            "about.profile_title": "Profil Program Studi",
            "about.profile_structure": "Struktur Prodi",
            "about.profile_lecturers": "Profil Dosen/Staff",
            "about.profile_students": "Profil Mahasiswa",
            "about.explore_more": "Explore More",
            "section.vision.title": "Visi dan Misi",
            "section.vision.subtitle":
                "Arah strategis pengembangan Program Studi Teknologi Informasi",
            "section.activities.title": "Kegiatan Kami",
            "section.activities.subtitle":
                "Berbagai kegiatan yang telah dan akan kami laksanakan",
            "section.activities.view_all": "Lihat Semua Kegiatan",
            "section.gallery.title": "Galeri Kegiatan",
            "section.gallery.subtitle":
                "Dokumentasi kegiatan dan momen penting kami",
            "section.gallery.view_all": "Lihat Semua Galeri",
            "section.project.title": "Project Mahasiswa",
            "section.project.subtitle":
                "Dokumentasi karya mahasiswa Prodi Teknologi Informasi",
            "project.share.title": "Bagikan Project",
            "project.share.whatsapp": "WhatsApp",
            "project.share.x": "X",
            "project.share.linkedin": "LinkedIn",
            "project.share.copy": "Salin Link",
            "project.share.copied": "Tautan berhasil disalin.",
            "section.calendar.title": "Kalender Kegiatan Prodi IT",
            "section.calendar.subtitle":
                "Agenda resmi akademik dan kalender ekonomi (Google Calendar)",
            "section.aspiration.title": "Sampaikan Aspirasi Anda",
            "section.aspiration.subtitle":
                "Kami mendengarkan setiap suara dan aspirasi dari mahasiswa",
            "section.accreditation.title": "Akreditasi Prodi TI",
            "section.accreditation.subtitle":
                "Informasi artikel pengembangan prodi dan dokumen sertifikasi",
            "section.tracer.title": "Tracer Alumni",
            "section.tracer.subtitle":
                "Data ringkas alumni berdasarkan hasil tracer alumni",
            "section.tracer.table.no": "No",
            "section.tracer.table.nim": "NIM",
            "section.tracer.table.company": "Perusahaan",
            "section.tracer.table.level": "Tingkat/Ukuran",
            "section.tracer.table.department": "Departemen",
            "section.tracer.table.relevance": "Kesesuaian",
            "section.tracer.empty": "Data tracer alumni belum tersedia.",
            "section.archive.title": "Arsip Informasi",
            "section.archive.subtitle":
                "Riwayat pengumuman dan dokumentasi informasi Prodi TI",
        },
        en: {
            "hero.cta.about": "About Us",
            "hero.cta.aspiration": "Submit Aspiration",
            "nav.home": "Home",
            "nav.profile_services": "Profile & Services",
            "nav.about": "About Us",
            "nav.lecturer_staff": "Lecturers & Staff",
            "nav.accreditation": "Accreditation",
            "nav.contact": "Contact",
            "nav.archive": "Archive",
            "nav.announcements": "Announcements",
            "nav.academic": "Academic",
            "nav.curriculum": "Curriculum",
            "nav.learning_outcomes": "Learning Outcomes",
            "nav.academic_calendar": "Academic Calendar",
            "nav.tridharma": "Tri Dharma",
            "nav.research": "Research",
            "nav.community_service": "Community Service",
            "nav.spmi": "SPMI",
            "nav.student_alumni": "Students & Alumni",
            "nav.student_projects": "Student Projects",
            "nav.events": "Events",
            "nav.gallery": "Gallery",
            "nav.tracer_alumni": "Tracer Alumni",
            "nav.aspiration": "Aspiration",
            "nav.login": "Login",
            "nav.dashboard": "Dashboard",
            "section.about.title": "About Us",
            "section.about.subtitle":
                "Brief profile of the Information Technology Study Program",
            "about.heading": "Information Technology Study Program",
            "about.description_primary":
                "The Information Technology Study Program at Politeknik Gajah Tunggal serves as a space to develop adaptive, creative, and industry-relevant digital talent.",
            "about.description_secondary":
                "We focus on applied learning, innovative projects, research collaboration, and soft-skill strengthening so students are ready to contribute in the digital transformation era.",
            "about.profile_title": "Study Program Profile",
            "about.profile_structure": "Program Structure",
            "about.profile_lecturers": "Lecturers/Staff Profile",
            "about.profile_students": "Student Profile",
            "about.explore_more": "Explore More",
            "section.vision.title": "Vision and Mission",
            "section.vision.subtitle":
                "Strategic direction for developing the Information Technology Study Program",
            "section.activities.title": "Our Activities",
            "section.activities.subtitle":
                "Activities we have carried out and will organize",
            "section.activities.view_all": "View All Activities",
            "section.gallery.title": "Activity Gallery",
            "section.gallery.subtitle":
                "Documentation of our activities and important moments",
            "section.gallery.view_all": "View All Galleries",
            "section.project.title": "Student Projects",
            "section.project.subtitle":
                "Documentation of student works in the IT Study Program",
            "project.share.title": "Share Project",
            "project.share.whatsapp": "WhatsApp",
            "project.share.x": "X",
            "project.share.linkedin": "LinkedIn",
            "project.share.copy": "Copy Link",
            "project.share.copied": "Link copied successfully.",
            "section.calendar.title": "IT Program Activity Calendar",
            "section.calendar.subtitle":
                "Official academic agenda and economic calendar (Google Calendar)",
            "section.aspiration.title": "Submit Your Aspiration",
            "section.aspiration.subtitle":
                "We listen to every student voice and aspiration",
            "section.accreditation.title": "IT Program Accreditation",
            "section.accreditation.subtitle":
                "Information on program development articles and certification documents",
            "section.tracer.title": "Alumni Tracer",
            "section.tracer.subtitle":
                "Summary of alumni data based on alumni tracer results",
            "section.tracer.table.no": "No",
            "section.tracer.table.nim": "Student ID",
            "section.tracer.table.company": "Company",
            "section.tracer.table.level": "Level/Scale",
            "section.tracer.table.department": "Department",
            "section.tracer.table.relevance": "Relevance",
            "section.tracer.empty": "No alumni tracer data is available yet.",
            "section.archive.title": "Information Archive",
            "section.archive.subtitle":
                "History of announcements and documentation of IT Program information",
        },
    };

    const freeTextDictionary = {
        en: {
            "Program Studi Teknologi Informasi":
                "Information Technology Study Program",
            "Program Studi Teknologi Informasi berkomitmen mencetak talenta digital yang unggul, adaptif, dan siap berkontribusi pada transformasi teknologi di industri.":
                "The Information Technology Study Program is committed to shaping outstanding digital talent that is adaptive and ready to contribute to technological transformation in industry.",
            "Tautan Cepat": "Quick Links",
            Beranda: "Home",
            "Kontak Kami": "Contact Us",
            "Ikuti Kami": "Follow Us",
            "Telepon: +62 899-9999-9999": "Phone: +62 899-9999-9999",
            "Sinari asa Wujudkan Cita": "Ignite Hope, Realize Dreams",
            Visi: "Vision",
            Misi: "Mission",
            "Nama Lengkap": "Full Name",
            "NIM (Opsional)": "NIM (Optional)",
            Subjek: "Subject",
            "Pesan Aspirasi": "Aspiration Message",
            "Kirim Aspirasi": "Send Aspiration",
            "Sertifikat Akreditasi Prodi TI":
                "IT Study Program Accreditation Certificate",
            "Dokumen akreditasi resmi prodi yang dapat diakses civitas akademika.":
                "Official study program accreditation documents accessible to the academic community.",
            "Sertifikat Akreditasi Institusi":
                "Institutional Accreditation Certificate",
            "Dukungan akreditasi institusi untuk menjamin mutu layanan pendidikan.":
                "Institutional accreditation support to ensure educational service quality.",
            "Status Akreditasi": "Accreditation Status",
            "Dokumen detail dan lampiran sertifikat akan diperbarui secara berkala pada section ini.":
                "Detailed documents and certificate attachments will be updated periodically in this section.",
            Tutup: "Close",
            "Kegiatan Prodi": "Program Activities",
            "Berbagai kegiatan dan program yang telah maupun akan dilaksanakan oleh Program Studi Teknologi Informasi":
                "Various activities and programs that have been and will be carried out by the Information Technology Study Program.",
            "Daftar Kegiatan": "Activity List",
            "Pilih kegiatan untuk melihat informasi lengkap, dokumentasi visual, serta rincian lokasi dan tanggal pelaksanaan.":
                "Choose an activity to view complete information, visual documentation, and detailed location and schedule.",
            "Lihat Detail": "View Details",
            "Belum ada kegiatan yang dipublikasikan.":
                "No published activities yet.",
            "Deskripsi kegiatan belum tersedia.":
                "Activity description is not available yet.",
            "Detail Kegiatan": "Activity Detail",
            "Informasi lengkap kegiatan Program Studi Teknologi Informasi":
                "Complete information about Information Technology Study Program activities.",
            "Kembali ke Daftar Kegiatan": "Back to Activity List",
            "Deskripsi Kegiatan": "Activity Description",
            "Deskripsi kegiatan belum ditambahkan.":
                "Activity description has not been added yet.",
            Informasi: "Information",
            "Dokumentasi ini merupakan informasi resmi dari Program Studi Teknologi Informasi. Untuk pertanyaan lanjutan, silakan hubungi kontak prodi pada footer website.":
                "This documentation is official information from the Information Technology Study Program. For further questions, please contact the program contact listed in the website footer.",
            "Kegiatan Lainnya": "Other Activities",
            Pengumuman: "Announcements",
            "Informasi resmi, agenda terbaru, dan pembaruan penting untuk civitas Program Studi Teknologi Informasi":
                "Official information, latest agenda, and important updates for the Information Technology Study Program community.",
            "Pusat Informasi Prodi": "Program Information Center",
            "Pantau pengumuman terbaru melalui kartu berjalan, lalu buka detail lengkap langsung dari tabel informasi.":
                "Monitor the latest announcements through the running cards, then open full details directly from the information table.",
            "Judul Pengumuman": "Announcement Title",
            Status: "Status",
            Publish: "Publish",
            Aksi: "Action",
            "Belum ada data pengumuman.": "No announcement data yet.",
            "Kurikulum dan Mata Kuliah": "Curriculum and Courses",
            "Informasi kurikulum aktif dan detail mata kuliah Program Studi Teknologi Informasi":
                "Information about active curriculum and course details of the Information Technology Study Program.",
            "Peta Kurikulum Per Tahun Akademik":
                "Curriculum Map by Academic Year",
            "Gunakan filter kurikulum untuk melihat struktur mata kuliah, beban SKS, serta silabus singkat per semester secara ringkas.":
                "Use the curriculum filter to view course structure, credit load, and concise semester syllabus.",
            Semester: "Semester",
            Kode: "Code",
            "Mata Kuliah": "Course",
            SKS: "Credits",
            "Silabus Singkat": "Short Syllabus",
            "Mata kuliah belum tersedia untuk kurikulum ini.":
                "Courses are not yet available for this curriculum.",
            "Informasi Kurikulum Belum anda Pilih":
                "Curriculum information has not been selected yet",
            "Project Mahasiswa": "Student Projects",
            "Kumpulan karya mahasiswa dengan prioritas unggulan dan dokumentasi project reguler":
                "A collection of student works with featured priorities and regular project documentation.",
            "Eksplorasi Karya Mahasiswa": "Explore Student Works",
            "Klik setiap kartu project untuk membuka halaman detail lengkap pada halaman baru.":
                "Click each project card to open its complete detail page in a new view.",
            "9 Project Pilihan": "9 Featured Projects",
            "Belum ada project unggulan.": "No featured projects yet.",
            "Project Reguler": "Regular Projects",
            "Belum ada project reguler.": "No regular projects yet.",
            "Detail Project Mahasiswa": "Student Project Detail",
            "Informasi lengkap karya mahasiswa dari judul, deskripsi, hingga tautan demo dan repository":
                "Complete student project information from title and description to demo and repository links.",
            "Kembali ke daftar project": "Back to project list",
            "Deskripsi project belum tersedia.":
                "Project description is not available yet.",
            Slug: "Slug",
            "Nama Mahasiswa": "Student Name",
            Tahun: "Year",
            "Tanggal Publish": "Publish Date",
            "Demo URL": "Demo URL",
            "Repository URL": "Repository URL",
            "Project Lainnya": "Other Projects",
            "Tracer Alumni": "Alumni Tracer",
            "Mengenal profil lulusan dan keterkaitan bidang kerja alumni Prodi Teknologi Informasi":
                "Learn about graduate profiles and the relevance of alumni work fields in the Information Technology Study Program.",
            "Jejak Karier Lulusan": "Graduate Career Track",
            "Filter tahun lulusan untuk melihat ringkasan penempatan alumni di berbagai perusahaan dan kesesuaian bidang kerja.":
                "Filter graduation year to view alumni placement summaries across companies and work-field relevance.",
            "Semua Lulusan": "All Graduates",
            "Sembunyikan Lulusan": "Hide Graduates",
            "Lihat Lulusan": "Show Graduates",
            "Data tracer untuk lulusan": "Tracer data for graduates",
            "menampilkan profil penempatan kerja alumni pada berbagai sektor industri.":
                "shows alumni employment profile placements across various industry sectors.",
            "Menampilkan seluruh data tracer alumni aktif. Gunakan filter tahun untuk menyaring data lulusan tertentu.":
                "Showing all active alumni tracer data. Use the year filter to narrow down specific graduate data.",
            Lulusan: "Graduates",
            Perusahaan: "Company",
            "Tingkat/Ukuran": "Level/Scale",
            Departemen: "Department",
            Kesesuaian: "Relevance",
            "Data tracer alumni belum tersedia.":
                "Alumni tracer data is not available yet.",
            "Dosen dan Staff": "Lecturers and Staff",
            "Mengenal lebih dalam tim pengajar dan tenaga pendukung Program Studi Teknologi Informasi":
                "Get to know the teaching team and support staff of the Information Technology Study Program.",
            "Tim Pengajar dan Pendukung Akademik":
                "Teaching Team and Academic Support",
            "Telusuri profil dosen dan staff berdasarkan nama, jabatan, ataupun kategori peran untuk menemukan narahubung akademik dengan lebih cepat.":
                "Browse lecturer and staff profiles by name, position, or role category to find academic contacts faster.",
            "Cari nama, jabatan, atau email...":
                "Search by name, position, or email...",
            "Semua Tipe": "All Types",
            Cari: "Search",
            "Informasi profil akan diperbarui secara berkala.":
                "Profile information will be updated periodically.",
            "Lihat Blog Kegiatan": "View Activity Blog",
            "Data dosen dan staff belum tersedia.":
                "Lecturer and staff data is not available yet.",
            "Blog Dosen & Staff": "Lecturer & Staff Blog",
            "Dokumentasi kegiatan mengajar, pembinaan, dan aktivitas akademik harian":
                "Documentation of teaching, mentoring, and daily academic activities.",
            "Kembali ke daftar dosen/staff": "Back to lecturer/staff list",
            Tanggal: "Date",
            Lokasi: "Location",
            "Belum ada blog kegiatan untuk dosen/staff ini.":
                "No activity blog entries for this lecturer/staff yet.",
            "Kalender Akademik": "Academic Calendar",
            "Agenda akademik bulanan dengan gaya manajemen event seperti Google Calendar.":
                "Monthly academic agenda with event management style like Google Calendar.",
            Terapkan: "Apply",
            "Bulan Sebelumnya": "Previous Month",
            "Bulan Berikutnya": "Next Month",
            "Lokasi:": "Location:",
            "Buka di Google Calendar": "Open in Google Calendar",
            "Tidak ada agenda akademik untuk bulan ini.":
                "No academic agenda for this month.",
            "Kembali ke atas": "Back to top",
        },
    };

    const freeTextPatterns = {
        en: [
            {
                regex: /^Total Kegiatan:\s*(.+)$/i,
                replace: "Total Activities: $1",
            },
            {
                regex: /^Halaman:\s*(.+)$/i,
                replace: "Page: $1",
            },
            {
                regex: /^Unggulan:\s*(.+)$/i,
                replace: "Featured: $1",
            },
            {
                regex: /^Reguler:\s*(.+)$/i,
                replace: "Regular: $1",
            },
            {
                regex: /^Total Pengumuman:\s*(.+)$/i,
                replace: "Total Announcements: $1",
            },
            {
                regex: /^Tahun Akademik:\s*(.+)$/i,
                replace: "Academic Year: $1",
            },
            {
                regex: /^Total Mata Kuliah:\s*(.+)$/i,
                replace: "Total Courses: $1",
            },
            {
                regex: /^Tahun Dipilih:\s*(.+)$/i,
                replace: "Selected Year: $1",
            },
            {
                regex: /^Total Data:\s*(.+)$/i,
                replace: "Total Records: $1",
            },
            {
                regex: /^Lulusan\s+(\d{4})$/i,
                replace: "Graduates $1",
            },
            {
                regex: /^Tracer Alumni\s+(.+)$/i,
                replace: "Alumni Tracer $1",
            },
            {
                regex: /^Total Personel:\s*(.+)$/i,
                replace: "Total Personnel: $1",
            },
            {
                regex: /^Kategori:\s*(.+)$/i,
                replace: "Category: $1",
            },
            {
                regex: /^Pencarian:\s*(.+)$/i,
                replace: "Search: $1",
            },
            {
                regex: /^Total Blog:\s*(.+)$/i,
                replace: "Total Blogs: $1",
            },
            {
                regex: /^Email:\s*(.+)$/i,
                replace: "Email: $1",
            },
        ],
    };

    const normalizeSpaces = (value) => value.replace(/\s+/g, " ").trim();

    const preserveOuterWhitespace = (source, replacement) => {
        const leading = source.match(/^\s*/)?.[0] || "";
        const trailing = source.match(/\s*$/)?.[0] || "";
        return `${leading}${replacement}${trailing}`;
    };

    const normalizedPhraseDictionary = {
        en: Object.fromEntries(
            Object.entries(freeTextDictionary.en).map(([source, target]) => [
                normalizeSpaces(source),
                target,
            ]),
        ),
    };

    const translateFreeText = (sourceValue, lang) => {
        if (lang !== "en") {
            return sourceValue;
        }

        const trimmed = sourceValue.trim();
        if (trimmed === "") {
            return sourceValue;
        }

        const normalized = normalizeSpaces(trimmed);
        const exactMatch = normalizedPhraseDictionary.en[normalized];
        if (exactMatch) {
            return preserveOuterWhitespace(sourceValue, exactMatch);
        }

        for (const pattern of freeTextPatterns.en) {
            if (pattern.regex.test(trimmed)) {
                return preserveOuterWhitespace(
                    sourceValue,
                    trimmed.replace(pattern.regex, pattern.replace),
                );
            }
        }

        return sourceValue;
    };

    const autoTextNodes = [];
    const autoTextSnapshot = new WeakMap();
    const autoAttributeEntries = [];

    const isSkippedTextNode = (node) => {
        const parent = node.parentElement;

        if (!parent) {
            return true;
        }

        if (
            parent.closest(ignoredSelector) ||
            parent.closest("[data-i18n]") ||
            parent.closest(dynamicContentSelector)
        ) {
            return true;
        }

        const blockedTags = ["SCRIPT", "STYLE", "NOSCRIPT", "TEXTAREA"];
        if (blockedTags.includes(parent.tagName)) {
            return true;
        }

        return false;
    };

    const collectAutoTextNodes = () => {
        const roots = Array.from(
            document.querySelectorAll("main, footer, #scroll-to-top"),
        );

        roots.forEach((root) => {
            const walker = document.createTreeWalker(
                root,
                NodeFilter.SHOW_TEXT,
                {
                    acceptNode: (node) => {
                        if ((node.nodeValue || "").trim() === "") {
                            return NodeFilter.FILTER_REJECT;
                        }

                        return isSkippedTextNode(node)
                            ? NodeFilter.FILTER_REJECT
                            : NodeFilter.FILTER_ACCEPT;
                    },
                },
            );

            let current = walker.nextNode();
            while (current) {
                autoTextNodes.push(current);
                autoTextSnapshot.set(current, current.nodeValue || "");
                current = walker.nextNode();
            }
        });
    };

    const trackAttributesForElement = (element) => {
        if (!(element instanceof HTMLElement)) {
            return;
        }

        if (
            element.closest(ignoredSelector) ||
            element.closest(dynamicContentSelector)
        ) {
            return;
        }

        ["placeholder", "aria-label", "title"].forEach((attribute) => {
            if (!element.hasAttribute(attribute)) {
                return;
            }

            autoAttributeEntries.push({
                element,
                attribute,
                originalValue: element.getAttribute(attribute) || "",
            });
        });
    };

    const collectAutoAttributes = () => {
        const roots = Array.from(
            document.querySelectorAll("main, footer, #scroll-to-top"),
        );

        roots.forEach((root) => {
            trackAttributesForElement(root);

            root.querySelectorAll(
                "[placeholder], [aria-label], [title]",
            ).forEach((element) => {
                trackAttributesForElement(element);
            });
        });
    };

    const applyAutoTranslation = (lang) => {
        autoTextNodes.forEach((node) => {
            const originalValue = autoTextSnapshot.get(node);
            if (typeof originalValue !== "string") {
                return;
            }

            node.nodeValue =
                lang === "id"
                    ? originalValue
                    : translateFreeText(originalValue, lang);
        });

        autoAttributeEntries.forEach((entry) => {
            const value =
                lang === "id"
                    ? entry.originalValue
                    : translateFreeText(entry.originalValue, lang);

            entry.element.setAttribute(entry.attribute, value);
        });
    };

    if (autoTranslationEnabled) {
        collectAutoTextNodes();
        collectAutoAttributes();
    }

    const applyLanguage = (lang) => {
        const fallback = dictionary.id;
        const selectedLang = dictionary[lang] ? lang : "id";
        const selected = dictionary[selectedLang] || fallback;

        textNodes.forEach((element) => {
            const key = element.getAttribute("data-i18n");
            if (!key) {
                return;
            }

            element.textContent =
                selected[key] || fallback[key] || element.textContent;
        });

        if (autoTranslationEnabled) {
            applyAutoTranslation(selectedLang);
        }

        switchButtons.forEach((button) => {
            button.classList.toggle(
                "is-active",
                button.getAttribute("data-lang-switch") === selectedLang,
            );
        });

        document.documentElement.setAttribute(
            "lang",
            selectedLang === "en" ? "en" : "id",
        );

        localStorage.setItem("site-lang", selectedLang);
        document.dispatchEvent(
            new CustomEvent("site-language-changed", {
                detail: { lang: selectedLang },
            }),
        );
    };

    const htmlLang = (document.documentElement.getAttribute("lang") || "")
        .toLowerCase()
        .startsWith("en")
        ? "en"
        : "id";
    applyLanguage(htmlLang);

    switchButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const selectedLang =
                button.getAttribute("data-lang-switch") || "id";
            applyLanguage(selectedLang);
        });
    });
};

const setupThemeToggle = () => {
    const root = document.documentElement;
    const button = document.getElementById("theme-toggle");
    const icon = document.getElementById("theme-icon");

    if (!button || !icon) {
        return;
    }

    const applyIcon = () => {
        const isDark = root.classList.contains("theme-dark");
        icon.className = isDark ? "fa-solid fa-sun" : "fa-regular fa-moon";
    };

    applyIcon();

    button.addEventListener("click", () => {
        button.classList.add("is-animating");
        root.classList.toggle("theme-dark");
        const theme = root.classList.contains("theme-dark") ? "dark" : "light";
        localStorage.setItem("site-theme", theme);
        applyIcon();
        window.setTimeout(() => {
            button.classList.remove("is-animating");
        }, 350);
    });
};

const setupAdaptiveHeader = () => {
    const header = document.getElementById("site-header");

    if (!header) {
        return;
    }

    const applyState = () => {
        header.setAttribute(
            "data-nav-state",
            window.scrollY > 10 ? "scrolled" : "top",
        );
    };

    applyState();
    window.addEventListener("scroll", applyState, { passive: true });
    window.addEventListener("resize", applyState);
};

const setupNavbarInteractions = () => {
    const desktopDropdowns = Array.from(
        document.querySelectorAll("[data-nav-dropdown]"),
    );
    const mobileToggle = document.getElementById("mobile-nav-toggle");
    const mobileIcon = document.getElementById("mobile-nav-icon");
    const mobileWrap = document.getElementById("mobile-nav-wrap");
    const mobileDropdowns = Array.from(
        document.querySelectorAll(".mobile-dropdown"),
    );
    const desktopCanHover =
        window.matchMedia && window.matchMedia("(hover: hover)").matches;
    const dropdownCloseTimers = new WeakMap();

    const clearDesktopCloseTimer = (dropdown) => {
        const timer = dropdownCloseTimers.get(dropdown);
        if (timer) {
            window.clearTimeout(timer);
            dropdownCloseTimers.delete(dropdown);
        }
    };

    const scheduleDesktopClose = (dropdown, delay = 90) => {
        clearDesktopCloseTimer(dropdown);
        const timer = window.setTimeout(() => {
            closeDesktopDropdown(dropdown);
            dropdownCloseTimers.delete(dropdown);
        }, delay);
        dropdownCloseTimers.set(dropdown, timer);
    };

    const closeDesktopDropdown = (dropdown) => {
        const trigger = dropdown.querySelector("[data-nav-trigger]");
        clearDesktopCloseTimer(dropdown);
        dropdown.classList.remove("is-open");
        if (trigger) {
            trigger.setAttribute("aria-expanded", "false");
        }
    };

    const openDesktopDropdown = (dropdown) => {
        const trigger = dropdown.querySelector("[data-nav-trigger]");
        clearDesktopCloseTimer(dropdown);
        closeAllDesktopDropdowns(dropdown);
        dropdown.classList.add("is-open");
        if (trigger) {
            trigger.setAttribute("aria-expanded", "true");
        }
    };

    const closeAllDesktopDropdowns = (except = null) => {
        desktopDropdowns.forEach((dropdown) => {
            if (except && dropdown === except) {
                return;
            }
            closeDesktopDropdown(dropdown);
        });
    };

    desktopDropdowns.forEach((dropdown) => {
        const trigger = dropdown.querySelector("[data-nav-trigger]");
        const menu = dropdown.querySelector("[data-nav-menu]");

        if (!trigger || !menu) {
            return;
        }

        trigger.addEventListener("click", (event) => {
            event.preventDefault();
            const isOpen = dropdown.classList.contains("is-open");
            clearDesktopCloseTimer(dropdown);

            if (isOpen) {
                closeDesktopDropdown(dropdown);
                return;
            }

            openDesktopDropdown(dropdown);
        });

        dropdown.addEventListener("mouseenter", () => {
            if (!desktopCanHover) {
                return;
            }

            openDesktopDropdown(dropdown);
        });

        dropdown.addEventListener("mouseleave", () => {
            if (!desktopCanHover) {
                return;
            }

            scheduleDesktopClose(dropdown, 90);
        });

        dropdown.addEventListener("focusin", () => {
            openDesktopDropdown(dropdown);
        });

        dropdown.addEventListener("focusout", (event) => {
            const relatedTarget = event.relatedTarget;
            if (relatedTarget && dropdown.contains(relatedTarget)) {
                return;
            }

            scheduleDesktopClose(dropdown, 60);
        });

        dropdown.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                closeDesktopDropdown(dropdown);
                trigger.focus();
            }
        });
    });

    document.addEventListener("click", (event) => {
        const clickedInside = desktopDropdowns.some((dropdown) =>
            dropdown.contains(event.target),
        );

        if (!clickedInside) {
            closeAllDesktopDropdowns();
        }
    });

    const setMobileIconState = (isOpen, immediate = false) => {
        if (!mobileIcon) {
            return;
        }

        const applyFinalState = () => {
            mobileIcon.classList.toggle("fa-bars", !isOpen);
            mobileIcon.classList.toggle("fa-xmark", isOpen);
            mobileIcon.classList.remove("is-swapping");
        };

        if (immediate) {
            applyFinalState();
            return;
        }

        mobileIcon.classList.add("is-swapping");
        window.setTimeout(applyFinalState, 90);
    };

    const closeMobileMenu = () => {
        if (!mobileWrap || !mobileToggle) {
            return;
        }

        mobileWrap.classList.remove("is-open");
        mobileWrap.setAttribute("data-mobile-nav-state", "closed");
        mobileToggle.classList.remove("is-open");
        mobileToggle.setAttribute("aria-expanded", "false");
        setMobileIconState(false, true);
    };

    if (mobileToggle && mobileWrap) {
        mobileWrap.classList.add("is-ready");
        closeMobileMenu();

        mobileToggle.addEventListener("click", () => {
            const shouldOpen = !mobileWrap.classList.contains("is-open");
            mobileWrap.classList.toggle("is-open", shouldOpen);
            mobileWrap.setAttribute(
                "data-mobile-nav-state",
                shouldOpen ? "open" : "closed",
            );
            mobileToggle.classList.toggle("is-open", shouldOpen);
            mobileToggle.setAttribute(
                "aria-expanded",
                shouldOpen ? "true" : "false",
            );
            setMobileIconState(shouldOpen);
        });

        mobileWrap.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", closeMobileMenu);
        });

        window.addEventListener("resize", () => {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });
    }

    mobileDropdowns.forEach((dropdown) => {
        const summary = dropdown.querySelector("summary");
        if (summary) {
            summary.setAttribute(
                "aria-expanded",
                dropdown.open ? "true" : "false",
            );
        }

        dropdown.addEventListener("toggle", () => {
            if (dropdown.open) {
                mobileDropdowns.forEach((otherDropdown) => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.open = false;
                    }
                });
            }

            if (summary) {
                summary.setAttribute(
                    "aria-expanded",
                    dropdown.open ? "true" : "false",
                );
            }
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") {
            return;
        }

        closeAllDesktopDropdowns();
        closeMobileMenu();
        mobileDropdowns.forEach((dropdown) => {
            dropdown.open = false;
        });
    });
};

const setupNavbarHoverIndicator = () => {
    const nav = document.querySelector(".main-nav");
    if (!nav) {
        return;
    }

    const indicator = nav.querySelector("[data-nav-indicator]");
    const items = Array.from(nav.querySelectorAll(".menu-link.nav-link"));

    if (!indicator || items.length === 0) {
        return;
    }

    const isDesktopVisible = () =>
        window.getComputedStyle(nav).display !== "none";

    const moveIndicatorTo = (target) => {
        if (!target || !isDesktopVisible()) {
            indicator.classList.remove("is-visible");
            return;
        }

        const navRect = nav.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const left = Math.round(targetRect.left - navRect.left);
        const width = Math.round(targetRect.width);

        indicator.style.width = `${width}px`;
        indicator.style.transform = `translate3d(${left}px, 0, 0)`;
        indicator.classList.add("is-visible");
    };

    const setActiveItem = (target) => {
        items.forEach((item) => item.classList.remove("is-nav-active"));
        if (target) {
            target.classList.add("is-nav-active");
        }
    };

    const normalizePath = (value) => {
        const path = value.replace(/\/+$/, "");
        return path === "" ? "/" : path;
    };

    const getPathnameFromHref = (href) => {
        try {
            return normalizePath(
                new URL(href, window.location.origin).pathname,
            );
        } catch {
            return null;
        }
    };

    const currentPath = normalizePath(window.location.pathname);

    const pickInitialItem = () => {
        const directMatch = items.find((item) => {
            if (!(item instanceof HTMLAnchorElement)) {
                return false;
            }

            const href = item.getAttribute("href") || "";
            const itemPath = getPathnameFromHref(href);
            if (!itemPath || itemPath !== currentPath) {
                return false;
            }

            const itemHash = item.hash || "";
            return !itemHash || itemHash === window.location.hash;
        });

        if (directMatch) {
            return directMatch;
        }

        const dropdowns = Array.from(
            nav.querySelectorAll("[data-nav-dropdown]"),
        );
        for (const dropdown of dropdowns) {
            const trigger = dropdown.querySelector("[data-nav-trigger]");
            const links = Array.from(
                dropdown.querySelectorAll(".dropdown-item[href]"),
            );

            const hasPathMatch = links.some((link) => {
                const href = link.getAttribute("href") || "";
                const linkPath = getPathnameFromHref(href);
                return linkPath === currentPath;
            });

            if (hasPathMatch && trigger) {
                return trigger;
            }
        }

        return items[0] || null;
    };

    let activeItem = pickInitialItem();
    setActiveItem(activeItem);
    moveIndicatorTo(activeItem);

    items.forEach((item) => {
        item.addEventListener("mouseenter", () => {
            moveIndicatorTo(item);
        });

        item.addEventListener("focus", () => {
            moveIndicatorTo(item);
        });

        item.addEventListener("click", () => {
            activeItem = item;
            setActiveItem(activeItem);
            moveIndicatorTo(activeItem);
        });
    });

    nav.addEventListener("mouseleave", () => {
        moveIndicatorTo(activeItem);
    });

    nav.addEventListener("focusout", (event) => {
        const relatedTarget = event.relatedTarget;
        if (relatedTarget && nav.contains(relatedTarget)) {
            return;
        }
        moveIndicatorTo(activeItem);
    });

    window.addEventListener("resize", () => {
        if (!isDesktopVisible()) {
            indicator.classList.remove("is-visible");
            return;
        }

        moveIndicatorTo(activeItem);
    });
};

const setupAnnouncementRealtimeSync = () => {
    const syncNode = document.querySelector(
        "[data-announcement-sync-url][data-announcement-signature]",
    );

    if (!syncNode) {
        return;
    }

    const endpoint = syncNode.getAttribute("data-announcement-sync-url") || "";
    let currentSignature =
        syncNode.getAttribute("data-announcement-signature") || "";
    const interval = Math.max(
        Number(
            syncNode.getAttribute("data-announcement-sync-interval") || 15000,
        ),
        5000,
    );

    if (!endpoint || !currentSignature) {
        return;
    }

    let requestInFlight = false;

    const checkUpdate = async () => {
        if (requestInFlight || document.hidden) {
            return;
        }

        requestInFlight = true;

        try {
            const response = await fetch(endpoint, {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                cache: "no-store",
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const nextSignature =
                typeof payload?.signature === "string" ? payload.signature : "";

            if (!nextSignature) {
                return;
            }

            if (nextSignature !== currentSignature) {
                window.location.reload();
                return;
            }

            currentSignature = nextSignature;
        } catch {
            // noop: keep polling in next interval
        } finally {
            requestInFlight = false;
        }
    };

    window.setTimeout(checkUpdate, 2200);
    window.setInterval(checkUpdate, interval);

    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) {
            checkUpdate();
        }
    });
};

const setupPreloader = () => {
    const preloader = document.getElementById("site-preloader");
    const progressFill = document.getElementById("preloader-progress-fill");

    if (!preloader || !progressFill) {
        return;
    }

    let progress = 10;
    let finished = false;

    const timer = window.setInterval(() => {
        if (finished) {
            return;
        }

        progress = Math.min(92, progress + Math.random() * 12);
        progressFill.style.width = `${progress}%`;
    }, 120);

    const hidePreloader = () => {
        if (finished) {
            return;
        }

        finished = true;
        window.clearInterval(timer);
        progressFill.style.width = "100%";

        window.setTimeout(() => {
            preloader.classList.add("is-hidden");
        }, 220);

        window.setTimeout(() => {
            preloader.remove();
        }, 860);
    };

    window.addEventListener("load", hidePreloader, { once: true });
    window.setTimeout(hidePreloader, 2400);
};

const setupPasswordToggle = () => {
    const toggleButton = document.getElementById("password-visibility-toggle");
    const passwordInput = document.getElementById("password");
    const eyeOpen = document.getElementById("password-eye-open");
    const eyeOff = document.getElementById("password-eye-off");

    if (!toggleButton || !passwordInput || !eyeOpen || !eyeOff) {
        return;
    }

    toggleButton.addEventListener("click", () => {
        const shouldShow = passwordInput.getAttribute("type") === "password";
        passwordInput.setAttribute("type", shouldShow ? "text" : "password");
        eyeOpen.classList.toggle("hidden", shouldShow);
        eyeOff.classList.toggle("hidden", !shouldShow);
    });
};

const setupScrollTopButton = () => {
    const button = document.getElementById("scroll-to-top");
    if (!button) {
        return;
    }

    const updateVisibility = () => {
        button.classList.toggle("is-visible", window.scrollY > 300);
    };

    button.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    updateVisibility();
    window.addEventListener("scroll", updateVisibility, { passive: true });
};

const setupTypewriter = () => {
    const target = document.getElementById("typewriter-text");
    if (!target) {
        return;
    }

    const parseWords = (attributeName) => {
        const raw = target.getAttribute(attributeName) || "[]";

        try {
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) {
                return [];
            }

            return parsed.filter(
                (word) => typeof word === "string" && word.trim() !== "",
            );
        } catch {
            return [];
        }
    };

    const resolveWordsByLanguage = (lang) => {
        const normalizedLang = lang === "en" ? "en" : "id";
        const localizedWords = parseWords(`data-words-${normalizedLang}`);
        if (localizedWords.length > 0) {
            return localizedWords;
        }

        return parseWords("data-words");
    };

    let words = resolveWordsByLanguage(
        localStorage.getItem("site-lang") || "id",
    );
    if (!Array.isArray(words) || words.length === 0) {
        return;
    }

    let wordIndex = 0;
    let charIndex = 0;
    let deleting = false;
    let timerId = 0;

    const scheduleTick = (delay) => {
        if (timerId) {
            window.clearTimeout(timerId);
        }

        timerId = window.setTimeout(tick, delay);
    };

    const tick = () => {
        if (!Array.isArray(words) || words.length === 0) {
            return;
        }

        const currentWord = words[wordIndex] || "";

        if (!deleting) {
            charIndex += 1;
        } else {
            charIndex -= 1;
        }

        target.textContent = currentWord.slice(0, charIndex);

        let delay = deleting ? 60 : 90;

        if (!deleting && charIndex === currentWord.length) {
            delay = 1600;
            deleting = true;
        } else if (deleting && charIndex === 0) {
            deleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            delay = 220;
        }

        scheduleTick(delay);
    };

    const resetForLanguage = (lang) => {
        const nextWords = resolveWordsByLanguage(lang);
        if (!Array.isArray(nextWords) || nextWords.length === 0) {
            return;
        }

        words = nextWords;
        wordIndex = 0;
        charIndex = 0;
        deleting = false;
        target.textContent = "";
    };

    document.addEventListener("site-language-changed", (event) => {
        const lang = event?.detail?.lang || "id";
        resetForLanguage(lang);
    });

    tick();
};

const setupHeroSlider = () => {
    const slides = Array.from(document.querySelectorAll("[data-hero-slide]"));
    const dots = Array.from(document.querySelectorAll("[data-hero-dot]"));

    if (slides.length <= 1 || dots.length === 0) {
        return;
    }

    let index = 0;

    const showSlide = (nextIndex) => {
        index = nextIndex;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle("opacity-100", slideIndex === index);
            slide.classList.toggle("opacity-0", slideIndex !== index);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle("is-active", dotIndex === index);
        });
    };

    dots.forEach((dot) => {
        dot.addEventListener("click", () => {
            const nextIndex = Number(dot.getAttribute("data-index") || "0");
            showSlide(nextIndex);
        });
    });

    window.setInterval(() => {
        showSlide((index + 1) % slides.length);
    }, 5000);
};

const setupRevealOnScroll = () => {
    const elements = document.querySelectorAll(".reveal-section");
    if (elements.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries, observe) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observe.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12 },
    );

    elements.forEach((element) => observer.observe(element));
};

const setupGalleryFilter = () => {
    const buttons = Array.from(
        document.querySelectorAll("[data-gallery-filter]"),
    );
    const cards = Array.from(document.querySelectorAll("[data-gallery-item]"));

    if (buttons.length === 0 || cards.length === 0) {
        return;
    }

    const setFilter = (filterValue) => {
        buttons.forEach((button) => {
            button.classList.toggle(
                "is-active",
                button.getAttribute("data-gallery-filter") === filterValue,
            );
        });

        cards.forEach((card) => {
            const cardCategory = card.getAttribute("data-gallery-item");
            const isVisible =
                filterValue === "all" || cardCategory === filterValue;
            card.classList.toggle("hidden-by-filter", !isVisible);
        });
    };

    buttons.forEach((button) => {
        button.addEventListener("click", () => {
            const filterValue =
                button.getAttribute("data-gallery-filter") || "all";
            setFilter(filterValue);
        });
    });
};

const setupGalleryLightbox = () => {
    const modal = document.getElementById("gallery-lightbox");
    const figure = modal?.querySelector(".gallery-lightbox-figure");
    const image = document.getElementById("gallery-lightbox-image");
    const title = document.getElementById("gallery-lightbox-title");
    const category = document.getElementById("gallery-lightbox-category");
    const counter = document.getElementById("gallery-lightbox-counter");
    const closeButton = document.querySelector("[data-gallery-close]");
    const prevButton = document.querySelector("[data-gallery-prev]");
    const nextButton = document.querySelector("[data-gallery-next]");
    const cards = Array.from(document.querySelectorAll("[data-gallery-open]"));

    if (
        !modal ||
        !figure ||
        !image ||
        !title ||
        !category ||
        !counter ||
        !closeButton ||
        !prevButton ||
        !nextButton ||
        cards.length === 0
    ) {
        return;
    }

    let currentIndex = 0;

    const visibleCards = () =>
        cards.filter((card) => !card.classList.contains("hidden-by-filter"));

    const render = (targetIndex) => {
        const activeCards = visibleCards();
        if (activeCards.length === 0) {
            return;
        }

        currentIndex = (targetIndex + activeCards.length) % activeCards.length;
        const current = activeCards[currentIndex];

        image.setAttribute(
            "src",
            current.getAttribute("data-gallery-image") || "",
        );
        image.setAttribute(
            "alt",
            current.getAttribute("data-gallery-title") || "Gallery Image",
        );
        title.textContent = current.getAttribute("data-gallery-title") || "-";
        category.textContent =
            current.getAttribute("data-gallery-category") || "-";
        counter.textContent = `${currentIndex + 1} / ${activeCards.length}`;
        image.classList.remove("is-zoomed");
        figure.classList.remove("is-zoomed");
    };

    const close = () => {
        modal.classList.add("hidden");
        document.body.style.removeProperty("overflow");
        image.classList.remove("is-zoomed");
        figure.classList.remove("is-zoomed");
    };

    const open = (clickedCard) => {
        const activeCards = visibleCards();
        if (activeCards.length === 0) {
            return;
        }

        const index = Math.max(0, activeCards.indexOf(clickedCard));
        render(index);
        modal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
    };

    cards.forEach((card) => {
        card.addEventListener("click", () => open(card));
    });

    prevButton.addEventListener("click", () => render(currentIndex - 1));
    nextButton.addEventListener("click", () => render(currentIndex + 1));
    closeButton.addEventListener("click", close);

    image.addEventListener("click", () => {
        image.classList.toggle("is-zoomed");
        figure.classList.toggle(
            "is-zoomed",
            image.classList.contains("is-zoomed"),
        );
    });

    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            close();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (modal.classList.contains("hidden")) {
            return;
        }

        if (event.key === "Escape") {
            close();
        }

        if (event.key === "ArrowLeft") {
            render(currentIndex - 1);
        }

        if (event.key === "ArrowRight") {
            render(currentIndex + 1);
        }
    });
};

const setupVideoModal = () => {
    const openButtons = Array.from(
        document.querySelectorAll("[data-video-open]"),
    );
    const closeButton = document.querySelector("[data-video-close]");
    const modal = document.getElementById("about-video-modal");
    const player = document.getElementById("about-video-player");

    if (openButtons.length === 0 || !closeButton || !modal || !player) {
        return;
    }

    const openModal = () => {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        player.play().catch(() => {});
    };

    const closeModal = () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        player.pause();
        player.currentTime = 0;
    };

    openButtons.forEach((button) => {
        button.addEventListener("click", openModal);
    });
    closeButton.addEventListener("click", closeModal);

    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && modal.classList.contains("flex")) {
            closeModal();
        }
    });
};

document.addEventListener("DOMContentLoaded", () => {
    setupPreloader();
    setupLanguageSwitcher();
    setupThemeToggle();
    setupAdaptiveHeader();
    setupNavbarInteractions();
    setupNavbarHoverIndicator();
    setupAnnouncementRealtimeSync();
    setupPasswordToggle();
    setupTypewriter();
    setupHeroSlider();
    setupRevealOnScroll();
    setupGalleryFilter();
    setupGalleryLightbox();
    setupVideoModal();
    setupScrollTopButton();
});
