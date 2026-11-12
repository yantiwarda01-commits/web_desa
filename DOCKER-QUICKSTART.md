# 🚀 Docker Quick Start Guide

Panduan cepat untuk menjalankan Website Desa menggunakan Docker dalam 5 menit!

---

## ✅ Prerequisites

Pastikan sudah terinstall:
- ✅ Docker Desktop (Windows/Mac) atau Docker Engine (Linux)
- ✅ Docker Compose v2.0+

**Cek instalasi:**
```bash
docker --version
docker-compose --version
```

---

## 🏃 Quick Start (3 Langkah)

### 1️⃣ Build & Run
```bash
# Masuk ke direktori project
cd "d:\Web Desa"

# Build dan jalankan semua services
docker-compose up -d --build
```

### 2️⃣ Tunggu Services Ready (~60 detik)
```bash
# Monitor logs sampai semua service healthy
docker-compose logs -f
```

Tunggu hingga muncul:
```
desa-mysql       | ready for connections
desa-proxysql    | ProxySQL is running
desa-webserver   | Apache started
```

Tekan `Ctrl+C` untuk keluar dari logs.

### 3️⃣ Akses Website
Buka browser:

**🌐 Website Public:**
```
http://localhost:8080/index-landing.php
```

**🔐 Admin Panel:**
```
http://localhost:8080/admin/login.php
Username: admin
Password: admin123
```

**🗄️ phpMyAdmin:**
```
http://localhost:8081
Server: mysql
Username: root
Password: root_password_secure_2024
```

---

## 🎉 Selesai!

Website Desa sudah berjalan di Docker dengan:
- ✅ PHP 8.1 + Apache Web Server
- ✅ MySQL 8.0 Database
- ✅ ProxySQL Load Balancer
- ✅ phpMyAdmin untuk management
- ✅ Persistent volumes (data tidak hilang)

---

## 🛑 Cara Stop & Start

```bash
# Stop semua container (data tetap aman)
docker-compose stop

# Start lagi
docker-compose start

# Stop dan hapus container (data volume tetap aman)
docker-compose down

# Start fresh
docker-compose up -d
```

---

## 📊 Monitoring

```bash
# Cek status semua container
docker-compose ps

# Lihat logs real-time
docker-compose logs -f

# Lihat logs service tertentu
docker-compose logs -f webserver
docker-compose logs -f mysql

# Cek resource usage
docker stats
```

---

## 🔧 Troubleshooting

### Problem: Port 8080 sudah digunakan

Edit `docker-compose.yml`:
```yaml
webserver:
  ports:
    - "8090:80"  # Ganti 8080 jadi 8090
```

Lalu restart:
```bash
docker-compose down
docker-compose up -d
```

### Problem: Container tidak start

```bash
# Lihat error logs
docker-compose logs

# Force recreate
docker-compose down
docker-compose up -d --force-recreate
```

### Problem: Database connection error

```bash
# Cek MySQL health
docker-compose exec mysql mysqladmin ping -uroot -proot_password_secure_2024

# Restart MySQL
docker-compose restart mysql

# Tunggu 30 detik, lalu restart webserver
docker-compose restart webserver
```

---

## 🧪 Test Database Connection

Buka health check endpoint:
```
http://localhost:8080/health.php
```

Harus muncul:
```json
{
  "status": "ok",
  "database": "connected",
  "database_host": "proxysql:6033"
}
```

---

## 📦 Backup Database

```bash
# Backup ke file SQL
docker-compose exec mysql mysqldump -uroot -proot_password_secure_2024 db_desa_sejahtera > backup.sql

# Restore dari backup
docker-compose exec -T mysql mysql -uroot -proot_password_secure_2024 db_desa_sejahtera < backup.sql
```

---

## 🔄 Update Application Code

Setelah edit code:
```bash
# Restart web container aja
docker-compose restart webserver

# Atau rebuild jika ada perubahan Dockerfile
docker-compose up -d --build webserver
```

---

## 🗑️ Clean Up

```bash
# Stop dan hapus semua container
docker-compose down

# Hapus juga volumes (⚠️ DATA AKAN HILANG!)
docker-compose down -v

# Hapus semua images
docker-compose down --rmi all

# Full cleanup (⚠️ SEMUA DATA HILANG!)
docker-compose down -v --rmi all
docker system prune -a --volumes
```

---

## 📚 Dokumentasi Lengkap

Baca detail lengkap di: **[DOCKER-README.md](./DOCKER-README.md)**

---

## 🆘 Need Help?

**Email:** developer@desasejahtera.id  
**Documentation:** Lihat DOCKER-README.md untuk detail lengkap

---

**Happy Deploying! 🎉**
