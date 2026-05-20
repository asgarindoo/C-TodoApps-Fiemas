# TaskFlow — To-Do List Plus

## Stack
- PHP 8.1 (native)
- MariaDB 10.6
- Nginx (LEMP)
- Tidak ada framework/library eksternal

## Cara Deploy

### 1. Upload file ke server
Salin seluruh folder ke web root nginx, misalnya:
```
/var/www/html/taskflow/
```
Atau langsung di `/var/www/html/` jika ingin akses di root.

### 2. Setup database (jalankan SEKALI)
Buka browser dan akses:
```
http://your-server-ip/taskflow/setup.php
```
Ini akan membuat:
- Database `todo_app`
- Tabel `users`, `categories`, `tasks`

Setelah berhasil, **hapus atau rename** `setup.php` untuk keamanan:
```bash
rm /var/www/html/taskflow/setup.php
```

### 3. Konfigurasi Nginx (jika belum ada)
Tambahkan ke block `server` di nginx config:
```nginx
location /taskflow/ {
    try_files $uri $uri/ /taskflow/index.php?$args;
    index index.php;
}

location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
}
```

### 4. Permission (jika perlu)
```bash
sudo chown -R www-data:www-data /var/www/html/taskflow
sudo chmod -R 755 /var/www/html/taskflow
```

## Struktur File
```
taskflow/
├── index.php          ← Halaman utama (dashboard)
├── login.php          ← Halaman login & register
├── logout.php         ← Handler logout
├── api.php            ← API endpoint (AJAX)
├── setup.php          ← Setup database (hapus setelah dijalankan)
└── includes/
    ├── db.php         ← Koneksi database
    ├── auth.php       ← Fungsi autentikasi
    └── functions.php  ← Fungsi tasks & categories
```

## Fitur
- ✅ Register & Login multi-user
- ✅ CRUD Tugas (buat, lihat, edit, hapus)
- ✅ Prioritas: High / Medium / Low (dengan warna)
- ✅ Deadline + indikator lewat deadline (⚠️)
- ✅ Filter: status, prioritas
- ✅ Sortir: deadline, prioritas, terbaru
- ✅ Kategori (Kerja, Kuliah, Belanja, Pribadi + custom)
- ✅ Statistik dashboard
- ✅ UI dark mode minimalis

## Kredensial DB Default
- Host: localhost
- User: adminjarkom
- Pass: segosambel
- DB: todo_app
