# ☕ Aplikasi Kasir Warkop Un Un

Aplikasi kasir sederhana berbasis PHP Native + MySQL dengan konsep **Object Oriented Programming (OOP)** untuk kebutuhan warkop / cafe kecil.

Project ini dibuat untuk memenuhi tugas mata kuliah **Konsep Bahasa Pemrograman**.

---

# 📌 Fitur Utama

## 👤 Sistem Login

* Login kasir dan admin
* Session login
* Redirect otomatis jika belum login
* Role admin dan kasir

## 🛒 Sistem Pemesanan

* Tambah pesanan
* Pilih menu berdasarkan kategori
* Hitung total otomatis
* Pilih metode pembayaran:

  * Tunai
  * QRIS
* Hitung kembalian otomatis
* Cetak struk pembayaran

## 📦 Manajemen Menu

(Khusus Admin)

* Tambah menu
* Edit menu
* Hapus menu
* Atur stok menu

## 📊 Dashboard Admin

* Total transaksi
* Total item terjual
* Total pendapatan
* Pendapatan tunai
* Pendapatan QRIS
* Total menu
* Pengaturan jumlah meja

## 📄 Data Pesanan

* Melihat seluruh data pesanan
* Edit pesanan (admin)
* Hapus pesanan (admin)
* Cetak struk

---

# 🧠 Konsep OOP yang Digunakan

## 1. Class & Object

Semua fitur dipisahkan ke dalam class:

* Database
* User
* Admin
* Auth
* Menu
* Pesanan

## 2. Inheritance

```php
class Admin extends User
```

Class `Admin` mewarisi class `User`.

## 3. Encapsulation

Property dibuat `private` dan `protected`, lalu diakses menggunakan getter dan setter.

## 4. Singleton Pattern

Digunakan pada:

* `Database`
* `Auth`

Agar object hanya dibuat satu kali.

---

# 📁 Struktur Folder

```bash
Projek UAS/
│
├── classes/
│   ├── Admin.php
│   ├── Auth.php
│   ├── Database.php
│   ├── Menu.php
│   ├── Pesanan.php
│   └── User.php
│
├── dashboard_admin.php
├── data.php
├── index.php
├── login.php
├── logout.php
├── proses.php
├── struk.php
├── setting_meja.txt
├── warkop.sql
├── class diagram.png
└── README_OOP.md
```

---

# 🗂 Penjelasan File

| File                  | Fungsi                      |
| --------------------- | --------------------------- |
| `index.php`           | Halaman utama kasir         |
| `login.php`           | Halaman login               |
| `logout.php`          | Logout session              |
| `dashboard_admin.php` | Dashboard khusus admin      |
| `data.php`            | Menampilkan data pesanan    |
| `proses.php`          | Handler seluruh proses CRUD |
| `struk.php`           | Cetak struk transaksi       |
| `setting_meja.txt`    | Menyimpan jumlah meja       |
| `warkop.sql`          | Database MySQL              |

---

# 🧩 Penjelasan Class

## 📌 Database.php

Class untuk koneksi database menggunakan Singleton.

### Method utama:

* `getInstance()`
* `getKoneksi()`
* `escape()`
* `tutupKoneksi()`

---

## 📌 User.php

Base class untuk user/kasir.

### Property:

* idKasir
* username
* password
* namaLengkap
* role
* isLogin

### Method:

* getter & setter
* `simpanKeSession()`
* `isAdmin()`
* `cariByLogin()`

---

## 📌 Admin.php

Turunan dari class `User`.

### Fitur admin:

* Tambah menu
* Edit menu
* Hapus menu
* Edit pesanan
* Hapus pesanan
* Update jumlah meja
* Statistik dashboard

---

## 📌 Auth.php

Class autentikasi dan session login.

### Method:

* `login()`
* `logout()`
* `cekLogin()`
* `cekAdmin()`
* `requireLogin()`
* `requireAdmin()`
* `redirectJikaSudahLogin()`

---

## 📌 Menu.php

Class untuk CRUD menu.

### Method:

* `tampilSemua()`
* `ambilById()`
* `tambah()`
* `edit()`
* `hapus()`
* `kategorikan()`

Kategori menu:

* Makanan
* Cemilan
* Kopi
* Non Kopi

---

## 📌 Pesanan.php

Class untuk mengelola transaksi.

### Method:

* `tampilSemua()`
* `ambilById()`
* `tambah()`
* `edit()`
* `hapus()`
* `tampilAlert()`

Data transaksi meliputi:

* Nama pembeli
* Nama kasir
* Menu
* Jumlah
* Total harga
* Metode pembayaran
* Uang bayar
* Kembalian
* Waktu transaksi

---

# 🖥️ Teknologi yang Digunakan

* PHP Native
* MySQL
* Bootstrap 5
* Bootstrap Icons
* HTML5
* CSS3
* JavaScript

---

# ⚙️ Cara Menjalankan Project

## 1. Pindahkan project ke folder Laragon

Contoh:

```bash
C:/laragon/www/
```

## 2. Jalankan Laragon

Aktifkan:

* Apache
* MySQL

## 3. Import database

* Buka phpMyAdmin
* Import file `warkop.sql`

## 4. Jalankan project

Buka browser:

```bash
http://localhost/Projek%20UAS/
```

---

# 🔐 Hak Akses

## Admin

Bisa:

* Kelola menu
* Kelola pesanan
* Lihat dashboard
* Atur jumlah meja

## Kasir

Bisa:

* Input pesanan
* Cetak struk
* Melihat data pesanan

---

# 🧾 Tampilan Utama

## Login

Halaman autentikasi kasir/admin.

## Halaman Kasir

Menampilkan:

* daftar menu
* kategori menu
* keranjang pesanan
* pembayaran

## Dashboard Admin

Menampilkan statistik dan CRUD menu.

## Struk

Tampilan printable untuk transaksi.

---

# 🗃 Database

Database menggunakan nama:

```sql
warkop
```

Tabel utama:

* kasir
* menu
* pesanan

---

# 📚 Tujuan Project

Project ini dibuat untuk mempelajari:

* OOP pada PHP Native
* CRUD MySQL
* Session Login
* Konsep inheritance
* Encapsulation
* Singleton Pattern
* Pembuatan sistem kasir sederhana

---

# 👨‍💻 Author

Project UAS Konsep Bahasa Pemrograman
Semester 2 - Informatika
