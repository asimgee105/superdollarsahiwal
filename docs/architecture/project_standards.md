# Enterprise Coding Standards & Guidelines

This document outlines the strict coding standards, naming conventions, and workflow rules for the Fashion E-commerce Platform project across backend, frontend, and mobile codebases.

---

## 1. File & Folder Naming Rules

### A. Global Rules
*   Use standard English nouns for files and folders.
*   Avoid single-letter variable names or directory acronyms.

### B. Backend (Laravel)
*   **Modules**: PascalCase (e.g. `Catalog`, `Inventory`).
*   **Controllers**: Singular PascalCase with `Controller` suffix (e.g. `ProductController.php`).
*   **Models**: Singular PascalCase (e.g. `ProductVariant.php`).
*   **Migrations**: Snake_case with standard database migration timestamp prefix (e.g. `2026_07_11_000000_create_products_table.php`).
*   **Services & Actions**: PascalCase representing singular action operations (e.g. `ReserveInventoryAction.php`).

### C. Frontend (Next.js)
*   **Components**: PascalCase (e.g. `PDPGallery.tsx`, `CartDrawer.tsx`).
*   **Hooks**: camelCase starting with `use` (e.g. `useCartSession.ts`).
*   **Features / Modules**: kebab-case or simple lowercase (e.g. `src/features/catalog`).
*   **Styles & Configuration**: lowercase or kebab-case (e.g. `global.css`, `tailwind.config.ts`).

### D. Mobile (Flutter)
*   **Dart files**: Lowercase snake_case (e.g. `product_detail_page.dart`, `catalog_repository.dart`).
*   **Classes**: PascalCase (e.g. `ProductRepository`).
*   **State Management (BLoCs)**: PascalCase suffix representing BLoC type (e.g. `CatalogBloc`, `CatalogEvent`, `CatalogState`).

---

## 2. API Design & Naming Rules

*   **Endpoint URLs**: Lowercase kebab-case (e.g. `/api/v1/product-variants`).
*   **HTTP Methods**:
    *   `GET`: Retrieve resources.
    *   `POST`: Create resources.
    *   `PUT`: Update entire resource payloads.
    *   `PATCH`: Modify partial resource attributes.
    *   `DELETE`: Remove resources.
*   **Response Format**: Uniform JSON wrap containing `success`, `data`, `message`, and pagination metadata (`meta`).

---

## 3. Migration Naming & Structure Rules

All migrations must utilize standard timestamp order and target plural database tables:
*   Use `bigIncrements('id')` or `uuid('id')->primary()` for identifiers.
*   Ensure all foreign key constraints explicitly map on-delete options (`cascadeOnDelete()`, `nullOnDelete()`).
*   Define indexes on search and filter columns (e.g. `$table->index(['product_id', 'status'])`).

---

## 4. Git Commit & Branching Rules

### Conventional Commits Format
Every commit message must follow this layout:
```text
<type>(<scope>): <short description>
```

*   `feat`: A new feature (e.g. `feat(catalog): add meilisearch dynamic filters`)
*   `fix`: A bug fix (e.g. `fix(auth): resolve jwt token expiration check`)
*   `chore`: Tooling, build files, dependencies adjustments (e.g. `chore(deps): upgrade next.js version`)
*   `docs`: Documentation changes (e.g. `docs(standards): create coding standards guide`)
*   `refactor`: Code restructuring without function changes (e.g. `refactor(checkout): streamline tax calculations`)

### Branch Names
*   Features: `feature/<module>-<description>` (e.g. `feature/catalog-eav-system`)
*   Bug fixes: `bugfix/<module>-<description>` (e.g. `bugfix/auth-token-refresh`)
*   Hotfixes: `hotfix/<description>` (e.g. `hotfix/stripe-webhook-failure`)
