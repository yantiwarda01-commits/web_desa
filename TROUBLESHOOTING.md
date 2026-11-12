# 🔧 Troubleshooting Guide - Website Desa

## Error 500 Internal Server Error - SOLVED ✅

### Masalah Yang Diperbaiki

1. **Database Connection Error Handling** - Diperbaiki error handling pada fungsi database
2. **Error Logging** - Menambahkan logging yang lebih baik
3. **Graceful Degradation** - Website tetap bisa loading meski database belum siap
4. **Startup Script** - Menambahkan entrypoint yang menunggu MySQL siap

### Perbaikan Yang Dilakukan

#### 1. File `config/database.php`
- ✅ Menambahkan pengecekan koneksi di `prepareStatement()`
- ✅ Menambahkan error handling di `fetchOne()` dan `fetchAll()`
- ✅ Mengembalikan array kosong/null jika gagal, bukan error fatal

#### 2. File `index-landing.php`
- ✅ Menambahkan try-catch untuk fetch data
- ✅ Menambahkan default values jika database gagal
- ✅ Website tetap bisa ditampilkan meski data kosong

#### 3. File `index.php` (NEW)
- ✅ Entry point baru dengan error handling
- ✅ Menampilkan halaman error yang user-friendly
- ✅ Link ke debugging tools

#### 4. File `docker-entrypoint.sh` (NEW)
- ✅ Menunggu MySQL siap sebelum start Apache
- ✅ Check koneksi database dengan retry mechanism
- ✅ Set permissions otomatis

#### 5. File `Dockerfile` (UPDATED)
- ✅ Menggunakan entrypoint script
- ✅ Copy script dengan permissions yang benar

---

## 🚀 Cara Deploy Ulang (SIMPLE VERSION)

### Step 1: Stop Container Yang Ada

```powershell
docker-compose down
```

### Step 2: Rebuild Images

```powershell
docker-compose -f docker-compose-simple.yml build --no-cache
```

### Step 3: Start Containers

```powershell
docker-compose -f docker-compose-simple.yml up -d
```

### Step 4: Check Status

```powershell
docker-compose -f docker-compose-simple.yml ps
```

Semua service harus status **Up** dan MySQL harus **healthy**.

### Step 5: Wait & Check Logs

Tunggu 30-60 detik agar MySQL selesai inisialisasi, kemudian check logs:

```powershell
# Check webserver logs
docker-compose -f docker-compose-simple.yml logs webserver

# Check MySQL logs
docker-compose -f docker-compose-simple.yml logs mysql
```

### Step 6: Test Website

Buka browser dan akses:

1. **Debug Info**: http://localhost:8080/debug-info.php
   - Melihat status lengkap sistem
   - Check koneksi database
   
2. **Connection Test**: http://localhost:8080/test-connection.php
   - Test koneksi database detail
   - Lihat tabel yang ada

3. **Health Check**: http://localhost:8080/health.php
   - JSON endpoint untuk monitoring
   
4. **Main Website**: http://localhost:8080 atau http://localhost:8080/index-landing.php
   - Website utama

5. **PhpMyAdmin**: http://localhost:8081
   - User: `root`
   - Password: `root_password_secure_2024`

---

## 🔍 Debugging Commands

### Melihat Logs Real-time

```powershell
# All services
docker-compose -f docker-compose-simple.yml logs -f

# Webserver only
docker-compose -f docker-compose-simple.yml logs -f webserver

# MySQL only
docker-compose -f docker-compose-simple.yml logs -f mysql
```

### Masuk ke Container

```powershell
# Masuk ke webserver container
docker exec -it desa-webserver bash

# Di dalam container, check:
php -v                          # PHP version
php -m | grep mysqli            # MySQLi extension
ls -la /var/www/html           # List files
cat /var/log/apache2/error.log # Apache error log
```

### Check Database Connection dari Container

```powershell
docker exec -it desa-webserver bash
```

Kemudian di dalam container:

```bash
php -r "
\$conn = new mysqli('mysql', 'desa_user', 'desa_pass_2024', 'db_desa_sejahtera', 3306);
if (\$conn->connect_error) {
    echo 'Connection failed: ' . \$conn->connect_error;
} else {
    echo 'Connected successfully!';
}
"
```

### Check MySQL Tables

```powershell
docker exec -it desa-mysql mysql -u root -proot_password_secure_2024 db_desa_sejahtera -e "SHOW TABLES;"
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: MySQL Container Unhealthy

**Symptom:**
```
desa-mysql is unhealthy
```

**Solution:**
```powershell
# Stop everything
docker-compose -f docker-compose-simple.yml down -v

# Remove MySQL volume
docker volume rm web-desa_mysql-data

# Start again
docker-compose -f docker-compose-simple.yml up -d
```

### Issue 2: Port Already in Use

**Symptom:**
```
Error: bind: address already in use
```

**Solution:**
```powershell
# Check what's using the port
netstat -ano | findstr :8080
netstat -ano | findstr :3307

# Kill the process (replace PID with actual PID)
taskkill /PID <PID> /F

# Or change port in docker-compose-simple.yml
```

### Issue 3: Permission Denied

**Symptom:**
```
Permission denied: /var/www/html/assets/uploads
```

**Solution:**
```powershell
docker exec -it desa-webserver bash
chmod -R 777 /var/www/html/assets/uploads
chown -R www-data:www-data /var/www/html/assets/uploads
exit
```

### Issue 4: Database Not Initialized

**Symptom:**
- Tables tidak ada
- Error: Table 'profil_desa' doesn't exist

**Solution:**
```powershell
# Import database manually
docker exec -i desa-mysql mysql -u root -proot_password_secure_2024 db_desa_sejahtera < database.sql

# Or copy and import inside container
docker cp database.sql desa-mysql:/tmp/
docker exec -it desa-mysql bash
mysql -u root -proot_password_secure_2024 db_desa_sejahtera < /tmp/database.sql
exit
```

### Issue 5: Website Shows Error Page

**Solution:**
1. Check logs:
   ```powershell
   docker-compose -f docker-compose-simple.yml logs webserver
   ```

2. Check debug page:
   ```
   http://localhost:8080/debug-info.php
   ```

3. Wait longer - MySQL might still be initializing (60 seconds)

4. Restart webserver:
   ```powershell
   docker-compose -f docker-compose-simple.yml restart webserver
   ```

---

## 📊 Expected Output

### Successful Deployment

```
[+] Running 4/4
 ✔ Network web-desa_desa-network       Created
 ✔ Container desa-mysql                Started (healthy)
 ✔ Container desa-webserver            Started (healthy)
 ✔ Container desa-phpmyadmin           Started
```

### Health Check JSON (http://localhost:8080/health.php)

```json
{
    "status": "ok",
    "timestamp": "2024-12-13 10:30:00",
    "service": "Website Desa Sejahtera",
    "version": "1.0.0",
    "database": "connected",
    "database_host": "mysql:3306",
    "database_name": "db_desa_sejahtera",
    "uploads_writable": true,
    "php_version": "8.1.x",
    "memory_usage": "2.5 MB"
}
```

### Debug Info Page

Should show:
- ✅ PHP Version: 8.1.x
- ✅ Docker: YES
- ✅ MySQLi Extension: LOADED
- ✅ DB_HOST: mysql
- ✅ Connection: SUCCESSFUL

---

## 🧹 Clean Start (Nuclear Option)

Jika semua cara di atas tidak berhasil, lakukan clean start:

```powershell
# Stop dan hapus semua
docker-compose -f docker-compose-simple.yml down -v

# Hapus semua volumes
docker volume prune -f

# Hapus images (optional)
docker rmi desa-webserver web-desa-webserver

# Build dan start fresh
docker-compose -f docker-compose-simple.yml build --no-cache
docker-compose -f docker-compose-simple.yml up -d

# Wait 60 seconds
Start-Sleep -Seconds 60

# Check status
docker-compose -f docker-compose-simple.yml ps

# Test
start http://localhost:8080/debug-info.php
```

---

## ✅ Verification Checklist

Setelah deployment, pastikan semua ini sukses:

- [ ] `docker-compose ps` shows all containers as **healthy** or **Up**
- [ ] http://localhost:8080/health.php returns `"status": "ok"`
- [ ] http://localhost:8080/debug-info.php shows ✓ CONNECTION SUCCESSFUL
- [ ] http://localhost:8080/test-connection.php shows green success message
- [ ] http://localhost:8080 or http://localhost:8080/index-landing.php loads website
- [ ] http://localhost:8081 PhpMyAdmin accessible
- [ ] No errors in `docker-compose logs webserver`
- [ ] No errors in `docker-compose logs mysql`

---

## 📝 Production Deployment Notes

Untuk deployment production:

1. **Hapus file debugging:**
   ```powershell
   Remove-Item debug-info.php, test-connection.php
   ```

2. **Ganti credentials di docker-compose:**
   - DB passwords
   - ROOT passwords

3. **Enable HTTPS:**
   - Uncomment HTTPS redirect di `.htaccess`
   - Add SSL certificate

4. **Set environment:**
   ```
   PHP_ENV=production
   display_errors=Off
   ```

5. **Backup strategy:**
   - Setup database backup cron
   - Backup volume `mysql-data`

---

## 🆘 Need More Help?

Jika masih ada masalah:

1. **Collect logs:**
   ```powershell
   docker-compose -f docker-compose-simple.yml logs > docker-logs.txt
   ```

2. **Collect info:**
   - Screenshot dari debug-info.php
   - Screenshot dari test-connection.php
   - Output dari `docker-compose ps`

3. **Check Docker resources:**
   - Docker Desktop -> Settings -> Resources
   - Minimal: 2GB RAM, 2 CPU cores

---

**Last Updated:** 2024-12-13  
**Version:** 2.0 (Post-Fix)
