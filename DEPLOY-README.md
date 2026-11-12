# 🚀 Quick Deploy Guide - Website Desa

## ✅ Perbaikan Terbaru (2024-12-13)

### Masalah Yang Diperbaiki:
- ✅ **500 Internal Server Error** - FIXED
- ✅ Database connection error handling
- ✅ Graceful degradation saat database belum siap
- ✅ Startup script yang menunggu MySQL ready
- ✅ Better error logging

---

## 🎯 Deployment Mudah (1 Klik)

### Metode 1: Gunakan Script Otomatis (RECOMMENDED)

```powershell
.\DEPLOY.bat
```

Script ini akan:
1. Check Docker running
2. Stop container lama
3. Build image baru
4. Start semua services
5. Wait MySQL ready (60s)
6. Buka debug page otomatis

---

## 📋 Deployment Manual

### Step 1: Stop Container Lama

```powershell
docker-compose -f docker-compose-simple.yml down
```

### Step 2: Build Image

```powershell
docker-compose -f docker-compose-simple.yml build --no-cache
```

### Step 3: Start Containers

```powershell
docker-compose -f docker-compose-simple.yml up -d
```

### Step 4: Wait (PENTING!)

Tunggu **60 detik** untuk MySQL selesai inisialisasi.

### Step 5: Check Status

```powershell
docker-compose -f docker-compose-simple.yml ps
```

Pastikan semua status **Up** dan **healthy**.

### Step 6: Test Website

Buka browser:
- http://localhost:8080/debug-info.php

---

## 🔍 Quick Check

### Semua Service Running?

```powershell
docker-compose -f docker-compose-simple.yml ps
```

Expected output:
```
NAME                 STATUS
desa-mysql           Up (healthy)
desa-webserver       Up (healthy)
desa-phpmyadmin      Up
```

### Check Logs

```powershell
# All logs
docker-compose -f docker-compose-simple.yml logs

# Follow logs (real-time)
docker-compose -f docker-compose-simple.yml logs -f webserver
```

### Health Check

```powershell
curl http://localhost:8080/health.php
```

Expected:
```json
{
    "status": "ok",
    "database": "connected"
}
```

---

## 🌐 Access URLs

| Service | URL | Credentials |
|---------|-----|-------------|
| Debug Info | http://localhost:8080/debug-info.php | - |
| Connection Test | http://localhost:8080/test-connection.php | - |
| Main Website | http://localhost:8080 | - |
| Health Check | http://localhost:8080/health.php | - |
| PhpMyAdmin | http://localhost:8081 | root / root_password_secure_2024 |

---

## ❌ Troubleshooting

### Issue: Port Already in Use

```powershell
# Check port 8080
netstat -ano | findstr :8080

# Kill process (replace PID)
taskkill /PID <PID> /F
```

### Issue: Container Unhealthy

```powershell
# Clean restart
docker-compose -f docker-compose-simple.yml down -v
docker-compose -f docker-compose-simple.yml up -d

# Wait 60 seconds
timeout /t 60 /nobreak
```

### Issue: Database Error

```powershell
# Reimport database
docker exec -i desa-mysql mysql -u root -proot_password_secure_2024 db_desa_sejahtera < database.sql
```

### Issue: Still Getting 500 Error

```powershell
# Check webserver logs
docker-compose -f docker-compose-simple.yml logs webserver

# Restart webserver
docker-compose -f docker-compose-simple.yml restart webserver

# Wait and try again
timeout /t 30 /nobreak
```

---

## 🧹 Clean Start (Nuclear Option)

Jika masih error, lakukan clean start:

```powershell
# Stop dan hapus semua
docker-compose -f docker-compose-simple.yml down -v

# Hapus volumes
docker volume prune -f

# Rebuild
docker-compose -f docker-compose-simple.yml build --no-cache

# Start
docker-compose -f docker-compose-simple.yml up -d

# Wait
timeout /t 60 /nobreak

# Test
start http://localhost:8080/debug-info.php
```

---

## 📁 Arsitektur

```
Website Desa (Docker)
│
├── webserver (PHP 8.1 + Apache)
│   ├── Port: 8080
│   ├── Volume: ./
│   └── Wait for: mysql
│
├── mysql (MySQL 8.0)
│   ├── Port: 3307 (external)
│   ├── Port: 3306 (internal)
│   ├── Volume: mysql-data (persistent)
│   └── Init: database.sql
│
└── phpmyadmin
    ├── Port: 8081
    └── Connect to: mysql
```

---

## ✅ Success Checklist

Deployment sukses jika:

- [ ] `docker-compose ps` menunjukkan semua **healthy/Up**
- [ ] http://localhost:8080/health.php returns `"status": "ok"`
- [ ] http://localhost:8080/debug-info.php shows ✓ **CONNECTION SUCCESSFUL**
- [ ] http://localhost:8080 menampilkan website
- [ ] No errors in logs

---

## 📞 Still Need Help?

Baca dokumentasi lengkap:
- **TROUBLESHOOTING.md** - Detailed troubleshooting guide
- **DOCKER-README.md** - Complete Docker documentation
- **DOCKER-QUICKSTART.md** - Quick start guide

Collect logs:
```powershell
docker-compose -f docker-compose-simple.yml logs > logs.txt
```

---

## 🔒 Security Notes

**JANGAN LUPA** setelah deployment sukses:

1. **Hapus file debugging:**
   ```powershell
   Remove-Item debug-info.php, test-connection.php
   ```

2. **Ganti password di `docker-compose-simple.yml`:**
   - MYSQL_ROOT_PASSWORD
   - MYSQL_PASSWORD

3. **Untuk production, gunakan `.env` file** untuk credentials

---

**Version:** 2.0 (Fixed)  
**Last Updated:** 2024-12-13  
**Status:** ✅ WORKING
