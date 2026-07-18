# Enterprise Fashion E-commerce Platform

A scalable, modular, and dynamic enterprise-grade headless e-commerce ecosystem built with Laravel 12 (API Core & Octane), Next.js 15 (Storefront Web), Flutter (Consumer Mobile App), and Filament v3 (Dynamic Admin Management Panel).

---

## 📂 Repository Layout

```text
├── backend/             # Laravel 12 API & Filament Admin Panel
├── frontend/            # Next.js 15 App Router Customer Storefront
├── mobile/              # Flutter Cross-Platform Mobile Application
├── docs/                # Architecture Specifications & API Schemas
└── docker-compose.yml   # Local Development Multi-Container Services
```

---

## 🛠️ Technology Stack Overview

### Backend (API Core & Backoffice)
*   **Framework**: Laravel 12
*   **Application Server**: Laravel Octane with Swoole (In-Memory Processing)
*   **Database**: PostgreSQL (Master-Slave replication support)
*   **Caching & Queues**: Redis (Cluster setup)
*   **Admin Panel**: Filament v3 (Livewire / Alpine.js integration)
*   **Faceted Search**: Meilisearch (Typo-tolerant product discovery engine)

### Frontend (Consumer Web Storefront)
*   **Framework**: Next.js 15 (App Router, Server Components)
*   **Styling**: Vanilla CSS Variables (supporting live dynamic CMS themes) + Tailwind CSS
*   **State Management**: Zustand & TanStack Query (React Query)

### Mobile App (Consumer iOS & Android)
*   **Framework**: Flutter
*   **State Management**: Business Logic Component (BLoC)
*   **Networking Client**: Dio Client with dynamic interceptors
*   **Local Caching**: Hive / Isar database

---

## 🚀 Getting Started

### Local Development Requirements
Ensure the following packages are installed on your machine:
*   [Docker Desktop](https://www.docker.com/products/docker-desktop/)
*   [Git](https://git-scm.com/)

Detailed setup and installation guides for each stack are located in the `docs/setup/` directory.
