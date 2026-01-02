# WP Desa

Plugin WordPress komprehensif untuk mengelola fitur website desa modern. Dibangun dengan arsitektur OOP, REST API, dan Alpine.js untuk performa yang cepat dan interaktif.

## 🚀 Fitur Utama (Core)

- **Arsitektur OOP**: Struktur kode yang modular dan mudah dikembangkan (Namespace `WP_Desa`).
- **REST API Driven**: Komunikasi data frontend-backend sepenuhnya melalui WordPress REST API.
- **Alpine.js Integration**: Interaktivitas frontend yang ringan tanpa dependensi jQuery yang berat (meskipun jQuery tetap diload oleh WP).
- **Autoloader**: PSR-4 Autoloading untuk manajemen class yang efisien.

## 📂 Struktur Folder

```
wp-desa/
├── assets/             # File statis (CSS, JS)
├── inc/                # File include non-class (autoloader)
├── src/                # Source code PHP (OOP)
│   ├── Api/            # Logika REST API
│   ├── Core/           # Logika inti plugin (Activator, Plugin Setup)
│   └── Frontend/       # Logika tampilan (Assets, Shortcodes)
└── wp-desa.php         # Entry point plugin
```

## 🛠️ Instalasi

1. Clone atau download repository ini ke folder `wp-content/plugins/`.
2. Masuk ke dashboard WordPress admin.
3. Aktifkan plugin **WP Desa**.

## 💻 Penggunaan

### Shortcode
Gunakan shortcode berikut di halaman atau post mana saja untuk menampilkan info desa (demo):

```
[wp_desa_info]
```

### Pengembangan

**Menambah Endpoint API Baru:**
1. Buat controller baru di `src/Api/Controllers/` extending `BaseController`.
2. Daftarkan controller di `src/Api/Router.php`.

**Menambah Logika Frontend:**
1. Edit `assets/js/wp-desa.js`.
2. Gunakan `Alpine.data()` untuk membuat komponen baru.

## 📄 Lisensi

GPL-2.0+
