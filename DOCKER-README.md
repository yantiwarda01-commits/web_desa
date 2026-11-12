# 🐳 Docker Deployment Guide - Website Desa Sejahtera

Dokumentasi lengkap untuk deployment Website Desa menggunakan Docker dengan arsitektur multi-container dan ProxySQL load balancer.

---

## 📋 Arsitektur Container

```
┌─────────────────────────────────────────────────────────┐
│                     DOCKER HOST                          │
│                                                          │
│  ┌──────────────┐      ┌──────────────┐               │
│  │  Web Server  │◄────►│  ProxySQL    │               │
│  │ PHP + Apache │      │ Load Balancer│               │
│  │   Port 8080  │      │  Port 6033   │               │
│  └──────────────┘      └──────┬───────┘               │
│         │                      │                        │
│         │                      ▼                        │
│         │              ┌──────────────┐                │
│         └─────────────►│    MySQL     │                │
│                        │  Database    │                │
│                        │  Port 3307   │                │
│                        └──────────────┘                │
│                                                          │
│  ┌──────────────┐                                       │
│  │ phpMyAdmin   │  (Optional - Database Management)    │
│  │  Port 8081   │                                       │
│  └──────────────┘                                       │
│                                                          │
│  Persistent Volumes:                                    │
│  • mysql-data (Database)                                │
│  • proxysql-data (ProxySQL config)                      │
│  • uploads-data (User uploads)                          │
│  • webserver-logs (Apache logs)                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🚀 Quick Start

### 1. Prasyarat

Pastikan sudah terinstall:
- **Docker** version 20.10+
- **Docker Compose** version 2.0+

Cek versi:
```bash
docker --version
docker-compose --version
```

### 2. Clone / Copy Project

```bash
cd /path/to/your/projects
# Project sudah ada di: d:\Web Desa
```

### 3. Build & Run

```bash
# Masuk ke direktori project
cd "d:\Web Desa"

# Build images
docker-compose build

# Run containers (detached mode)
docker-compose up -d

# Atau build + run sekaligus
docker-compose up -d --build
```

### 4. Verifikasi Deployment

Cek status containers:
```bash
docker-compose ps
```

Output yang diharapkan:
```
NAME                STATUS              PORTS
desa-webserver      Up (healthy)        0.0.0.0:8080->80/tcp
desa-mysql          Up (healthy)        0.0.0.0:3307->3306/tcp
desa-proxysql       Up (healthy)        0.0.0.0:6032->6032/tcp, 0.0.0.0:6033->6033/tcp
desa-phpmyadmin     Up                  0.0.0.0:8081->80/tcp
```

### 5. Akses Aplikasi

**🌐 Website Public:**
```
http://localhost:8080/index-landing.php
```

**🔐 Admin Panel:**
```
http://localhost:8080/admin/login.php
```
- Username: `admin`
- Password: `admin123`

**🗄️ phpMyAdmin (Database Management):**
```
http://localhost:8081
```
- Server: `mysql`
- Username: `root`
- Password: `root_password_secure_2024`

---

## 📦 Services Detail

### 1. Web Server (webserver)

**Container:** `desa-webserver`  
**Image:** Custom (built from Dockerfile)  
**Base:** `php:8.1-apache`  
**Port:** `8080:80`

**Features:**
- PHP 8.1 with Apache
- Extensions: mysqli, pdo, pdo_mysql, gd, zip, opcache
- Upload limit: 10MB
- Memory limit: 256MB
- Timezone: Asia/Jakarta

**Environment Variables:**
```yaml
TZ: Asia/Jakarta
PHP_MEMORY_LIMIT: 256M
PHP_UPLOAD_MAX_FILESIZE: 10M
PHP_POST_MAX_SIZE: 12M
```

**Health Check:**
```bash
curl -f http://localhost:8080/health.php
```

---

### 2. Database (mysql)

**Container:** `desa-mysql`  
**Image:** `mysql:8.0`  
**Port:** `3307:3306` (external:internal)

**Credentials:**
```
Root User:
  Username: root
  Password: root_password_secure_2024

App User:
  Username: desa_user
  Password: desa_pass_2024
  Database: db_desa_sejahtera
```

**Configuration:**
- Character Set: utf8mb4
- Collation: utf8mb4_unicode_ci
- Max Connections: 200
- InnoDB Buffer Pool: 256M
- Slow Query Log: Enabled
- Binary Logging: Enabled

**Custom Config:** `docker/mysql/my.cnf`

**Health Check:**
```bash
docker exec desa-mysql mysqladmin ping -h localhost -uroot -proot_password_secure_2024
```

---

### 3. Database Proxy (proxysql)

**Container:** `desa-proxysql`  
**Image:** `proxysql/proxysql:2.5.5`  
**Ports:**
- `6033` - MySQL Interface (app connects here)
- `6032` - Admin Interface

**Purpose:**
- **Load Balancing:** Distribute queries across multiple MySQL servers
- **Query Routing:** Route read/write queries intelligently
- **Connection Pooling:** Reuse connections efficiently
- **Query Caching:** Cache SELECT queries for better performance
- **Monitoring:** Track query statistics

**Admin Access:**
```bash
# Via MySQL client
mysql -h 127.0.0.1 -P 6032 -u admin -padmin

# View backend servers
SELECT * FROM mysql_servers;

# View users
SELECT * FROM mysql_users;

# View query rules
SELECT * FROM mysql_query_rules;

# View statistics
SELECT * FROM stats_mysql_query_digest ORDER BY sum_time DESC LIMIT 10;
```

**Configuration:** `docker/proxysql/proxysql.cnf`

**Query Routing Rules:**
- Rule 1: `SELECT ... FOR UPDATE` → Master (no cache)
- Rule 2: `SELECT` queries → Cached (5 seconds TTL)
- Rule 3: `INSERT/UPDATE/DELETE` → Master

---

### 4. phpMyAdmin (Optional)

**Container:** `desa-phpmyadmin`  
**Image:** `phpmyadmin/phpmyadmin:latest`  
**Port:** `8081:80`

**Access:**
```
http://localhost:8081
```

**Features:**
- Visual database management
- SQL query editor
- Import/Export data
- User management

---

## 🔧 Docker Commands Cheatsheet

### Container Management

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose stop

# Stop and remove containers
docker-compose down

# Stop, remove containers + volumes (⚠️ DATA AKAN HILANG)
docker-compose down -v

# Restart specific service
docker-compose restart webserver

# View logs
docker-compose logs -f

# View logs for specific service
docker-compose logs -f webserver
docker-compose logs -f mysql
docker-compose logs -f proxysql

# Execute command in container
docker-compose exec webserver bash
docker-compose exec mysql mysql -uroot -p

# View container stats (CPU, Memory, Network)
docker stats
```

### Database Operations

```bash
# Backup database
docker-compose exec mysql mysqldump -uroot -proot_password_secure_2024 db_desa_sejahtera > backup_$(date +%Y%m%d).sql

# Restore database
docker-compose exec -T mysql mysql -uroot -proot_password_secure_2024 db_desa_sejahtera < backup_20261111.sql

# Access MySQL CLI
docker-compose exec mysql mysql -uroot -proot_password_secure_2024

# Access via ProxySQL
docker-compose exec mysql mysql -h proxysql -P 6033 -udesa_user -pdesa_pass_2024 db_desa_sejahtera
```

### Monitoring & Debugging

```bash
# Check service health
docker-compose ps

# Inspect container
docker inspect desa-webserver

# View container resource usage
docker stats desa-webserver desa-mysql desa-proxysql

# Check network
docker network ls
docker network inspect desa-sejahtera_desa-network

# Check volumes
docker volume ls
docker volume inspect desa-sejahtera_mysql-data
```

---

## 📊 Volume Management

### Persistent Volumes

1. **mysql-data**
   - Path: `/var/lib/mysql`
   - Purpose: MySQL database files
   - Backup: CRITICAL

2. **proxysql-data**
   - Path: `/var/lib/proxysql`
   - Purpose: ProxySQL configuration
   - Backup: Recommended

3. **uploads-data**
   - Path: `/var/www/html/assets/uploads`
   - Purpose: User uploaded files
   - Backup: CRITICAL

4. **webserver-logs**
   - Path: `/var/log/apache2`
   - Purpose: Apache access & error logs
   - Backup: Optional

### Backup Volumes

```bash
# Backup mysql-data volume
docker run --rm \
  -v desa-sejahtera_mysql-data:/data \
  -v $(pwd)/backups:/backup \
  ubuntu \
  tar czf /backup/mysql-data-$(date +%Y%m%d).tar.gz -C /data .

# Restore mysql-data volume
docker run --rm \
  -v desa-sejahtera_mysql-data:/data \
  -v $(pwd)/backups:/backup \
  ubuntu \
  tar xzf /backup/mysql-data-20261111.tar.gz -C /data
```

---

## 🔒 Security Best Practices

### 1. Change Default Passwords

Edit `docker-compose.yml` dan ubah:
```yaml
MYSQL_ROOT_PASSWORD: your_secure_root_password_here
MYSQL_PASSWORD: your_secure_app_password_here
```

Edit `docker/proxysql/proxysql.cnf`:
```conf
admin_credentials="admin:your_secure_admin_password"
```

### 2. Firewall Rules

Jika deploy ke production server:
```bash
# Allow only specific ports
ufw allow 8080/tcp  # Web
ufw allow 22/tcp    # SSH
ufw deny 3307/tcp   # Block direct MySQL access
ufw deny 6032/tcp   # Block ProxySQL admin
ufw deny 6033/tcp   # Block direct ProxySQL access
```

### 3. SSL/TLS Certificate

Tambahkan reverse proxy (Nginx/Traefik) dengan Let's Encrypt:
```yaml
nginx:
  image: nginx:alpine
  ports:
    - "443:443"
  volumes:
    - ./nginx.conf:/etc/nginx/nginx.conf
    - /etc/letsencrypt:/etc/letsencrypt
```

### 4. Environment Variables

Gunakan `.env` file (jangan commit ke Git):
```bash
cp .env.example .env
# Edit .env dengan credentials production
```

---

## 🚨 Troubleshooting

### Problem: Container tidak bisa start

```bash
# Check logs
docker-compose logs webserver
docker-compose logs mysql

# Check port conflicts
netstat -ano | findstr :8080
netstat -ano | findstr :3307

# Remove and recreate
docker-compose down
docker-compose up -d --force-recreate
```

### Problem: Database connection failed

```bash
# Test MySQL connection
docker-compose exec mysql mysql -uroot -p

# Test ProxySQL connection
docker-compose exec mysql mysql -h proxysql -P 6033 -udesa_user -p

# Check ProxySQL backend
docker-compose exec proxysql mysql -h 127.0.0.1 -P 6032 -uadmin -padmin -e "SELECT * FROM mysql_servers;"
```

### Problem: Permission denied pada uploads

```bash
# Fix permissions
docker-compose exec webserver chmod -R 777 /var/www/html/assets/uploads
```

### Problem: Slow performance

```bash
# Check resource usage
docker stats

# Increase MySQL buffer
# Edit docker/mysql/my.cnf
innodb_buffer_pool_size = 512M

# Restart
docker-compose restart mysql
```

---

## 🔄 Update & Maintenance

### Update Application Code

```bash
# Pull latest code
git pull

# Rebuild web container
docker-compose build webserver

# Restart without downtime
docker-compose up -d --no-deps webserver
```

### Database Migration

```bash
# Run migration SQL
docker-compose exec mysql mysql -uroot -p db_desa_sejahtera < migration.sql

# Or via phpMyAdmin
# Import SQL file through web interface
```

### Clean Up

```bash
# Remove unused images
docker image prune -a

# Remove unused volumes (⚠️ Careful!)
docker volume prune

# Remove everything unused
docker system prune -a --volumes
```

---

## 📈 Production Deployment

### 1. Server Requirements

**Minimum:**
- CPU: 2 cores
- RAM: 4GB
- Storage: 50GB SSD
- OS: Ubuntu 20.04+ / Debian 11+

**Recommended:**
- CPU: 4 cores
- RAM: 8GB
- Storage: 100GB SSD NVMe
- OS: Ubuntu 22.04 LTS

### 2. Installation on Ubuntu Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify
docker --version
docker-compose --version
```

### 3. Deploy Application

```bash
# Clone repository
git clone https://github.com/your-repo/website-desa.git
cd website-desa

# Copy environment
cp .env.example .env
nano .env  # Edit dengan production credentials

# Deploy
docker-compose up -d --build

# Check status
docker-compose ps
docker-compose logs -f
```

### 4. Setup Monitoring (Optional)

```bash
# Add Prometheus & Grafana
# Coming soon in Phase 2
```

---

## 📞 Support

**Developer:** Tim IT Desa Sejahtera  
**Email:** developer@desasejahtera.id  
**Documentation:** https://docs.desasejahtera.id

---

## 📄 License

Free untuk keperluan non-komersial dan edukasi.

---

**Selamat Deploy! 🚀**
