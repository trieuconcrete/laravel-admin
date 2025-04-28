# 🚀 Laravel 12 - Docker Starter Kit

This project provides a clean Docker-based development environment for Laravel 12 with the following services:

- PHP 8.2 (FPM)
- Nginx
- MySQL 8
- Redis
- Mailhog (for email testing)
- Node.js (build frontend assets)

## 📦 Prerequisites

- [Docker](https://www.docker.com/) installed
- [Docker Compose](https://docs.docker.com/compose/) installed
- (Optional) [Make](https://www.gnu.org/software/make/) installed for shortcut commands

---

## 🚀 Quick Start

1. Clone the repository:

    ```bash
    git clone https://github.com/trieuconcrete/laravel-admin.git
    cd laravel-admin
    ```

2. Start all services:

    ```bash
    make up
    ```

3. Install Laravel (if not yet installed):

    ```bash
    make bash
    composer create-project laravel/laravel . "^12.0"
    exit
    ```

4. Set Laravel environment:

    ```bash
    cp .env.example .env
    ```

5. Generate app key:

    ```bash
    make key-generate
    ```

6. Run migrations:

    ```bash
    make migrate
    ```

7. Run seeder:

    ```bash
    make seed
    ```

8. Build frontend assets:

    ```bash
    make npm cmd=dev
    ```

---

## 📚 Available Commands (Makefile)

| Command                  | Description                      |
|:------------------------- |:---------------------------------|
| `make up`                 | Start all docker services        |
| `make down`               | Stop all services                |
| `make build`              | Build Docker images              |
| `make bash`               | SSH into the app container       |
| `make artisan cmd="xxx"`  | Run an Artisan command           |
| `make composer cmd="xxx"` | Run a Composer command           |
| `make npm cmd="xxx"`      | Run npm/yarn command             |
| `make migrate`            | Run Laravel migrations           |
| `make fresh`              | Fresh migrate with seeding       |
| `make key-generate`       | Generate Laravel app key         |
| `make mailhog`            | Open Mailhog web interface       |

---

## 🔧 Services

| Service  | URL                  | Description       |
|:---------|:----------------------|:------------------|
| App      | http://localhost/admin/login       | Laravel app       |
| Mailhog  | http://localhost:8025  | Email catcher     |
| MySQL    | 127.0.0.1:3306         | Database          |
| Redis    | 127.0.0.1:6379         | Cache / Queue     |

---

## 🛠 Configuration Highlights

- Laravel `.env` automatically points to:
  - `DB_HOST=mysql`
  - `REDIS_HOST=redis`
  - `MAIL_HOST=mailhog`
- PHP `supervisor` included for queue workers.
- Support for Composer and Node.js in isolated containers.

---
